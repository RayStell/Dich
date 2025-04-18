<?php
require_once 'config/database.php';

// Создание таблицы колледжей (учебных заведений)
try {
    $sql = "
    CREATE TABLE IF NOT EXISTS colleges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        address VARCHAR(255),
        phone VARCHAR(50),
        email VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Таблица колледжей успешно создана или уже существует.<br>";
} catch(PDOException $e) {
    echo "Ошибка при создании таблицы колледжей: " . $e->getMessage() . "<br>";
}

// Создание таблицы видео
try {
    $sql = "
    CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        college_id INT,
        tags VARCHAR(255),
        video_path VARCHAR(255) NOT NULL,
        thumbnail_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Таблица видео успешно создана или уже существует.<br>";
} catch(PDOException $e) {
    echo "Ошибка при создании таблицы видео: " . $e->getMessage() . "<br>";
}

// Создание таблицы комментариев
try {
    $sql = "
    CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        video_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $pdo->exec($sql);
    echo "Таблица комментариев успешно создана или уже существует.<br>";
} catch(PDOException $e) {
    echo "Ошибка при создании таблицы комментариев: " . $e->getMessage() . "<br>";
}

echo "<p>Инициализация базы данных завершена.</p>";
?> 