-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Feb 20. 10:20
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `gymdb`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `gym`
--

CREATE TABLE `gym` (
  `gym_id` varchar(20) NOT NULL,
  `gym_name` varchar(30) NOT NULL,
  `gym_address` varchar(150) NOT NULL,
  `gym_type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `pwd` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `login`
--

INSERT INTO `login` (`id`, `username`, `pwd`) VALUES
(1, 'admin', 'admin'),
(2, 'teszt', '$2y$10$..7XwzF.GEe2EP7jxWcHnOFOQjf0UPF4pKiy3Ui8NFeyLA/TRqeVO'),
(3, 'teszt2', 'asdasd'),
(4, 'teszt22', 'jelszo'),
(5, 'teszt3', '111'),
(6, 'teszt32', 'asda'),
(7, 'teszt33', 'asdf');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `member`
--

CREATE TABLE `member` (
  `member_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `age` int(11) NOT NULL,
  `package` varchar(50) NOT NULL,
  `expDate` date DEFAULT NULL,
  `mobilenum` varchar(20) NOT NULL,
  `trainer_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `member`
--

INSERT INTO `member` (`member_id`, `name`, `dob`, `age`, `package`, `expDate`, `mobilenum`, `trainer_id`) VALUES
('2', 'MATIAS', '2020-01-01', 12, 'gangszta', NULL, '98232232', '1'),
('3', 'Akhos', '1979-07-13', 100, 'None', NULL, '+43321239', ''),
('4', 'CHordas', '1020-02-27', 1004, 'könnyűsúlyú', NULL, 'nincs', 'van');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `trainer`
--

CREATE TABLE `trainer` (
  `trainer_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `time` varchar(50) NOT NULL,
  `mobilenum` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- A tábla indexei `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `mobilenum` (`mobilenum`);

--
-- A tábla indexei `trainer`
--
ALTER TABLE `trainer`
  ADD PRIMARY KEY (`trainer_id`),
  ADD UNIQUE KEY `mobilenum` (`mobilenum`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
