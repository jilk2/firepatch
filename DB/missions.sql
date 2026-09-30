-- Firepatch missie-migratie voor MariaDB.
-- Niet-destructief: bestaande tabellen en overige missies blijven behouden.

CREATE TABLE IF NOT EXISTS `missions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `area` VARCHAR(255) NOT NULL DEFAULT 'Kralingse Bos',
  `purpose` LONGTEXT NOT NULL,
  `interventions` LONGTEXT NOT NULL,
  `start-time` DATETIME NOT NULL,
  `end-time` DATETIME NOT NULL,
  `state` VARCHAR(255) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`id`),
  KEY `idx_missions_start_time` (`start-time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `missions`
  ADD COLUMN IF NOT EXISTS `state` VARCHAR(255) NOT NULL DEFAULT '[]' AFTER `end-time`;

INSERT INTO `missions`
  (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`)
VALUES
  (36, 'Kralingse Bos',
   '["Planten scannen","Grondvruchtbaarheid meten","Dieren monitoren","Lichtlevels controleren"]',
   '["Water geven aan planten","Invasieve dierensoorten verwijderen"]',
   '2026-09-28 12:00:00', '2026-09-28 16:00:00',
   '["Gepland","Gepland","Gepland","Gepland"]')
ON DUPLICATE KEY UPDATE
  `area` = VALUES(`area`),
  `purpose` = VALUES(`purpose`),
  `interventions` = VALUES(`interventions`),
  `start-time` = VALUES(`start-time`),
  `end-time` = VALUES(`end-time`),
  `state` = VALUES(`state`);
