<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Доступ запрещен']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID не указан']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM colleges WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $college = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($college) {
        echo json_encode($college);
    } else {
        echo json_encode(['error' => 'Колледж не найден']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 