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
?> 