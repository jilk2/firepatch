-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 30 sep 2026 om 12:36
-- Serverversie: 8.4.2
-- PHP-versie: 8.4.14
--
-- MariaDB-compatibele snapshot voor een LEGE database.
-- NIET uitvoeren op de bestaande NAS-database. Gebruik daarvoor
-- DB/migrate-nas-20260930.sql; die behoudt bestaande tabellen en gegevens.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tle-1`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `accounts`
--

CREATE TABLE `accounts` (
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Claims` varchar(255) DEFAULT NULL,
  `Comments` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `accounts`
--

INSERT INTO `accounts` (`Email`, `Password`, `Claims`, `Comments`) VALUES
('admin@firepatch.nl', '$2y$12$FRI03DKaROXhpARbtKusPeQUxQWBvvfQ52/Zi.V0kuII/VcPg0ugO', NULL, NULL),
('email@gmail.com', '$2y$12$EQVZTsxkUuHfFEvAacvCZ.AwFVP9ROh14MNdItKutPvj/faLzTx/q', NULL, NULL),
('hier@iets.nl', '$2y$12$EaOWIodK2Oc3K3QuBZ4p0O5zDdpMH/3Yljtl48fj76d.xVWd7C0IC', NULL, NULL),
('hier2@iets.nl', '$2y$12$9rd04vRWrLIxr4aQeRXf/uls.TJ3YICy9yOF40TeV7pYBtLtdQG4y', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `claims`
--

CREATE TABLE `claims` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text,
  `image_path` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_email` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `x_value` double DEFAULT NULL,
  `y_value` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `community_notes`
--

CREATE TABLE `community_notes` (
  `id` int NOT NULL,
  `claim_id` int NOT NULL,
  `author_email` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `source` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `favorites`
--

CREATE TABLE `favorites` (
  `id` int NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `claim_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `logboek`
--

CREATE TABLE `logboek` (
  `id` int NOT NULL,
  `activity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('done','pending','active') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'done',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mission_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `logboek`
--

INSERT INTO `logboek` (`id`, `activity`, `location`, `status`, `created_at`, `mission_id`) VALUES
(119, 'Planten scannen', 'Section 1', 'pending', '2026-09-30 14:24:32', 83),
(120, 'Grondvruchtbaarheid meten', 'Section 1', 'pending', '2026-09-30 14:24:32', 83),
(121, 'Dieren monitoren', 'Section 1', 'pending', '2026-09-30 14:24:32', 83),
(122, 'Grond bemesten', 'Section 1', 'pending', '2026-09-30 14:24:32', 83),
(125, 'Brand blussen', 'Section 34', 'active', '2026-09-30 14:31:57', 85),
(126, 'Brand blussen', 'Section 34', 'active', '2026-09-30 14:31:57', 85);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `map_sectors`
--

CREATE TABLE `map_sectors` (
  `sector_number` tinyint UNSIGNED NOT NULL,
  `sector_name` varchar(50) NOT NULL,
  `x_value` decimal(8,6) UNSIGNED NOT NULL,
  `y_value` decimal(8,6) UNSIGNED NOT NULL,
  `state` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `map_sectors`
--

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
(36, 'De Houtsnipbeek', 1.000000, 1.000000, 'Healthy');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `missions`
--

CREATE TABLE `missions` (
  `id` int NOT NULL,
  `area` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kralingse Bos',
  `purpose` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `interventions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start-time` datetime NOT NULL,
  `end-time` datetime NOT NULL,
  `purpose_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Gepland',
  `mission_state` varchar(15) NOT NULL DEFAULT 'queued',
  `priority` varchar(15) NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Gegevens worden geëxporteerd voor tabel `missions`
--

INSERT INTO `missions` (`id`, `area`, `purpose`, `interventions`, `start-time`, `end-time`, `purpose_state`, `mission_state`, `priority`) VALUES
(83, 'Section 1', '[\"Planten scannen\",\"Grondvruchtbaarheid meten\",\"Dieren monitoren\"]', '[\"Grond bemesten\"]', '2026-09-30 15:15:00', '2026-10-01 00:00:00', '[\"Gepland\",\"Gepland\",\"Gepland\"]', 'queued', 'normal'),
(85, 'Section 34', '[\"Brand blussen\"]', '[\"Brand blussen\"]', '2026-09-30 14:30:00', '2026-09-30 16:30:00', '[\"Actief\"]', 'active', 'high');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`Email`);

--
-- Indexen voor tabel `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `community_notes`
--
ALTER TABLE `community_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claim_id` (`claim_id`);

--
-- Indexen voor tabel `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_claim_unique` (`user_email`,`claim_id`),
  ADD KEY `claim_id` (`claim_id`);

--
-- Indexen voor tabel `logboek`
--
ALTER TABLE `logboek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexen voor tabel `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT voor een tabel `community_notes`
--
ALTER TABLE `community_notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT voor een tabel `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `logboek`
--
ALTER TABLE `logboek`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT voor een tabel `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `community_notes`
--
ALTER TABLE `community_notes`
  ADD CONSTRAINT `community_notes_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
