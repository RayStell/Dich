<?php
session_start();
require_once '../config/database.php';

// Проверяем, что форма была отправлена методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

// Получаем данные из формы
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Проверяем, что все поля заполнены
if (empty($email) || empty($password)) {
    header('Location: ../login.php?error=empty');
    exit;
}

try {
    // Включаем вывод ошибок
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Ищем пользователя по email и паролю
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password_hash = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Если пользователь найден
    if ($user) {
        // Устанавливаем данные сессии
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];

        // Перенаправляем на главную страницу
        header('Location: ../index.php');
        exit;
    } else {
        // Неверные учетные данные
        header('Location: ../login.php?error=invalid');
        exit;
    }
} catch (PDOException $e) {
    // В случае ошибки базы данных
    error_log("Ошибка входа: " . $e->getMessage());
    header('Location: ../login.php?error=db');
    exit;
} 