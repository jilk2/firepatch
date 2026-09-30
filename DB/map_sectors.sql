-- MariaDB-compatibele, niet-destructieve versie van map_sectors.sql.

CREATE TABLE IF NOT EXISTS `map_sectors` (
  `sector_number` TINYINT UNSIGNED NOT NULL,
  `x_value` DECIMAL(8,6) UNSIGNED NOT NULL,
  `y_value` DECIMAL(8,6) UNSIGNED NOT NULL,
  `state` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`sector_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `map_sectors`
  ADD COLUMN IF NOT EXISTS `x_value` DECIMAL(8,6) UNSIGNED NOT NULL DEFAULT 0 AFTER `sector_number`,
  ADD COLUMN IF NOT EXISTS `y_value` DECIMAL(8,6) UNSIGNED NOT NULL DEFAULT 0 AFTER `x_value`,
  ADD COLUMN IF NOT EXISTS `state` VARCHAR(32) NOT NULL DEFAULT 'Healthy' AFTER `y_value`;

INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES
(1, 0.166670, 0.166670, 'Healthy'),
(2, 0.333330, 0.166670, 'Healthy'),
(3, 0.500000, 0.166670, 'Healthy'),
(4, 0.666670, 0.166670, 'Healthy'),
(5, 0.833330, 0.166670, 'Healthy'),
(6, 1.000000, 0.166670, 'Healthy'),
(7, 0.166670, 0.333330, 'Healthy'),
(8, 0.333330, 0.333330, 'Healthy'),
(9, 0.500000, 0.333330, 'Healthy'),
(10, 0.666670, 0.333330, 'Healthy'),
(11, 0.833330, 0.333330, 'Healthy'),
(12, 1.000000, 0.333330, 'Healthy'),
(13, 0.166670, 0.500000, 'Healthy'),
(14, 0.333330, 0.500000, 'Healthy'),
(15, 0.500000, 0.500000, 'Healthy'),
(16, 0.666670, 0.500000, 'Healthy'),
(17, 0.833330, 0.500000, 'Healthy'),
(18, 1.000000, 0.500000, 'Healthy'),
(19, 0.166670, 0.666670, 'Healthy'),
(20, 0.333330, 0.666670, 'Healthy'),
(21, 0.500000, 0.666670, 'Healthy'),
(22, 0.666670, 0.666670, 'Healthy'),
(23, 0.833330, 0.666670, 'Healthy'),
(24, 1.000000, 0.666670, 'Healthy'),
(25, 0.166670, 0.833330, 'Healthy'),
(26, 0.333330, 0.833330, 'Healthy'),
(27, 0.500000, 0.833330, 'Healthy'),
(28, 0.666670, 0.833330, 'Healthy'),
(29, 0.833330, 0.833330, 'Healthy'),
(30, 1.000000, 0.833330, 'Healthy'),
(31, 0.166670, 1.000000, 'Healthy'),
(32, 0.333330, 1.000000, 'Healthy'),
(33, 0.500000, 1.000000, 'Healthy'),
(34, 0.666670, 1.000000, 'Healthy'),
(35, 0.833330, 1.000000, 'Healthy'),
(36, 1.000000, 1.000000, 'Healthy')
ON DUPLICATE KEY UPDATE
  `x_value` = VALUES(`x_value`),
  `y_value` = VALUES(`y_value`),
  `state` = VALUES(`state`);
