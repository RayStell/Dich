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
    // Проверка существования колледжа
    $stmt = $pdo->prepare("SELECT id FROM colleges WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Колледж не найден']);
        exit();
    }
    
    // Удаление колледжа
    $stmt = $pdo->prepare("DELETE FROM colleges WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    
    echo json_encode(['success' => true, 'message' => 'Колледж успешно удален']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 