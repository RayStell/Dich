<?php
require_once 'config/database.php';

// Добавление счетчика просмотров в таблицу videos, если его еще нет
try {
    // Проверяем, существует ли уже колонка views
    $result = $pdo->query("SHOW COLUMNS FROM videos LIKE 'views'");
    if ($result->rowCount() == 0) {
        // Колонка не существует, добавляем её
        $sql = "ALTER TABLE videos ADD COLUMN views INT DEFAULT 0";
        $pdo->exec($sql);
        echo "Поле счетчика просмотров успешно добавлено в таблицу videos.<br>";
    } else {
        echo "Поле просмотров уже существует в таблице videos.<br>";
    }
    
    echo "<p>Обновление таблицы videos завершено.</p>";
} catch(PDOException $e) {
    echo "Ошибка при обновлении таблицы videos: " . $e->getMessage() . "<br>";
}
?> 