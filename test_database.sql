-- Создание базы данных
DROP DATABASE IF EXISTS college_db;
CREATE DATABASE college_db;
USE college_db;

-- Создание таблицы colleges
CREATE TABLE IF NOT EXISTS colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255),
    phone VARCHAR(50),
    email VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы videos
CREATE TABLE IF NOT EXISTS videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    college_id INT,
    tags VARCHAR(255),
    video_path VARCHAR(255) NOT NULL,
    thumbnail_path VARCHAR(255),
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Создание таблицы comments
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставка тестовых данных для колледжей
INSERT INTO colleges (name, address, phone, email, description) VALUES
('Московский Технический Колледж', 'ул. Ленина 1, Москва', '+7 (495) 123-45-67', 'info@mtk.ru', 'Ведущий технический колледж Москвы'),
('Санкт-Петербургский Колледж Информатики', 'пр. Невский 100, Санкт-Петербург', '+7 (812) 765-43-21', 'info@spki.ru', 'Колледж с углубленным изучением информационных технологий'),
('Казанский Профессиональный Колледж', 'ул. Баумана 20, Казань', '+7 (843) 234-56-78', 'info@kpk.ru', 'Современный колледж с богатой историей');

-- Вставка тестовых пользователей с простыми паролями
INSERT INTO users (username, email, password_hash, role) VALUES
('student1', 'student1@example.com', '123456', 'student'),
('teacher1', 'teacher1@example.com', '123456', 'teacher'),
('admin', 'admin@example.com', '123456', 'admin');

-- Вставка тестовых данных для видео
INSERT INTO videos (title, description, college_id, tags, video_path, thumbnail_path) VALUES
('Введение в программирование', 'Базовый курс по основам программирования', 1, 'программирование,основы,python', 'https://www.w3schools.com/html/mov_bbb.mp4', 'https://www.w3schools.com/html/pic_mountain.jpg'),
('Сетевые технологии', 'Курс по компьютерным сетям', 2, 'сети,cisco,networking', 'https://www.w3schools.com/html/mov_bbb.mp4', 'https://www.w3schools.com/html/pic_mountain.jpg'),
('3D моделирование', 'Основы работы в Blender', 3, '3d,моделирование,дизайн', 'https://www.w3schools.com/html/mov_bbb.mp4', 'https://www.w3schools.com/html/pic_mountain.jpg');

-- Вставка тестовых комментариев
INSERT INTO comments (video_id, user_id, content) VALUES
(1, 1, 'Отличный вводный курс! Очень понятно объясняется материал.'),
(1, 2, 'Спасибо за подробное объяснение основ программирования'),
(2, 1, 'Интересная информация о сетевых протоколах'),
(3, 3, 'Полезный курс по 3D моделированию, жду продолжения!'); 