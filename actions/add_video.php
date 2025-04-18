<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Доступ запрещен']);
    exit();
}

// Проверка обязательных полей
if (empty($_POST['title']) || empty($_POST['college_id'])) {
    echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля']);
    exit();
}

// Проверка загрузки видео-файла
if (!isset($_FILES['video_file']) || $_FILES['video_file']['error'] != 0) {
    echo json_encode(['success' => false, 'message' => 'Файл видео не загружен или произошла ошибка при загрузке']);
    exit();
}

// Папки для сохранения файлов
$videos_dir = '../uploads/videos/';
$thumbnails_dir = '../uploads/thumbnails/';

// Проверка и создание директорий, если они не существуют
if (!file_exists($videos_dir)) {
    mkdir($videos_dir, 0777, true);
}
if (!file_exists($thumbnails_dir)) {
    mkdir($thumbnails_dir, 0777, true);
}

try {
    // Генерируем уникальное имя для файла видео
    $video_filename = uniqid() . '_' . basename($_FILES['video_file']['name']);
    $video_path = $videos_dir . $video_filename;
    
    // Перемещение загруженного видео-файла
    if (!move_uploaded_file($_FILES['video_file']['tmp_name'], $video_path)) {
        echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении видео-файла']);
        exit();
    }
    
    // Обработка миниатюры, если она загружена
    $thumbnail_path = null;
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $thumbnail_filename = uniqid() . '_' . basename($_FILES['thumbnail']['name']);
        $thumbnail_path = $thumbnails_dir . $thumbnail_filename;
        
        if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_path)) {
            echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении миниатюры']);
            exit();
        }
    }
    
    // Подготовка и выполнение запроса
    $stmt = $pdo->prepare("
        INSERT INTO videos (title, description, college_id, tags, video_path, thumbnail_path, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([
        $_POST['title'], 
        $_POST['description'] ?? null, 
        $_POST['college_id'],
        $_POST['tags'] ?? null,
        'uploads/videos/' . $video_filename,
        $thumbnail_path ? 'uploads/thumbnails/' . $thumbnail_filename : null
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Видео успешно добавлено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении видео']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?> 