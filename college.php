<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: colleges_list.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM colleges WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $college = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$college) {
        header('Location: colleges_list.php');
        exit;
    }

    // Получаем видео для данного колледжа
    $videos = [];  // Инициализируем пустым массивом
    $stmt = $pdo->prepare("
        SELECT v.*, COALESCE(COUNT(DISTINCT w.id), 0) as views_count 
        FROM videos v 
        LEFT JOIN video_views w ON v.id = w.video_id
        WHERE v.college_id = ?
        GROUP BY v.id, v.title, v.description, v.filename, v.thumbnail, v.created_at
        ORDER BY v.created_at DESC
    ");
    $stmt->execute([$_GET['id']]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = 'Ошибка базы данных: ' . $e->getMessage();
    $videos = []; // В случае ошибки также инициализируем пустым массивом
}

// Инициализируем переменные с значениями по умолчанию
$total_views = 0;
if (!empty($videos)) {
    foreach ($videos as $video) {
        $total_views += (int)$video['views_count'];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($college['name']); ?> - Учебная видеоплатформа</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #2c3e50;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .logo a {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            background-color: #3498db;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .user-menu a:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .page-header {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 60px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('path/to/pattern.png') repeat;
            opacity: 0.1;
        }

        .page-header .container {
            position: relative;
            z-index: 1;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .college-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 30px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            flex-grow: 1;
        }

        .college-info {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 40px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .info-item i {
            font-size: 1.5rem;
            color: #3498db;
            width: 30px;
            text-align: center;
        }

        .info-content h3 {
            margin: 0 0 5px;
            font-size: 1.1rem;
            color: #2c3e50;
        }

        .info-content p {
            margin: 0;
            color: #7f8c8d;
            line-height: 1.6;
        }

        .college-description {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }

        .college-description h2 {
            margin: 0 0 20px;
            color: #2c3e50;
            font-size: 1.5rem;
        }

        .college-description p {
            color: #34495e;
            line-height: 1.8;
            margin: 0;
        }

        .section-title {
            margin: 0 0 30px;
            color: #2c3e50;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-title i {
            color: #3498db;
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .video-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .video-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .video-thumbnail {
            position: relative;
            padding-top: 56.25%;
            background: #34495e;
            overflow: hidden;
        }

        .video-thumbnail img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-thumbnail .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 48px;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .video-card:hover .play-icon {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1.1);
        }

        .video-content {
            padding: 20px;
        }

        .video-title {
            margin: 0 0 10px;
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .video-info {
            color: #7f8c8d;
            font-size: 0.9rem;
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .video-info span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .video-description {
            color: #34495e;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .video-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .video-button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .no-videos {
            grid-column: 1/-1;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .no-videos i {
            font-size: 48px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .no-videos p {
            color: #7f8c8d;
            margin: 0;
        }

        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 40px 0;
            }

            .college-stats {
                flex-direction: column;
                gap: 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .videos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php">
                <i class="fas fa-graduation-cap"></i>
                Учебная видеоплатформа
            </a>
        </div>
        <div class="user-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Привет, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Выйти
                </a>
            <?php else: ?>
                <a href="login.php">
                    <i class="fas fa-sign-in-alt"></i>
                    Войти
                </a>
            <?php endif; ?>
        </div>
    </header>

    <div class="page-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($college['name']); ?></h1>
            <div class="college-stats">
                <div class="stat-item">
                    <div class="stat-value"><?php echo count($videos); ?></div>
                    <div class="stat-label">Видеоматериалов</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo $total_views; ?></div>
                    <div class="stat-label">Просмотров</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="college-info">
            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="info-content">
                        <h3>Адрес</h3>
                        <p><?php echo !empty($college['address']) ? htmlspecialchars($college['address']) : 'Не указан'; ?></p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div class="info-content">
                        <h3>Телефон</h3>
                        <p><?php echo !empty($college['phone']) ? htmlspecialchars($college['phone']) : 'Не указан'; ?></p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p><?php echo !empty($college['email']) ? htmlspecialchars($college['email']) : 'Не указан'; ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($college['description'])): ?>
                <div class="college-description">
                    <h2>О учебном заведении</h2>
                    <p><?php echo nl2br(htmlspecialchars($college['description'])); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="section-title">
            <i class="fas fa-play-circle"></i>
            Видеоматериалы
        </h2>

        <div class="videos-grid">
            <?php if (empty($videos)): ?>
                <div class="no-videos">
                    <i class="fas fa-video-slash"></i>
                    <p>Пока нет доступных видеоматериалов</p>
                </div>
            <?php else: ?>
                <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <div class="video-thumbnail">
                            <?php if (!empty($video['thumbnail'])): ?>
                                <img src="uploads/thumbnails/<?php echo htmlspecialchars($video['thumbnail']); ?>" 
                                     alt="<?php echo htmlspecialchars($video['title']); ?>">
                            <?php endif; ?>
                            <i class="fas fa-play-circle play-icon"></i>
                        </div>
                        <div class="video-content">
                            <h3 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h3>
                            <div class="video-info">
                                <span>
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('d.m.Y', strtotime($video['created_at'])); ?>
                                </span>
                                <span>
                                    <i class="fas fa-eye"></i>
                                    <?php echo $video['views_count']; ?> просмотров
                                </span>
                            </div>
                            <?php if (!empty($video['description'])): ?>
                                <div class="video-description">
                                    <?php echo htmlspecialchars($video['description']); ?>
                                </div>
                            <?php endif; ?>
                            <a href="video.php?id=<?php echo $video['id']; ?>" class="video-button">
                                <i class="fas fa-play"></i>
                                Смотреть
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Учебная видеоплатформа. Все права защищены.</p>
    </div>
</body>
</html> 