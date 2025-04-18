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
    $stmt = $pdo->prepare("
        SELECT v.*, c.name as college_name
        FROM videos v
        LEFT JOIN colleges c ON v.college_id = c.id
        WHERE v.id = ?
    ");
    $stmt->execute([$_GET['id']]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($video) {
        echo json_encode($video);
    } else {
        echo json_encode(['error' => 'Видео не найдено']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 