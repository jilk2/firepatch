-- Firepatch MariaDB-export
-- Database: TLE-1
-- Gegenereerd: 2026-09-28T14:03:28+02:00

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Tabel `accounts`

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Claims` varchar(255) DEFAULT NULL,
  `Comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accounts` (`Email`, `Password`, `Claims`, `Comments`) VALUES ('hier@iets.nl', '$2y$12$EaOWIodK2Oc3K3QuBZ4p0O5zDdpMH/3Yljtl48fj76d.xVWd7C0IC', NULL, NULL);
INSERT INTO `accounts` (`Email`, `Password`, `Claims`, `Comments`) VALUES ('hier2@iets.nl', '$2y$12$9rd04vRWrLIxr4aQeRXf/uls.TJ3YICy9yOF40TeV7pYBtLtdQG4y', NULL, NULL);
INSERT INTO `accounts` (`Email`, `Password`, `Claims`, `Comments`) VALUES ('joeypmalta@gmail.com', '$2y$10$aojZ/n4SdwD6jeNpHdVYXe4AFO.WEhSVa4Zpa14FXsuY8x9Scuqxi', NULL, NULL);

-- --------------------------------------------------------
-- Tabel `claims`

DROP TABLE IF EXISTS `claims`;
CREATE TABLE `claims` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `Status` varchar(50) NOT NULL,
  `author_email` varchar(255) NOT NULL,
  `image_path` varchar(2048) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `claims` (`id`, `Title`, `description`, `source`, `Status`, `author_email`, `image_path`, `timestamp`) VALUES ('1', 'hiero', NULL, NULL, 'pending', 'hier@iets.nl', NULL, '2026-09-22 13:24:14');
INSERT INTO `claims` (`id`, `Title`, `description`, `source`, `Status`, `author_email`, `image_path`, `timestamp`) VALUES ('2', 'wawd', NULL, NULL, 'pending', 'hier@iets.nl', NULL, '2026-09-22 13:27:15');
INSERT INTO `claims` (`id`, `Title`, `description`, `source`, `Status`, `author_email`, `image_path`, `timestamp`) VALUES ('3', 'testing 2', 'wasdwasdwa', 'wadw', 'pending', 'hier@iets.nl', NULL, '2026-09-22 13:31:43');
INSERT INTO `claims` (`id`, `Title`, `description`, `source`, `Status`, `author_email`, `image_path`, `timestamp`) VALUES ('4', 'test 3', 'niets', 'iets', 'true', 'hier@iets.nl', NULL, '2026-09-22 13:32:38');
INSERT INTO `claims` (`id`, `Title`, `description`, `source`, `Status`, `author_email`, `image_path`, `timestamp`) VALUES ('5', 'peo', 'dwasdawsdwa', 'wadwas', 'false', 'hier@iets.nl', NULL, '2026-09-22 13:35:31');
INSERT INTO `claims` (`id`, `Title`, `description`, `source`, `Status`, `author_email`, `image_path`, `timestamp`) VALUES ('6', 'DITISEENTEST', 'DITISEENTESTDITISEENTESTDITISEENTESTDITISEENTEST', 'ewaja (Sector 8)', 'true', 'joeypmalta@gmail.com', NULL, '2026-09-28 13:56:56');

-- --------------------------------------------------------
-- Tabel `community_notes`

DROP TABLE IF EXISTS `community_notes`;
CREATE TABLE `community_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `claim_id` int(11) NOT NULL,
  `author_email` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `claim_id` (`claim_id`),
  CONSTRAINT `fk_firepatch_notes_claim` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('1', '5', 'hier@iets.nl', 'wasdwa', 'sdwasdwasdwa', '2026-09-22 13:37:48');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('2', '5', 'hier@iets.nl', 'dit klopt', 'huier', '2026-09-22 13:41:32');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('3', '5', 'hier@iets.nl', 'dit is zeker waar', 'trust', '2026-09-22 13:41:43');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('4', '5', 'hier@iets.nl', 'dit is correct', 'really', '2026-09-22 13:41:54');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('5', '5', 'hier@iets.nl', 'dit klopt niet', 'Geen bron opgegeven', '2026-09-22 13:42:01');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('6', '5', 'hier@iets.nl', 'dit is incorrect', 'Geen bron opgegeven', '2026-09-22 13:42:09');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('7', '5', 'hier@iets.nl', 'dit is compleet niet waar', 'Geen bron opgegeven', '2026-09-22 13:42:24');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('8', '5', 'hier@iets.nl', 'dit is helemaar raar, klopt niet', 'Geen bron opgegeven', '2026-09-22 13:42:35');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('9', '5', 'hier@iets.nl', 'dit is niet waar hoe kom je hier op', 'Geen bron opgegeven', '2026-09-22 13:45:41');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('10', '4', 'hier@iets.nl', 'waar', 'wadwa', '2026-09-22 14:23:26');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('11', '4', 'hier@iets.nl', 'woasfwd klopt', 'asdwa', '2026-09-22 14:23:33');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('12', '4', 'hier@iets.nl', 'incorrect', 'Geen bron opgegeven', '2026-09-22 14:23:39');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('13', '4', 'hier@iets.nl', 'correct', 'Geen bron opgegeven', '2026-09-22 14:23:50');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('14', '6', 'joeypmalta@gmail.com', 'dit is echt trust', 'Geen bron opgegeven', '2026-09-28 13:57:11');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('15', '6', 'joeypmalta@gmail.com', 'waar', 'Geen bron opgegeven', '2026-09-28 13:58:22');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('16', '6', 'joeypmalta@gmail.com', 'waar', 'Geen bron opgegeven', '2026-09-28 13:58:25');
