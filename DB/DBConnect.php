<?php

declare(strict_types=1);

/**
 * Centrale databaseverbinding voor Firepatch.
 *
 * Gebruik firepatchPdo() voor PDO en firepatchMysqli() voor MySQLi.
 * Servergebonden waarden staan in config/database.local.php en worden niet
 * in Git opgenomen.
 */
function firepatchDatabaseConfig(): array
{
    static $config = null;

    if (is_array($config)) {
        return $config;
    }

    $config = [
        'host' => getenv('FIREPATCH_DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('FIREPATCH_DB_PORT') ?: 3306),
        'name' => getenv('FIREPATCH_DB_NAME') ?: 'TLE-1',
        'user' => getenv('FIREPATCH_DB_USER') ?: 'root',
        'password' => getenv('FIREPATCH_DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ];

    $localConfigFile = __DIR__ . '/../config/database.local.php';

    if (is_file($localConfigFile)) {
        $localConfig = require $localConfigFile;

        if (!is_array($localConfig)) {
            throw new RuntimeException('config/database.local.php moet een PHP-array teruggeven.');
        }

        $config = array_merge($config, $localConfig);
    }

    foreach (['host', 'name', 'user', 'password', 'charset', 'port'] as $requiredKey) {
        if (!array_key_exists($requiredKey, $config)) {
            throw new RuntimeException("Databaseconfiguratie mist de waarde {$requiredKey}.");
        }
    }

    $config['port'] = (int) $config['port'];

    if ($config['host'] === '' || $config['name'] === '' || $config['user'] === '') {
        throw new RuntimeException('Host, databasenaam en databasegebruiker mogen niet leeg zijn.');
    }

    if ($config['port'] < 1 || $config['port'] > 65535) {
        throw new RuntimeException('De databasepoort is ongeldig.');
    }

    return $config;
}

/**
 * Synology gebruikt voor MariaDB 10 meestal poort 3307, terwijl lokale
 * installaties vaak 3306 gebruiken. Probeer voor een lokale database beide
 * adressen en poorten, met de ingestelde combinatie altijd als eerste.
 */
function firepatchConnectionCandidates(array $config): array
{
    $configuredHost = (string) $config['host'];
    $configuredPort = (int) $config['port'];
    $candidates = [[$configuredHost, $configuredPort]];

    if (in_array($configuredHost, ['127.0.0.1', 'localhost'], true)) {
        $candidates = array_merge($candidates, [
            ['127.0.0.1', 3307],
            ['localhost', 3307],
            ['127.0.0.1', 3306],
            ['localhost', 3306],
        ]);
    }

    $unique = [];
    foreach ($candidates as [$host, $port]) {
        $unique[$host . ':' . $port] = [$host, $port];
    }

    return array_values($unique);
}

/**
 * De eerste Firepatch-database gebruikte Title en Status met hoofdletters.
 * Nieuwe code gebruikt title en status. Houd beide databases zonder migratie
 * compatibel door claimresultaten direct na het ophalen te normaliseren.
 */
function firepatchNormalizeClaimRow(array $claim): array
{
    if (!array_key_exists('title', $claim) && array_key_exists('Title', $claim)) {
        $claim['title'] = $claim['Title'];
    }

    if (!array_key_exists('status', $claim) && array_key_exists('Status', $claim)) {
        $claim['status'] = $claim['Status'];
    }

    return $claim;
}

/**
 * Bepaal de sector uit genormaliseerde kaartcoordinaten. Oude claims hadden
 * nog geen coordinaten; probeer daarbij "Sector 8" uit source te herkennen.
 */
function firepatchClaimSectorNumber(array $claim): ?int
{
    $x = $claim['x_value'] ?? null;
    $y = $claim['y_value'] ?? null;

    if (is_numeric($x) && is_numeric($y)) {
        $x = (float) $x;
        $y = (float) $y;

        if ($x >= 0.0 && $x <= 1.0 && $y >= 0.0 && $y <= 1.0) {
            $column = min(5, max(0, (int) floor($x * 6)));
            $row = min(5, max(0, (int) floor($y * 6)));

            return ($row * 6) + $column + 1;
        }
    }

    $source = (string) ($claim['source'] ?? '');
    if (preg_match('/\bsector\s*([1-9]|[12][0-9]|3[0-6])\b/i', $source, $matches) === 1) {
        return (int) $matches[1];
    }

    return null;
}

function firepatchPdo(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    if (!extension_loaded('pdo_mysql')) {
        throw new RuntimeException('De PHP-extensie pdo_mysql is niet ingeschakeld.');
    }

    $config = firepatchDatabaseConfig();
    $lastException = null;

    foreach (firepatchConnectionCandidates($config) as [$host, $port]) {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $host,
            $port,
            $config['name'],
            $config['charset']
        );

        try {
            $connection = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 3,
            ]);

            return $connection;
        } catch (PDOException $exception) {
            $lastException = $exception;
        }
    }

    throw $lastException ?? new RuntimeException('Kon geen databaseverbinding maken.');
}

function firepatchMysqli(): mysqli
{
    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    if (!extension_loaded('mysqli')) {
        throw new RuntimeException('De PHP-extensie mysqli is niet ingeschakeld.');
    }

    $config = firepatchDatabaseConfig();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $lastException = null;

    foreach (firepatchConnectionCandidates($config) as [$host, $port]) {
        $candidate = mysqli_init();
        $candidate->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);

        try {
            $candidate->real_connect(
                $host,
                $config['user'],
                $config['password'],
                $config['name'],
                $port
            );
            $candidate->set_charset($config['charset']);
            $connection = $candidate;

            return $connection;
        } catch (mysqli_sql_exception $exception) {
            $lastException = $exception;
            $candidate->close();
        }
    }

    throw $lastException ?? new RuntimeException('Kon geen databaseverbinding maken.');
}
