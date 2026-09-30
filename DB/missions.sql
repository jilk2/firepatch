-- Firepatch missie-migratie voor MariaDB.
-- Niet-destructief: bestaande tabellen en overige missies blijven behouden.

CREATE TABLE IF NOT EXISTS `missions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `area` VARCHAR(255) NOT NULL DEFAULT 'Kralingse Bos',
  `purpose` LONGTEXT NOT NULL,
  `interventions` LONGTEXT NOT NULL,
  `start-time` DATETIME NOT NULL,
  `end-time` DATETIME NOT NULL,
  `purpose_state` VARCHAR(255) NOT NULL DEFAULT '[]',
  `mission_state` VARCHAR(32) NOT NULL DEFAULT 'queued',
  `priority` VARCHAR(32) NOT NULL DEFAULT 'normal',
  PRIMARY KEY (`id`),
  KEY `idx_missions_start_time` (`start-time`),
  KEY `idx_missions_queue` (`mission_state`, `priority`, `start-time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `missions`
  ADD COLUMN IF NOT EXISTS `purpose_state` VARCHAR(255) NOT NULL DEFAULT '[]' AFTER `end-time`,
  ADD COLUMN IF NOT EXISTS `mission_state` VARCHAR(32) NOT NULL DEFAULT 'queued' AFTER `purpose_state`,
  ADD COLUMN IF NOT EXISTS `priority` VARCHAR(32) NOT NULL DEFAULT 'normal' AFTER `mission_state`;

INSERT INTO `missions`
  (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `purpose_state`, `mission_state`, `priority`)
VALUES
  (36, 'Kralingse Bos',
   '["Planten scannen","Grondvruchtbaarheid meten","Dieren monitoren","Lichtlevels controleren"]',
   '["Water geven aan planten","Invasieve dierensoorten verwijderen"]',
  '2026-09-28 12:00:00', '2026-09-28 16:00:00',
  '["Gepland","Gepland","Gepland","Gepland"]', 'queued', 'normal')
ON DUPLICATE KEY UPDATE
  `area` = VALUES(`area`),
  `purpose` = VALUES(`purpose`),
  `interventions` = VALUES(`interventions`),
  `start-time` = VALUES(`start-time`),
  `end-time` = VALUES(`end-time`),
  `purpose_state` = VALUES(`purpose_state`),
  `mission_state` = VALUES(`mission_state`),
  `priority` = VALUES(`priority`);
