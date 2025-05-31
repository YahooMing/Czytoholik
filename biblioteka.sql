-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2025-05-23 21:19:12
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biblioteka`
--

CREATE DATABASE IF NOT EXISTS `biblioteka`;
USE `biblioteka`;

-- --------------------------------------------------------

-- Table structure for table `czytelnicy`
--

CREATE TABLE `czytelnicy` (
  `id_czytelnika` int(11) NOT NULL AUTO_INCREMENT,
  `imie` text NOT NULL,
  `nazwisko` text NOT NULL,
  `adres` text NOT NULL,
  `telefon` VARCHAR(20) NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY (`id_czytelnika`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `ksiazki`
--

CREATE TABLE `ksiazki` (
  `id_ksiazki` int(11) NOT NULL AUTO_INCREMENT,
  `tytul` text NOT NULL,
  `autor` text NOT NULL,
  `wydawnictwo` text NOT NULL,
  `rok_wydania` int(11) NOT NULL,
  `kategoria` text NOT NULL,
  PRIMARY KEY (`id_ksiazki`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `egzemplarze`
--

CREATE TABLE `egzemplarze` (
  `id_egzemplarz` int(11) NOT NULL AUTO_INCREMENT,
  `k_id` int(11) NOT NULL,
  `stan` text NOT NULL,
  `egzemplarz` text NOT NULL,
  PRIMARY KEY (`id_egzemplarz`),
  KEY `k_id` (`k_id`),
  CONSTRAINT `egzemplarze_ibfk_1` FOREIGN KEY (`k_id`) REFERENCES `ksiazki` (`id_ksiazki`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `rezerwacje`
--

CREATE TABLE `rezerwacje` (
  `id_rezerwacji` int(11) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `data_rezerwacji` date NOT NULL,
  PRIMARY KEY (`id_rezerwacji`),
  KEY `e_id` (`e_id`),
  KEY `c_id` (`c_id`),
  CONSTRAINT `rezerwacje_ibfk_1` FOREIGN KEY (`e_id`) REFERENCES `egzemplarze` (`id_egzemplarz`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `rezerwacje_ibfk_2` FOREIGN KEY (`c_id`) REFERENCES `czytelnicy` (`id_czytelnika`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `wypozyczenia`
--

CREATE TABLE `wypozyczenia` (
  `id_wypozyczenia` int(11) NOT NULL AUTO_INCREMENT,
  `e_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `data_wypozyczenia` date NOT NULL,
  `przewidywana_data_zwrotu` date NOT NULL,
  `data_zwrotu` date DEFAULT NULL,
  PRIMARY KEY (`id_wypozyczenia`),
  KEY `e_id` (`e_id`),
  KEY `c_id` (`c_id`),
  CONSTRAINT `wypozyczenia_ibfk_1` FOREIGN KEY (`c_id`) REFERENCES `czytelnicy` (`id_czytelnika`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `wypozyczenia_ibfk_2` FOREIGN KEY (`e_id`) REFERENCES `egzemplarze` (`id_egzemplarz`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Table structure for table `kary`
--

CREATE TABLE `kary` (
  `id_kary` int(11) NOT NULL AUTO_INCREMENT,
  `w_id` int(11) NOT NULL,
  `kwota_kary` int(11) NOT NULL,
  `status_kary` text NOT NULL,
  PRIMARY KEY (`id_kary`),
  KEY `w_id` (`w_id`),
  CONSTRAINT `kary_ibfk_1` FOREIGN KEY (`w_id`) REFERENCES `wypozyczenia` (`id_wypozyczenia`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- losowe rekordy

INSERT INTO `czytelnicy` (`imie`, `nazwisko`, `adres`, `telefon`, `email`) VALUES
('Jan', 'Kowalski', 'ul. Lipowa 3, Warszawa', '501234567', 'jan.kowalski@example.com'),
('Anna', 'Nowak', 'ul. Długa 5, Kraków', '502345678', 'anna.nowak@example.com'),
('Tomasz', 'Wiśniewski', 'ul. Polna 10, Poznań', '503456789', 't.wisniewski@example.com'),
('Ewa', 'Zielińska', 'ul. Leśna 8, Gdańsk', '504567890', 'ewa.zielinska@example.com'),
('Michał', 'Lewandowski', 'ul. Wiosenna 1, Wrocław', '505678901', 'michal.lew@example.com');

INSERT INTO `ksiazki` (`tytul`, `autor`, `wydawnictwo`, `rok_wydania`, `kategoria`) VALUES
('Pan Tadeusz', 'Adam Mickiewicz', 'Ossolineum', 1834, 'Epika'),
('Lalka', 'Bolesław Prus', 'PWN', 1890, 'Powieść'),
('Zbrodnia i kara', 'Fiodor Dostojewski', 'Rebis', 1866, 'Powieść psychologiczna'),
('Wiedźmin', 'Andrzej Sapkowski', 'SuperNOWA', 1993, 'Fantasy'),
('Harry Potter', 'J.K. Rowling', 'Media Rodzina', 1997, 'Fantasy');

INSERT INTO `egzemplarze` (`k_id`, `stan`, `egzemplarz`) VALUES
(1, 'dobry', 'A-001'),
(1, 'dobry', 'A-002'),
(2, 'zużyty', 'B-001'),
(3, 'nowy', 'C-001'),
(4, 'dobry', 'D-001');

INSERT INTO `rezerwacje` (`e_id`, `c_id`, `data_rezerwacji`) VALUES
(1, 1, '2025-05-01'),
(2, 2, '2025-05-02'),
(3, 3, '2025-05-03'),
(4, 4, '2025-05-04'),
(5, 5, '2025-05-05');

INSERT INTO `wypozyczenia` (`e_id`, `c_id`, `data_wypozyczenia`, `przewidywana_data_zwrotu`, `data_zwrotu`) VALUES
(1, 1, '2025-04-01', '2025-04-15', '2025-04-12'),
(2, 2, '2025-04-03', '2025-04-17', '2025-04-16'),
(3, 3, '2025-04-05', '2025-04-19', NULL),
(4, 4, '2025-04-10', '2025-04-24', '2025-04-20'),
(5, 5, '2025-04-15', '2025-04-29', NULL);

INSERT INTO `kary` (`w_id`, `kwota_kary`, `status_kary`) VALUES
(1, 0, 'brak'),
(2, 0, 'brak'),
(3, 10, 'niezapłacona'),
(4, 0, 'brak'),
(5, 5, 'niezapłacona');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
