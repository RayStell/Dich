<?php
require_once 'config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Проверяем таблицу users
    $stmt = $pdo->query("SELECT * FROM users");
    echo "<h2>Содержимое таблицы users:</h2>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?> 