-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 27 2023 г., 19:43
-- Версия сервера: 10.4.27-MariaDB
-- Версия PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `water_system_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `meters`
--

CREATE TABLE `meters` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `meters` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `price` float DEFAULT NULL,
  `source` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `meters`
--

INSERT INTO `meters` (`id`, `id_user`, `meters`, `date`, `price`, `source`) VALUES
(47, 25, 5, '2023-03-11', NULL, NULL),
(52, 25, 15, '2023-03-11', NULL, NULL),
(59, 25, 17, '2023-03-14', NULL, NULL),
(60, 25, 20, '2023-03-14', NULL, NULL),
(61, 25, 22, '2023-03-14', NULL, NULL),
(64, 25, 25, '2023-03-14', NULL, NULL),
(105, 27, 3, '2023-04-03', NULL, NULL),
(106, 27, 5, '2023-04-03', NULL, NULL),
(107, 27, 7, '2023-04-03', NULL, NULL),
(108, 27, 9, '2024-01-03', NULL, NULL),
(109, 27, 10, '2023-04-03', NULL, NULL),
(150, 25, 35, '2023-04-08', NULL, NULL),
(151, 27, 20, '2023-04-08', NULL, NULL),
(156, 28, 10, '2023-04-08', NULL, NULL),
(157, 28, 15, '2023-04-08', NULL, NULL),
(160, 28, 20, '2023-04-08', NULL, NULL),
(161, 28, 30, '2023-04-08', NULL, NULL),
(162, 28, 32, '2023-04-08', NULL, NULL),
(163, 28, 40, '2023-04-08', NULL, NULL),
(164, 28, 55, '2023-04-08', NULL, NULL),
(165, 28, 56, '2023-04-08', NULL, NULL),
(166, 27, 30, '2023-04-19', NULL, NULL),
(167, 27, 50, '2023-04-20', NULL, NULL),
(168, 27, 52, '2023-04-21', NULL, NULL),
(169, 27, 75, '2023-04-22', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `months`
--

CREATE TABLE `months` (
  `id` int(11) NOT NULL,
  `price_for_month` float DEFAULT NULL,
  `id_meters` int(11) DEFAULT NULL,
  `id_user` int(55) DEFAULT NULL,
  `payment_status` enum('unpaid','paid','pending','overdue','partial_payment','canceled','refunded','failed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `months`
--

INSERT INTO `months` (`id`, `price_for_month`, `id_meters`, `id_user`, `payment_status`) VALUES
(11, 325, 52, 25, NULL),
(18, 65, 59, 25, NULL),
(19, 97.5, 60, 25, NULL),
(48, 65, 106, 27, NULL),
(49, 65, 107, 27, NULL),
(50, 292.5, 108, 27, NULL),
(51, 32.5, 109, 27, NULL),
(96, 325, 150, 25, 'unpaid'),
(97, 325, 151, 27, 'overdue'),
(102, 162.5, 157, 28, 'paid'),
(105, 162.5, 160, 28, 'overdue'),
(106, 325, 161, 28, 'overdue'),
(107, 65, 162, 28, 'paid'),
(108, 260, 163, 28, 'paid'),
(109, 487.5, 164, 28, 'overdue'),
(110, 32.5, 165, 28, 'paid'),
(111, 325, 166, 27, 'paid'),
(112, 650, 167, 27, 'paid'),
(113, 65, 168, 27, 'overdue'),
(114, 747.5, 169, 27, 'unpaid');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
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
  `role` varchar(20) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `counter` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `middle_name`, `mobile_phone`, `home_phone`, `email`, `password`, `role`, `token`, `address`, `counter`) VALUES
(25, 'Ігор', 'Дуванов', NULL, '+380997471990', '214521', 'duvanovigor79@gmail.com', '698d51a19d8a121ce581499d7b701668', NULL, NULL, 'Пулюя 10', '№ 214478569  '),
(27, 'Igor', 'Duvanov', NULL, ' +3807856999   ', ' 1231777', 'rchaplinskyi@ukr.net', '698d51a19d8a121ce581499d7b701668', NULL, NULL, ' Наливайка 12    ', ' № 23456789876777 '),
(28, 'Павло', 'Муратов', NULL, ' +3807856986', ' 7856', 'user_test@gmai.com', '698d51a19d8a121ce581499d7b701668', NULL, NULL, ' Наливайка 12', ' № 986562412'),
(40, 'ігор', 'дуванов', NULL, NULL, NULL, 'tesr@gmail.com', '$2y$10$0yahiYkfjNdt9wcV6LJnSOdlyYxId.dJmRm6HA4viJrou1Uz', NULL, NULL, NULL, NULL),
(41, 'ігор', 'Google_pro2004', NULL, NULL, NULL, 'user_test@gmail.com', '$2y$10$w5KeQ88kYH8xfH.sjc27AuB97JP1PXRpfsugwcJYucNjG.Li', NULL, NULL, NULL, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `meters`
--
ALTER TABLE `meters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `months`
--
ALTER TABLE `months`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_meters` (`id_meters`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `meters`
--
ALTER TABLE `meters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT для таблицы `months`
--
ALTER TABLE `months`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `meters`
--
ALTER TABLE `meters`
  ADD CONSTRAINT `meters_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `months`
--
ALTER TABLE `months`
  ADD CONSTRAINT `months_ibfk_1` FOREIGN KEY (`id_meters`) REFERENCES `meters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `months_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
