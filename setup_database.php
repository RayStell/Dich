<?php
require_once 'config/database.php';

try {
    // Создание таблицы colleges
    $pdo->exec("CREATE TABLE IF NOT EXISTS colleges (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        address TEXT,
        phone VARCHAR(50),
        email VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Создание таблицы videos
    $pdo->exec("CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        filename VARCHAR(255),
        thumbnail VARCHAR(255),
        college_id INT,
        category ENUM('lecture', 'practice', 'seminar'),
        tags VARCHAR(255),
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE
    )");

    // Создание таблицы video_views
    $pdo->exec("CREATE TABLE IF NOT EXISTS video_views (
        id INT AUTO_INCREMENT PRIMARY KEY,
        video_id INT,
        user_id INT,
        viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
    )");

    // Создание таблицы users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Добавление тестового администратора, если таблица пользователей пуста
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password_hash, role) 
                   VALUES ('Администратор', 'admin@example.com', '$password_hash', 'admin')");
    }

    // Выводим HTML-страницу с результатами
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Настройка базы данных</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
            }
            h2 {
                color: #333;
                border-bottom: 1px solid #ddd;
                padding-bottom: 10px;
            }
            p {
                margin-bottom: 15px;
            }
            a {
                color: #007bff;
                text-decoration: none;
            }
            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <h2>Настройка базы данных</h2>
        <p>База данных успешно настроена.</p>
        <p>Данные для входа администратора:</p>
        <ul>
            <li>Email: admin@example.com</li>
            <li>Пароль: admin123</li>
        </ul>
        <p><a href="login.php">Перейти на страницу входа</a></p>
    </body>
    </html>
    <?php
} catch(PDOException $e) {
    die("Ошибка настройки базы данных: " . $e->getMessage());
}
?> 