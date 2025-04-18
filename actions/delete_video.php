<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации администратора
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$video_id = $_GET['id'];

try {
    // Получаем информацию о видео, чтобы удалить файлы
    $stmt = $pdo->prepare("SELECT video_path, thumbnail_path FROM videos WHERE id = ?");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($video) {
        // Удаляем файл видео и миниатюру, если они существуют
        if (!empty($video['video_path']) && file_exists('../' . $video['video_path'])) {
            unlink('../' . $video['video_path']);
        }
        
        if (!empty($video['thumbnail_path']) && file_exists('../' . $video['thumbnail_path'])) {
            unlink('../' . $video['thumbnail_path']);
        }
        
        // Удаляем запись из базы данных
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->execute([$video_id]);
        
        header('Location: ../videos_list.php?deleted=1');
        exit();
    } else {
        header('Location: ../videos_list.php?error=not_found');
        exit();
    }
} catch(PDOException $e) {
    header('Location: ../videos_list.php?error=db_error');
    exit();
}
?> 