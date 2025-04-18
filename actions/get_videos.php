<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Доступ запрещен']);
    exit();
}

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT v.*, c.name as college_name 
        FROM videos v 
        LEFT JOIN colleges c ON v.college_id = c.id 
        ORDER BY v.created_at DESC
    ");
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'videos' => $videos
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка при получении списка видео: ' . $e->getMessage()
    ]);
}
?> 