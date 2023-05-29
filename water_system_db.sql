-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Трв 29 2023 р., 15:18
-- Версія сервера: 10.4.27-MariaDB
-- Версія PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `water_system_db`
--

-- --------------------------------------------------------

--
-- Структура таблиці `meters`
--

CREATE TABLE `meters` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `meters` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `date_update` datetime(6) DEFAULT NULL,
  `id_operator` int(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `meters`
--

INSERT INTO `meters` (`id`, `id_user`, `meters`, `date`, `date_update`, `id_operator`) VALUES
(47, 25, 5, '2023-03-11', NULL, NULL),
(52, 25, 15, '2023-03-11', '2023-05-29 16:14:37.000000', 27),
(59, 25, 31, '2023-03-14', '2023-05-29 16:14:37.000000', 27),
(60, 25, 20, '2023-03-14', '2023-05-29 16:14:37.000000', 27),
(150, 25, 52, '2023-04-08', '2023-05-29 16:14:37.000000', 27),
(154, 30, 50, '2023-05-14', NULL, NULL),
(155, 30, 75, '2023-05-15', NULL, NULL),
(156, 30, 80, '2023-05-16', NULL, NULL),
(159, 30, 95, '2023-05-17', NULL, NULL),
(160, 30, 100, '2023-05-17', NULL, NULL),
(180, 30, 120, '2023-05-22', NULL, NULL),
(181, 25, 61, '2023-05-22', '2023-05-29 16:14:37.000000', 27),
(182, 25, 65, '2023-05-25', '2023-05-29 16:14:37.000000', 27),
(183, 25, 90, '2023-05-29', '2023-05-29 16:14:37.000000', 27);

-- --------------------------------------------------------

--
-- Структура таблиці `months`
--

CREATE TABLE `months` (
  `id` int(11) NOT NULL,
  `price_for_month` float DEFAULT NULL,
  `id_meters` int(11) DEFAULT NULL,
  `id_user` int(55) DEFAULT NULL,
  `payment_status` enum('не сплачено','сплачено','в очікуванні','прострочений','часткова оплата','скасовано','відшкодовано','невдалий') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `months`
--

INSERT INTO `months` (`id`, `price_for_month`, `id_meters`, `id_user`, `payment_status`) VALUES
(11, 325, 52, 25, 'сплачено'),
(18, 65, 59, 25, 'прострочений'),
(19, 95, 60, 25, 'сплачено'),
(96, 450, 150, 25, 'часткова оплата'),
(99, 812.5, 155, 30, ''),
(100, 162.5, 156, 30, ''),
(103, 487.5, 159, 30, ''),
(104, 162.5, 160, 30, ''),
(122, 650, 180, 30, 'не сплачено'),
(123, 260, 181, 25, 'відшкодовано'),
(124, 292.5, 182, 25, 'сплачено'),
(125, 812.5, 183, 25, 'не сплачено');

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(16) DEFAULT NULL,
  `last_name` varchar(24) DEFAULT NULL,
  `middle_name` varchar(24) DEFAULT NULL,
  `mobile_phone` varchar(15) DEFAULT NULL,
  `home_phone` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(55) DEFAULT NULL,
  `role` varchar(10) DEFAULT 'user',
  `personal_account` varchar(255) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `counter` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `middle_name`, `mobile_phone`, `home_phone`, `email`, `password`, `role`, `personal_account`, `address`, `counter`) VALUES
(25, 'admin', 'admin', 'Павлович', '+380997471990', '214521', 'duvanovigor79@gmail.com', '698d51a19d8a121ce581499d7b701668', 'user', 'О/Р test', 'Пулюя 10', '№ 214478569  '),
(27, 'operator', 'operator', '', ' +3807856986', ' 1231412', 'rchaplinskyi@ukr.net', '698d51a19d8a121ce581499d7b701668', 'operator', 'О/Р 2222', ' Наливайка 12', ' № 23456789876543'),
(30, 'test', 'Duvanov', 'Павлович', '0997471990', '', 'test_osob@gmail.com', 'bc47a7a828c79c91d9364ee72a8b09af', 'user', 'О/Р 31232184', 'Пулюя', ' № 23456789876543');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `meters`
--
ALTER TABLE `meters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `operator_update` (`id_operator`),
  ADD KEY `id_operator` (`id_operator`);

--
-- Індекси таблиці `months`
--
ALTER TABLE `months`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_meters` (`id_meters`),
  ADD KEY `id_user` (`id_user`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `meters`
--
ALTER TABLE `meters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT для таблиці `months`
--
ALTER TABLE `months`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `meters`
--
ALTER TABLE `meters`
  ADD CONSTRAINT `meters_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meters_ibfk_2` FOREIGN KEY (`id_operator`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `months`
--
ALTER TABLE `months`
  ADD CONSTRAINT `months_ibfk_1` FOREIGN KEY (`id_meters`) REFERENCES `meters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `months_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
