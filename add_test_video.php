<?php
require_once 'config/database.php';

// Проверяем существование директорий для загрузки
$video_dir = 'uploads/videos/';
$thumbnail_dir = 'uploads/thumbnails/';

if (!file_exists($video_dir)) {
    mkdir($video_dir, 0777, true);
}

if (!file_exists($thumbnail_dir)) {
    mkdir($thumbnail_dir, 0777, true);
}

// Проверяем наличие колледжа
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM colleges");
    $college_count = $stmt->fetchColumn();
    
    if ($college_count == 0) {
        // Добавляем тестовый колледж
        $stmt = $pdo->prepare("
            INSERT INTO colleges (name, address, phone, email, description)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'Тестовый колледж',
            'ул. Примерная, 123',
            '+7 (123) 456-78-90',
            'test@college.ru',
            'Это тестовый колледж для демонстрации работы видеоплатформы.'
        ]);
        
        $college_id = $pdo->lastInsertId();
        echo "Создан тестовый колледж с ID: $college_id<br>";
    } else {
        // Берем ID первого колледжа
        $stmt = $pdo->query("SELECT id FROM colleges LIMIT 1");
        $college_id = $stmt->fetchColumn();
    }
    
    // Проверяем наличие тестового видео
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM videos WHERE title = ?");
    $stmt->execute(['Тестовое видео']);
    $video_count = $stmt->fetchColumn();
    
    if ($video_count == 0) {
        // Создаем простой HTML5 тестовый видеофайл
        $test_video_content = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #000;
        }
        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            font-family: Arial, sans-serif;
        }
        h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Тестовое видео</h1>
        <p>Это демонстрационное видео для проверки работы плеера</p>
    </div>
</body>
</html>
HTML;

        // Путь к файлу видео и скриншоту
        $video_filename = 'test_video.mp4';
        $thumbnail_filename = 'test_thumbnail.jpg';
        $video_path = $video_dir . $video_filename;
        $thumbnail_path = $thumbnail_dir . $thumbnail_filename;
        
        // Проверяем, есть ли тестовое видео в системе
        $real_video_exists = false;
        
        // Если реального видеофайла нет, создаем HTML заглушку
        if (!$real_video_exists) {
            file_put_contents($video_path, $test_video_content);
            
            // Создаем простую миниатюру с текстом
            $img = imagecreatetruecolor(640, 360);
            $bg_color = imagecolorallocate($img, 30, 30, 30);
            $text_color = imagecolorallocate($img, 255, 255, 255);
            
            imagefill($img, 0, 0, $bg_color);
            
            // Пишем текст на миниатюре
            $font = 5; // Встроенный шрифт (размер 5)
            $text = "Тестовое видео";
            
            // Центрируем текст
            $textwidth = imagefontwidth($font) * strlen($text);
            $textheight = imagefontheight($font);
            $x = (imagesx($img) - $textwidth) / 2;
            $y = (imagesy($img) - $textheight) / 2;
            
            imagestring($img, $font, $x, $y, $text, $text_color);
            
            // Сохраняем миниатюру
            imagejpeg($img, $thumbnail_path);
            imagedestroy($img);
        }
        
        // Добавляем запись о видео в базу данных
        $stmt = $pdo->prepare("
            INSERT INTO videos (title, description, college_id, tags, video_path, thumbnail_path, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            'Тестовое видео',
            'Это демонстрационное видео для проверки работы видеоплатформы.',
            $college_id,
            'тест, видео, демонстрация',
            $video_path,
            $thumbnail_path
        ]);
        
        $video_id = $pdo->lastInsertId();
        echo "Создано тестовое видео с ID: $video_id<br>";
        echo "<p>Теперь вы можете просмотреть видео здесь: <a href='video.php?id=$video_id'>Открыть тестовое видео</a></p>";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM videos WHERE title = ? LIMIT 1");
        $stmt->execute(['Тестовое видео']);
        $video_id = $stmt->fetchColumn();
        echo "Тестовое видео уже существует с ID: $video_id<br>";
        echo "<p>Вы можете просмотреть видео здесь: <a href='video.php?id=$video_id'>Открыть тестовое видео</a></p>";
    }
    
} catch(PDOException $e) {
    echo "Ошибка базы данных: " . $e->getMessage() . "<br>";
}
?> 