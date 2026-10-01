# Firepatch team-main

Dit is de gedeelde ontwikkelversie van Firepatch van 28 september 2026.
De map bevat geen NAS-wachtwoorden, productieconfiguratie of tijdelijke
databasebeheerpagina's.

## Lokale installatie

1. Gebruik PHP 8.2 of nieuwer met `pdo_mysql`, `mysqli` en `fileinfo`.
2. Start MySQL of MariaDB.
3. Maak een database aan met exact de naam `TLE-1` en collatie
   `utf8mb4_unicode_ci`.
4. Selecteer een lege database `TLE-1` en importeer
   `DB/Database-COMPLETE.sql`. Deze snapshot is MariaDB-compatibel en is alleen
   bedoeld voor een nieuwe, lege database.
5. Kopieer `config/database.local.example.php` naar
   `config/database.local.php`.
6. Vul in `config/database.local.php` de eigen lokale databasegebruiker en het
   eigen lokale wachtwoord in.
7. Open `index.php` via de lokale PHP-webserver.

Voor bewijsafbeeldingen moet de map `uploads` schrijfbaar zijn voor de lokale
PHP-webserver. Op Linux/macOS kan dit vanuit de projectmap met
`chmod 775 uploads`. Op Windows moet het account waaronder Apache/PHP draait
schrijfrechten hebben op de map `uploads`; plaats het project niet in een
beveiligde systeemmap zoals `C:\\Program Files`.

## Database-afspraken

`DB/DBConnect.php` is de enige source of truth voor databaseconfiguratie en
verbindingen. Gebruik in pagina's één van de bestaande wrappers:

- `database.php` voor PDO via `$pdo`;
- `config/database.php` voor MySQLi via `$db`.

Commit `config/database.local.php` nooit. Dit bestand staat in `.gitignore`.
`DB/Database-COMPLETE.sql` is de volledige snapshot voor een lege lokale
database. `DB/migrate-nas-20260930.sql` is de enige juiste update voor een al
bestaande NAS-database: die verwijdert geen tabellen of records en kan opnieuw
worden uitgevoerd. De oudere dumps blijven alleen als historische referentie
aanwezig.

## Productie

Maak vóór iedere NAS-update een database-export. Voer daarna eenmalig
`DB/migrate-nas-20260930.sql` uit op de bestaande database `TLE-1`. Upload
vervolgens uitsluitend de inhoud van de genegeerde map `nas-upload`. Deze map
bevat wel het noodzakelijke `DB/DBConnect.php`, maar geen SQL-dumps, `.git`,
`.idea` of Node-bestanden. Bewaar altijd de bestaande
`config/database.local.php` en de bestaande inhoud van `uploads/`. De map
`uploads` moet schrijfbaar blijven voor PHP.

## Unreal claim-API

Gebruik bij voorkeur `POST /api/create_claim.php`. Voor compatibiliteit werkt
ook `POST /create_claim.php`.

Headers:

```text
Content-Type: application/json
```

JSON-body:

```json
{
  "title": "Brand gedetecteerd",
  "description": "Brand gezien door Unreal",
  "source": "Unreal Engine",
  "sector": 8,
  "author_email": "unreal@firepatch.nl",
  "image_mime": "image/png",
  "image_base64": "iVBORw0KGgoAAA..."
}
```

`title` en `author_email` zijn verplicht. `description`, `source`, `sector`,
`image_mime` en `image_base64` zijn optioneel. Een sector moet tussen 1 en 36
liggen. De API maakt altijd een claim met status `pending`. Afbeeldingen mogen
JPEG, PNG of WebP zijn en maximaal 5 MB na Base64-decodering.

## Unreal claim oplossen

Stuur na het afronden van de droneactie een `POST` naar
`/solve_claim.php` met alleen het sectornummer:

```json
{
  "sector": 8
}
```

Gebruik de header `Content-Type: application/json`. `sector` moet tussen
1 en 36 liggen. Alle nog openstaande claims in die sector krijgen de status
`resolved`; de response bevat `resolved_count` en de bijgewerkte `claim_ids`.
