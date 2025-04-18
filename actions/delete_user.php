<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}

// Проверка данных
if (empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID не указан']);
    exit();
}

try {
    // Проверка существования пользователя
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
        exit();
    }
    
    // Удаление пользователя
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    
    echo json_encode(['success' => true, 'message' => 'Пользователь успешно удален']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 