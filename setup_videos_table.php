<?php
require_once 'config/database.php';

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
?> 