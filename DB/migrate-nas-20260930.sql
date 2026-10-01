-- Firepatch NAS-migratie voor MariaDB 10.
-- Niet-destructief en herhaalbaar: geen DROP, DELETE of TRUNCATE.
-- Maak voor de zekerheid eerst een export van database TLE-1.

SET NAMES utf8mb4;

-- Claims: behoud alle bestaande meldingen en voeg alleen de nieuwe velden toe.
ALTER TABLE `claims`
  ADD COLUMN IF NOT EXISTS `image_path` VARCHAR(2048) NULL AFTER `author_email`,
  ADD COLUMN IF NOT EXISTS `x_value` DOUBLE NULL AFTER `timestamp`,
  ADD COLUMN IF NOT EXISTS `y_value` DOUBLE NULL AFTER `x_value`;

-- Ook databases uit de recente teamdump moeten claims zonder sector accepteren.
ALTER TABLE `claims`
  MODIFY COLUMN `x_value` DOUBLE NULL,
  MODIFY COLUMN `y_value` DOUBLE NULL;

-- Sectornamen en coördinaten. Een bestaande state wordt bewust niet overschreven.
ALTER TABLE `map_sectors`
  ADD COLUMN IF NOT EXISTS `sector_name` VARCHAR(50) NOT NULL DEFAULT '' AFTER `sector_number`,
  ADD COLUMN IF NOT EXISTS `x_value` DECIMAL(8,6) UNSIGNED NOT NULL DEFAULT 0 AFTER `sector_name`,
  ADD COLUMN IF NOT EXISTS `y_value` DECIMAL(8,6) UNSIGNED NOT NULL DEFAULT 0 AFTER `x_value`,
  ADD COLUMN IF NOT EXISTS `state` VARCHAR(32) NOT NULL DEFAULT 'Healthy' AFTER `y_value`;

INSERT INTO `map_sectors` (`sector_number`, `sector_name`, `x_value`, `y_value`, `state`) VALUES
(1, 'De Vossenbramen', 0.166670, 0.166670, 'Healthy'),
(2, 'Het Marterstruweel', 0.333330, 0.166670, 'Healthy'),
(3, 'De Dassenkloof', 0.500000, 0.166670, 'Healthy'),
(4, 'De Adderheide', 0.666670, 0.166670, 'Healthy'),
(5, 'De Uilendennen', 0.833330, 0.166670, 'Healthy'),
(6, 'De Havikseiken', 1.000000, 0.166670, 'Healthy'),
(7, 'Het Spechtenhout', 0.166670, 0.333330, 'Healthy'),
(8, 'De IJsvogelkreek', 0.333330, 0.333330, 'Healthy'),
(9, 'De Everwortelpoel', 0.500000, 0.333330, 'Healthy'),
(10, 'De Hertenbeuken', 0.666670, 0.333330, 'Healthy'),
(11, 'De Reewilgen', 0.833330, 0.333330, 'Healthy'),
(12, 'De Egelkuil', 1.000000, 0.333330, 'Healthy'),
(13, 'De Wolvenrotsen', 0.166670, 0.500000, 'Healthy'),
(14, 'Het Berenmos', 0.333330, 0.500000, 'Healthy'),
(15, 'Het Beverriet', 0.500000, 0.500000, 'Healthy'),
(16, 'De Hazenheuvel', 0.666670, 0.500000, 'Healthy'),
(17, 'De Kraaienvallei', 0.833330, 0.500000, 'Healthy'),
(18, 'De Vleermuiskloof', 1.000000, 0.500000, 'Healthy'),
(19, 'De Eekhoornsparren', 0.166670, 0.666670, 'Healthy'),
(20, 'Het Salamanderven', 0.333330, 0.666670, 'Healthy'),
(21, 'De Buizerdkam', 0.500000, 0.666670, 'Healthy'),
(22, 'Het Kikkermoeras', 0.666670, 0.666670, 'Healthy'),
(23, 'De Nachtegaaldoorns', 0.833330, 0.666670, 'Healthy'),
(24, 'Het Ottermeer', 1.000000, 0.666670, 'Healthy'),
(25, 'De Valkenklif', 0.166670, 0.833330, 'Healthy'),
(26, 'De Slangenwortels', 0.333330, 0.833330, 'Healthy'),
(27, 'De Wezelkreek', 0.500000, 0.833330, 'Healthy'),
(28, 'Het Muizenkreupelhout', 0.666670, 0.833330, 'Healthy'),
(29, 'De Fazantenvarens', 0.833330, 0.833330, 'Healthy'),
(30, 'Het Hermelijnveen', 1.000000, 0.833330, 'Healthy'),
(31, 'De Paddenpoel', 0.166670, 1.000000, 'Healthy'),
(32, 'Het Lynxenravijn', 0.333330, 1.000000, 'Healthy'),
(33, 'De Bosuilholte', 0.500000, 1.000000, 'Healthy'),
(34, 'De Ravenkruinen', 0.666670, 1.000000, 'Healthy'),
(35, 'Het Roerdompmoeras', 0.833330, 1.000000, 'Healthy'),
(36, 'De Houtsnipbeek', 1.000000, 1.000000, 'Healthy')
ON DUPLICATE KEY UPDATE
  `sector_name` = VALUES(`sector_name`),
  `x_value` = VALUES(`x_value`),
  `y_value` = VALUES(`y_value`);

-- Missievelden die de huidige teamcode gebruikt.
ALTER TABLE `missions`
  ADD COLUMN IF NOT EXISTS `purpose_state` VARCHAR(255) NOT NULL DEFAULT '[]' AFTER `end-time`,
  ADD COLUMN IF NOT EXISTS `mission_state` VARCHAR(32) NOT NULL DEFAULT 'queued' AFTER `purpose_state`,
  ADD COLUMN IF NOT EXISTS `priority` VARCHAR(32) NOT NULL DEFAULT 'normal' AFTER `mission_state`,
  ADD COLUMN IF NOT EXISTS `state` VARCHAR(255) NULL AFTER `priority`;

-- Oude installaties hadden purpose_state onder de naam state. De kolom wordt
-- hierboven zo nodig toegevoegd, zodat deze kopie geen information_schema-
-- rechten nodig heeft en bestaande waarden behouden blijven.
UPDATE `missions`
SET `purpose_state` = `state`
WHERE `state` IS NOT NULL
  AND `state` <> ''
  AND (`purpose_state` IS NULL OR `purpose_state` = '' OR `purpose_state` = '[]');

-- Nieuwe logboekregels koppelen aan een missie; oude regels blijven intact.
ALTER TABLE `logboek`
  ADD COLUMN IF NOT EXISTS `mission_id` INT NULL AFTER `created_at`;

-- Controles zonder toegang tot information_schema. Iedere SHOW-opdracht hoort
-- exact één kolomregel terug te geven.
SHOW COLUMNS FROM `claims` LIKE 'x_value';
SHOW COLUMNS FROM `claims` LIKE 'y_value';
SHOW COLUMNS FROM `map_sectors` LIKE 'sector_name';
SHOW COLUMNS FROM `missions` LIKE 'mission_state';
SHOW COLUMNS FROM `logboek` LIKE 'mission_id';
