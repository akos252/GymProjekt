-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Már 12. 12:20
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.0.30

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

--
-- A tábla adatainak kiíratása `gym`
--

INSERT INTO `gym` (`gym_id`, `gym_name`, `gym_address`, `gym_type`,`owner_id` ) VALUES
('GYM67d004f95ac68', 'Mega Fitness', 'Kaposvár, Németh István fasor 25.', 'Unisex', '10'),
('GYM67d00650889e2', 'Gutler Gym&Fitness', 'Kaposvár, Szent Imre u. 1.', 'Unisex', '10'),
('GYM67d014b232cbe', 'Eden Fitness', 'Kaposvár, Dózsa György u. 15.', 'Female', '10'),
('GYM67d015851dd53', 'X-body Gym', 'Kaposvár, Petőfi u. 11.', 'Male', '10'),
('GYM67d015d4be3d0', 'XS-Fittlesz', 'Kaposvár, Arany János u. 2/b.', 'Unisex', '10'),
('GYM67d0161e0e398', 'Szupi Fitness', 'Kaposvár. Béke u. 45.', 'Unisex', '10'),
('GYM67d01bdb2af4c', 'FitPro Gym&Fitness', 'Kaposszerdahely, Kossuth L. u. 25/c.', 'Unisex', '10');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `mobilenum` varchar(21) NOT NULL,
  `dob` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `login`
--

INSERT INTO `login` (`id`, `username`, `pwd`, `mobilenum`, `dob`) VALUES
(9, 'admin', 'admin', '06308892512', '2005-02-01');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `memberships`
--

CREATE TABLE `memberships` (
  `membership_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gym_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL DEFAULT curdate(),
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `trainer`
--

CREATE TABLE `trainer` (
  `trainer_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `time` varchar(50) NOT NULL,
  `mobilenum` varchar(20) NOT NULL,
  `image` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `trainer_gym`
--

CREATE TABLE `trainer_gym` (
  `trainer_id` varchar(50) NOT NULL,
  `gym_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `gym`
--
ALTER TABLE `gym`
  ADD PRIMARY KEY (`gym_id`);

--
-- A tábla indexei `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `mobilenum` (`mobilenum`);

--
-- A tábla indexei `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`membership_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `gym_id` (`gym_id`);

--
-- A tábla indexei `trainer`
--
ALTER TABLE `trainer`
  ADD PRIMARY KEY (`trainer_id`),
  ADD UNIQUE KEY `mobilenum` (`mobilenum`);

--
-- A tábla indexei `trainer_gym`
--
ALTER TABLE `trainer_gym`
  ADD PRIMARY KEY (`trainer_id`,`gym_id`),
  ADD KEY `gym_id` (`gym_id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `memberships`
--
ALTER TABLE `memberships`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `memberships`
--
ALTER TABLE `memberships`
  ADD CONSTRAINT `memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `memberships_ibfk_2` FOREIGN KEY (`gym_id`) REFERENCES `gym` (`gym_id`) ON DELETE CASCADE;

--
-- Megkötések a táblához `trainer_gym`
--
ALTER TABLE `trainer_gym`
  ADD CONSTRAINT `trainer_gym_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainer` (`trainer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainer_gym_ibfk_2` FOREIGN KEY (`gym_id`) REFERENCES `gym` (`gym_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
