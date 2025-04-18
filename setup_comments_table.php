<?php
require_once 'config/database.php';

// Создание таблицы комментариев, если она не существует
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
?> 