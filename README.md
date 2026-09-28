# Firepatch team-main

Dit is de gedeelde ontwikkelversie van Firepatch van 28 september 2026.
De map bevat geen NAS-wachtwoorden, productieconfiguratie of tijdelijke
databasebeheerpagina's.

## Lokale installatie

1. Gebruik PHP 8.2 of nieuwer met `pdo_mysql`, `mysqli` en `fileinfo`.
2. Start MySQL of MariaDB.
3. Maak een database aan met exact de naam `TLE-1` en collatie
   `utf8mb4_unicode_ci`.
4. Selecteer `TLE-1` en importeer `DB/TLE-1-20260928-140328.sql`.
   Let op: deze export bevat `DROP TABLE IF EXISTS` en vervangt dus bestaande
   tabellen in de geselecteerde database.
5. Kopieer `config/database.local.example.php` naar
   `config/database.local.php`.
6. Vul in `config/database.local.php` de eigen lokale databasegebruiker en het
   eigen lokale wachtwoord in.
7. Open `index.php` via de lokale PHP-webserver.

## Database-afspraken

`DB/DBConnect.php` is de enige source of truth voor databaseconfiguratie en
verbindingen. Gebruik in pagina's één van de bestaande wrappers:

- `database.php` voor PDO via `$pdo`;
- `config/database.php` voor MySQLi via `$db`.

Commit `config/database.local.php` nooit. Dit bestand staat in `.gitignore`.
De losse bestanden `DB/missions.sql` en `DB/map_sectors.sql` documenteren de
laatste niet-destructieve schema-updates; de volledige actuele databasesnapshot
staat in `DB/TLE-1-20260928-140328.sql`.

## Productie

Deze map is voor ontwikkeling. Gebruik voor een NAS-deployment de afzonderlijk
opgeleverde `nas-upload`-map en bewaar daar de bestaande
`config/database.local.php`.
