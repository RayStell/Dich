<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users ORDER BY id");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при получении списка пользователей: ' . $e->getMessage()
    ]);
}
?> 