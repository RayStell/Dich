-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 16 2025 г., 10:39
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `college_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `colleges`
--

CREATE TABLE `colleges` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `colleges`
--

INSERT INTO `colleges` (`id`, `name`, `address`, `phone`, `email`, `description`, `created_at`) VALUES
(1, 'Московский Технический Колледж', 'ул. Ленина 1, Москва', '+7 (495) 123-45-67', 'info@mtk.ru', 'Ведущий технический колледж Москвы', '2025-04-16 07:05:20'),
(2, 'Санкт-Петербургский Колледж Информатики', 'пр. Невский 100, Санкт-Петербург', '+7 (812) 765-43-21', 'info@spki.ru', 'Колледж с углубленным изучением информационных технологий', '2025-04-16 07:05:20'),
(3, 'Казанский Профессиональный Колледж', 'ул. Баумана 20, Казань', '+7 (843) 234-56-78', 'info@kpk.ru', 'Современный колледж с богатой историей', '2025-04-16 07:05:20');

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `video_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 1, 'Отличный вводный курс! Очень понятно объясняется материал.', '2025-04-16 14:05:20'),
(2, 1, 2, 'Спасибо за подробное объяснение основ программирования', '2025-04-16 14:05:20'),
(3, 2, 1, 'Интересная информация о сетевых протоколах', '2025-04-16 14:05:20'),
(4, 3, 3, 'Полезный курс по 3D моделированию, жду продолжения!', '2025-04-16 14:05:20');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'student1', 'student1@example.com', '123456', 'student', '2025-04-16 07:05:20'),
(2, 'teacher1', 'teacher1@example.com', '123456', 'teacher', '2025-04-16 07:05:20'),
(3, 'admin', 'admin@example.com', '123456', 'admin', '2025-04-16 07:05:20'),
(4, 'ПЕТР', 'adamsmit909777@gmail.com', '1234', 'user', '2025-04-16 07:10:00'),
(5, '123', '123@mail.ru', '123123', 'user', '2025-04-16 08:26:46');

-- --------------------------------------------------------

--
-- Структура таблицы `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `college_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `videos`
--

INSERT INTO `videos` (`id`, `title`, `description`, `college_id`, `tags`, `video_path`, `thumbnail_path`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Введение в программирование', 'Базовый курс по основам программирования', 1, 'программирование,основы,python', 'uploads/videos/67ff6bd90f743_перевязка.mp4', 'uploads/thumbnails/67ff6bd90fb0f_4Vm0EfnmmSk.jpg', 9, '2025-04-16 07:05:20', '2025-04-16 08:38:03'),
(2, 'Сетевые технологии', 'Курс по компьютерным сетям', 2, 'сети,cisco,networking', 'uploads/videos/67ff6bb9e04a7_перевязка.mp4', 'uploads/thumbnails/67ff6bb9e0769_4Vm0EfnmmSk.jpg', 1, '2025-04-16 07:05:20', '2025-04-16 08:35:20'),
(3, '3D моделирование', 'Основы работы в Blender', 3, '3d,моделирование,дизайн', 'uploads/videos/67ff6bad6fb1f_перевязка.mp4', 'uploads/thumbnails/67ff6bad700c5_4Vm0EfnmmSk.jpg', 4, '2025-04-16 07:05:20', '2025-04-16 08:38:54');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `college_id` (`college_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `colleges`
--
ALTER TABLE `colleges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
