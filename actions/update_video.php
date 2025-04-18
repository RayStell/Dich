<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Доступ запрещен']);
    exit();
}

// Проверка обязательных полей
if (empty($_POST['videoId']) || empty($_POST['title']) || empty($_POST['college_id'])) {
    echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля']);
    exit();
}

// Получение текущих данных видео из базы данных
try {
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$_POST['videoId']]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$video) {
        echo json_encode(['success' => false, 'message' => 'Видео не найдено']);
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
    
    // Обработка загрузки нового видео-файла, если он загружен
    $video_path = $video['video_path']; // Используем текущий путь по умолчанию
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
        $video_filename = uniqid() . '_' . basename($_FILES['video_file']['name']);
        $new_video_path = $videos_dir . $video_filename;
        
        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $new_video_path)) {
            // Если загрузка новогоф файла успешна, обновляем путь
            $video_path = 'uploads/videos/' . $video_filename;
            
            // Удаляем старый файл видео, если он существует
            if (!empty($video['video_path']) && file_exists('../' . $video['video_path'])) {
                unlink('../' . $video['video_path']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении видео-файла']);
            exit();
        }
    }
    
    // Обработка миниатюры, если она загружена
    $thumbnail_path = $video['thumbnail_path']; // Используем текущий путь по умолчанию
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $thumbnail_filename = uniqid() . '_' . basename($_FILES['thumbnail']['name']);
        $new_thumbnail_path = $thumbnails_dir . $thumbnail_filename;
        
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $new_thumbnail_path)) {
            // Если загрузка новой миниатюры успешна, обновляем путь
            $thumbnail_path = 'uploads/thumbnails/' . $thumbnail_filename;
            
            // Удаляем старую миниатюру, если она существует
            if (!empty($video['thumbnail_path']) && file_exists('../' . $video['thumbnail_path'])) {
                unlink('../' . $video['thumbnail_path']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении миниатюры']);
            exit();
        }
    }
    
    // Подготовка и выполнение запроса на обновление
    $stmt = $pdo->prepare("
        UPDATE videos 
        SET title = ?, description = ?, college_id = ?, tags = ?, video_path = ?, thumbnail_path = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $result = $stmt->execute([
        $_POST['title'], 
        $_POST['description'] ?? null, 
        $_POST['college_id'],
        $_POST['tags'] ?? null,
        $video_path,
        $thumbnail_path,
        $_POST['videoId']
    ]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Видео успешно обновлено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении видео']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?> 