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
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['name'],
        $config['charset']
    );

    $connection = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $connection;
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

    $connection = mysqli_connect(
        $config['host'],
        $config['user'],
        $config['password'],
        $config['name'],
        $config['port']
    );
    mysqli_set_charset($connection, $config['charset']);

    return $connection;
}
