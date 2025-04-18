<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}

// Проверка данных
if (empty($_POST['userId']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['role'])) {
    echo json_encode(['success' => false, 'message' => 'Обязательные поля должны быть заполнены']);
    exit();
}

try {
    // Проверка существования email у других пользователей
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$_POST['email'], $_POST['userId']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким email уже существует']);
        exit();
    }
    
    // Обновление данных пользователя
    if (!empty($_POST['password'])) {
        // Если указан новый пароль
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->execute([
            $_POST['username'],
            $_POST['email'],
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['role'],
            $_POST['userId']
        ]);
    } else {
        // Если пароль не меняется
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([
            $_POST['username'],
            $_POST['email'],
            $_POST['role'],
            $_POST['userId']
        ]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Данные пользователя успешно обновлены']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 