INSERT INTO `community_notes` (`id`, `claim_id`, `author_email`, `text`, `source`, `timestamp`) VALUES ('17', '6', 'joeypmalta@gmail.com', 'waar', 'Geen bron opgegeven', '2026-09-28 13:58:27');

-- --------------------------------------------------------
-- Tabel `favorites`

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(255) NOT NULL,
  `claim_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_claim_unique` (`user_email`,`claim_id`),
  KEY `claim_id` (`claim_id`),
  CONSTRAINT `fk_firepatch_favorites_claim` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- Tabel `logboek`

DROP TABLE IF EXISTS `logboek`;
CREATE TABLE `logboek` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `status` enum('done','pending','active') NOT NULL DEFAULT 'done',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('1', 'interventie', 'Bewateringsactie afgerond', 'Sector 04', 'done', '2026-09-21 14:26:00');
INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('2', 'drone', 'Deployment gestart', 'Sector 04', 'done', '2026-09-21 14:20:00');
INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('3', 'interventie', 'Interventieverzoek gegenereerd', 'Sector 04', 'pending', '2026-09-21 14:15:00');
INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('4', 'scan', 'Sensorpiek gedetecteerd', 'Sector 04', 'done', '2026-09-21 14:14:00');
INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('5', 'drone', 'Patrouillepad aangepast', 'Sector 04', 'active', '2026-09-21 13:52:00');
INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('6', 'Water geven aan planten, Grond bemesten', 'Planten scannen, Grondvruchtbaarheid meten', 'Kralingse Bos', 'active', '2026-09-28 10:53:55');
INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('7', 'Planten scannen', 'Water geven aan planten', 'Kralingse Bos', 'active', '2026-09-28 11:07:07');
INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('8', 'Water geven aan planten', 'Planten scannen, Grondvruchtbaarheid meten', 'Kralingse Bos', 'active', '2026-09-28 13:56:04');
INSERT INTO `logboek` (`id`, `type`, `activity`, `location`, `status`, `created_at`) VALUES ('9', 'Water geven aan planten', 'Planten scannen, Grondvruchtbaarheid meten', 'Kralingse Bos', 'active', '2026-09-28 13:57:50');

