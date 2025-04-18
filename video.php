<?php
session_start();
require_once 'config/database.php';

// Проверка наличия ID видео в URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$video_id = $_GET['id'];
$video = null;
$college = null;
$comments = [];
$error = '';

try {
    // Получение информации о видео
    $stmt = $pdo->prepare("
        SELECT v.*, c.name as college_name, c.id as college_id
        FROM videos v
        LEFT JOIN colleges c ON v.college_id = c.id
        WHERE v.id = ?
    ");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$video) {
        $error = 'Видео не найдено';
    } else {
        // Увеличение счетчика просмотров
        $stmt = $pdo->prepare("
            UPDATE videos 
            SET views = views + 1 
            WHERE id = ?
        ");
        $stmt->execute([$video_id]);
        
        // Получение комментариев к видео
        $stmt = $pdo->prepare("
            SELECT c.*, u.username as user_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.video_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$video_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Получение связанных видео из того же колледжа
        if ($video['college_id']) {
            $stmt = $pdo->prepare("
                SELECT id, title, thumbnail_path 
                FROM videos 
                WHERE college_id = ? AND id != ? 
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            $stmt->execute([$video['college_id'], $video_id]);
            $related_videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch(PDOException $e) {
    $error = 'Ошибка базы данных: ' . $e->getMessage();
}

// Обработка добавления комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && !empty($_POST['comment']) && !empty($video)) {
    if (!isset($_SESSION['user_id'])) {
        $error = 'Для добавления комментария необходимо авторизоваться';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO comments (video_id, user_id, content, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $result = $stmt->execute([$video_id, $_SESSION['user_id'], $_POST['comment']]);
            
            if ($result) {
                // Перезагрузка страницы для обновления комментариев
                header("Location: video.php?id=$video_id");
                exit();
            } else {
                $error = 'Ошибка при добавлении комментария';
            }
        } catch(PDOException $e) {
            $error = 'Ошибка базы данных: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $video ? htmlspecialchars($video['title']) : 'Просмотр видео'; ?></title>
    <link rel="stylesheet" href="css/admin.css">
    <!-- Добавляем библиотеку Video.js для улучшенного плеера -->
    <link href="https://vjs.zencdn.net/7.17.0/video-js.css" rel="stylesheet" />
    <style>
        .video-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .video-header {
            margin-bottom: 20px;
        }
        
        .video-content {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
        }
        
        @media (max-width: 900px) {
            .video-content {
                grid-template-columns: 1fr;
            }
        }
        
        .video-main {
            width: 100%;
        }
        
        .video-player-container {
            width: 100%;
            margin-bottom: 20px;
            background-color: #000;
            position: relative;
            overflow: hidden;
            padding-top: 56.25%; /* 16:9 соотношение */
            border-radius: 8px;
        }
        
        .video-js {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .video-info {
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        
        .video-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .video-stats {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .video-views {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
        }
        
        .video-views svg {
            width: 18px;
            height: 18px;
            fill: #666;
        }
        
        .video-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }
        
        .video-tag {
            background-color: #f0f0f0;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.8em;
        }
        
        .video-description {
            margin-bottom: 30px;
            line-height: 1.6;
            color: #333;
        }
        
        .comments-section {
            margin-top: 30px;
        }
        
        .comment-form {
            margin-bottom: 30px;
        }
        
        .comment-form textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }
        
        .comment-form button {
            margin-top: 10px;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .comment-form button:hover {
            background-color: #45a049;
        }
        
        .comments-list {
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        .comment {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .comment-author {
            font-weight: bold;
        }
        
        .comment-date {
            color: #777;
            font-size: 0.9em;
        }
        
        .comment-content {
            line-height: 1.5;
        }
        
        .no-comments {
            text-align: center;
            padding: 30px;
            color: #777;
        }
        
        .related-videos {
            margin-bottom: 20px;
        }
        
        .related-video {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            text-decoration: none;
            color: inherit;
        }
        
        .related-video:hover {
            background-color: #f9f9f9;
        }
        
        .related-video-thumbnail {
            width: 100px;
            height: 60px;
            background-color: #f0f0f0;
            background-size: cover;
            background-position: center;
            margin-right: 10px;
            flex-shrink: 0;
            border-radius: 4px;
        }
        
        .related-video-info {
            flex-grow: 1;
        }
        
        .related-video-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9em;
            color: #333;
        }
        
        .back-link {
            margin-bottom: 20px;
            display: inline-block;
            color: #555;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .admin-actions {
            display: flex;
            gap: 10px;
        }
        
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            color: white;
        }
        
        .edit-btn {
            background-color: #4CAF50;
        }
        
        .delete-btn {
            background-color: #f44336;
        }
        
        .edit-btn:hover {
            background-color: #45a049;
        }
        
        .delete-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php">Учебная видеоплатформа</a>
        </div>
        <div class="user-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Привет, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Войти</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="video-container">
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($video): ?>
            <a href="javascript:history.back()" class="back-link">← Назад</a>
            
            <div class="video-header">
                <h1><?php echo htmlspecialchars($video['title']); ?></h1>
            </div>
            
            <div class="video-content">
                <div class="video-main">
                    <div class="video-player-container">
                        <video 
                            id="my-video"
                            class="video-js vjs-big-play-centered"
                            controls
                            preload="auto"
                            poster="<?php echo !empty($video['thumbnail_path']) ? htmlspecialchars($video['thumbnail_path']) : 'img/default-thumbnail.jpg'; ?>"
                            data-setup='{}'
                        >
                            <source src="<?php echo htmlspecialchars($video['video_path']); ?>" type="video/mp4" />
                            <p class="vjs-no-js">
                                Для просмотра этого видео включите JavaScript и обновите браузер до
                                <a href="https://videojs.com/html5-video-support/" target="_blank">
                                  поддерживающего HTML5 видео
                                </a>
                            </p>
                        </video>
                    </div>
                    
                    <div class="video-info">
                        <div class="video-meta">
                            <div>
                                <p>
                                    Учебное заведение: 
                                    <a href="college.php?id=<?php echo $video['college_id']; ?>">
                                        <?php echo htmlspecialchars($video['college_name'] ?? 'Не указано'); ?>
                                    </a>
                                </p>
                                <p>Дата публикации: <?php echo date('d.m.Y', strtotime($video['created_at'])); ?></p>
                            </div>
                            
                            <div class="video-stats">
                                <div class="video-views">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                    </svg>
                                    <?php echo isset($video['views']) ? number_format($video['views']) : '0'; ?> просмотров
                                </div>
                                
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <div class="admin-actions">
                                    <a href="edit_video.php?id=<?php echo $video['id']; ?>" class="edit-btn">Редактировать</a>
                                    <a href="javascript:void(0)" onclick="if(confirm('Вы уверены, что хотите удалить это видео?')) { window.location = 'actions/delete_video.php?id=<?php echo $video['id']; ?>'; }" class="delete-btn">Удалить</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($video['tags'])): ?>
                            <div class="video-tags">
                                <?php 
                                $tags = explode(',', $video['tags']);
                                foreach ($tags as $tag): 
                                    $tag = trim($tag);
                                    if (!empty($tag)):
                                ?>
                                    <span class="video-tag"><?php echo htmlspecialchars($tag); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($video['description'])): ?>
                        <div class="video-description">
                            <h2>Описание</h2>
                            <p><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="comments-section">
                        <h2>Комментарии</h2>
                        
                        <div class="comment-form">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="post" action="">
                                    <textarea name="comment" placeholder="Добавьте комментарий..." required></textarea>
                                    <button type="submit">Отправить</button>
                                </form>
                            <?php else: ?>
                                <p>Чтобы оставлять комментарии, <a href="login.php">войдите</a> в систему.</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="comments-list">
                            <?php if (empty($comments)): ?>
                                <div class="no-comments">Пока нет комментариев. Будьте первым!</div>
                            <?php else: ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment">
                                        <div class="comment-header">
                                            <div class="comment-author"><?php echo htmlspecialchars($comment['user_name']); ?></div>
                                            <div class="comment-date"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></div>
                                        </div>
                                        <div class="comment-content">
                                            <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="video-sidebar">
                    <?php if (!empty($related_videos)): ?>
                        <div class="related-videos">
                            <h3>Похожие видео</h3>
                            
                            <?php foreach ($related_videos as $rel_video): ?>
                                <a href="video.php?id=<?php echo $rel_video['id']; ?>" class="related-video">
                                    <div class="related-video-thumbnail" style="background-image: url('<?php echo !empty($rel_video['thumbnail_path']) ? htmlspecialchars($rel_video['thumbnail_path']) : 'img/default-thumbnail.jpg'; ?>')"></div>
                                    <div class="related-video-info">
                                        <div class="related-video-title"><?php echo htmlspecialchars($rel_video['title']); ?></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Скрипты для Video.js -->
    <script src="https://vjs.zencdn.net/7.17.0/video.min.js"></script>
    <script>
        // Инициализация Video.js плеера
        var player = videojs('my-video', {
            controls: true,
            autoplay: false,
            preload: 'auto',
            fluid: true,
            playbackRates: [0.5, 1, 1.5, 2],
            controlBar: {
                children: [
                    'playToggle',
                    'volumePanel',
                    'currentTimeDisplay',
                    'timeDivider',
                    'durationDisplay',
                    'progressControl',
                    'playbackRateMenuButton',
                    'fullscreenToggle'
                ]
            }
        });
    </script>
</body>
</html> 