<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Доступ запрещен']);
    exit();
}

try {
    $stmt = $pdo->query("SELECT id, name, email, phone FROM colleges ORDER BY id DESC");
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($colleges);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 