-- --------------------------------------------------------
-- Tabel `map_sectors`

DROP TABLE IF EXISTS `map_sectors`;
CREATE TABLE `map_sectors` (
  `sector_number` tinyint(3) unsigned NOT NULL,
  `x_value` decimal(8,6) unsigned NOT NULL,
  `y_value` decimal(8,6) unsigned NOT NULL,
  `state` varchar(32) NOT NULL,
  PRIMARY KEY (`sector_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('1', '0.166670', '0.166670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('2', '0.333330', '0.166670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('3', '0.500000', '0.166670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('4', '0.666670', '0.166670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('5', '0.833330', '0.166670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('6', '1.000000', '0.166670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('7', '0.166670', '0.333330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('8', '0.333330', '0.333330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('9', '0.500000', '0.333330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('10', '0.666670', '0.333330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('11', '0.833330', '0.333330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('12', '1.000000', '0.333330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('13', '0.166670', '0.500000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('14', '0.333330', '0.500000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('15', '0.500000', '0.500000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('16', '0.666670', '0.500000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('17', '0.833330', '0.500000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('18', '1.000000', '0.500000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('19', '0.166670', '0.666670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('20', '0.333330', '0.666670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('21', '0.500000', '0.666670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('22', '0.666670', '0.666670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('23', '0.833330', '0.666670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('24', '1.000000', '0.666670', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('25', '0.166670', '0.833330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('26', '0.333330', '0.833330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('27', '0.500000', '0.833330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('28', '0.666670', '0.833330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('29', '0.833330', '0.833330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('30', '1.000000', '0.833330', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('31', '0.166670', '1.000000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('32', '0.333330', '1.000000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('33', '0.500000', '1.000000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('34', '0.666670', '1.000000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('35', '0.833330', '1.000000', 'Healthy');
INSERT INTO `map_sectors` (`sector_number`, `x_value`, `y_value`, `state`) VALUES ('36', '1.000000', '1.000000', 'Healthy');

-- --------------------------------------------------------
-- Tabel `missions`

DROP TABLE IF EXISTS `missions`;
CREATE TABLE `missions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area` varchar(30) NOT NULL DEFAULT 'Kralingse Bos',
  `purpose` varchar(255) NOT NULL,
  `interventions` varchar(255) NOT NULL,
  `start-time` datetime NOT NULL,
  `end-time` datetime NOT NULL,
  `state` varchar(255) NOT NULL DEFAULT '[]',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('1', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-23 06:00:00', '2026-09-23 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('2', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\",\"Dieren monitoren\",\"Lichtlevels controleren\"]', '[\"Water geven aan planten\",\"Grond bemesten\",\"Invasieve dierensoorten verwijderen\",\"Onkruid verwijderen\"]', '2026-09-23 06:00:00', '2026-09-23 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('4', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\",\"Grond bemesten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('5', 'Kralingse Bos', '[\"Water geven aan planten\"]', '[\"Planten scannen\"]', '2026-09-28 04:45:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('6', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('7', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('8', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('9', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('10', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('11', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('12', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 13:45:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('13', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('36', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\",\"Dieren monitoren\",\"Lichtlevels controleren\"]', '[\"Water geven aan planten\",\"Invasieve dierensoorten verwijderen\"]', '2026-09-28 12:00:00', '2026-09-28 16:00:00', '[\"Gepland\",\"Gepland\",\"Gepland\",\"Gepland\"]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('37', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[\"Gepland\",\"Gepland\"]');
INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `state`) VALUES ('38', 'Kralingse Bos', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\"]', '[\"Water geven aan planten\"]', '2026-09-28 06:00:00', '2026-09-28 18:00:00', '[\"Gepland\",\"Gepland\"]');

SET FOREIGN_KEY_CHECKS = 1;
