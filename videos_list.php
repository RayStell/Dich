<?php
session_start();
require_once 'config/database.php';

try {
    // Получаем параметры фильтрации
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $college_id = isset($_GET['college_id']) ? (int)$_GET['college_id'] : 0;
    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
    $tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';

    // Получаем список колледжей для фильтра
    $stmt = $pdo->query("SELECT id, name FROM colleges ORDER BY name");
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Формируем SQL запрос с учетом фильтров
    $sql = "SELECT v.*, c.name as college_name 
            FROM videos v 
            LEFT JOIN colleges c ON v.college_id = c.id 
            WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (v.title LIKE ? OR v.description LIKE ? OR c.name LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($college_id > 0) {
        $sql .= " AND v.college_id = ?";
        $params[] = $college_id;
    }

    if (!empty($tag)) {
        $sql .= " AND v.tags LIKE ?";
        $params[] = "%$tag%";
    }

    // Добавляем сортировку
    $sql .= match($sort) {
        'oldest' => " ORDER BY v.created_at ASC",
        default => " ORDER BY v.created_at DESC"
    };

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = 'Ошибка базы данных: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Видеоматериалы - Учебная видеоплатформа</title>
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
            padding: 40px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .page-header p {
            margin: 10px 0 0;
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            flex-grow: 1;
        }

        .filters {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            gap: 20px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .filter-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .video-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .video-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .video-thumbnail {
            position: relative;
            padding-top: 56.25%; /* 16:9 соотношение */
            background-color: #34495e;
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
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .video-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 0 0 10px;
        }

        .video-info {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .video-info p {
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .video-description {
            color: #34495e;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .video-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .video-tag {
            background-color: #f0f2f5;
            color: #7f8c8d;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .video-actions {
            display: flex;
            gap: 10px;
        }

        .video-button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 6px;
            background-color: #3498db;
            color: white;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .video-button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .video-button.secondary {
            background-color: #95a5a6;
        }

        .video-button.secondary:hover {
            background-color: #7f8c8d;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            grid-column: 1/-1;
        }

        .no-results i {
            font-size: 48px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .no-results p {
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
            .filters {
                flex-direction: column;
                gap: 15px;
            }

            .videos-grid {
                grid-template-columns: 1fr;
            }

            .video-actions {
                flex-direction: column;
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
            <h1>Видеоматериалы</h1>
            <p>Смотрите образовательные видео от лучших учебных заведений</p>
        </div>
    </div>

    <div class="container">
        <form class="filters" method="GET" action="">
            <div class="filter-group">
                <label for="search">
                    <i class="fas fa-search"></i>
                    Поиск по названию или описанию
                </label>
                <input type="text" id="search" name="search" class="filter-input" 
                       placeholder="Введите название или описание..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <label for="college">
                    <i class="fas fa-university"></i>
                    Учебное заведение
                </label>
                <select id="college" name="college_id" class="filter-input">
                    <option value="">Все учебные заведения</option>
                    <?php foreach ($colleges as $c): ?>
                        <option value="<?php echo $c['id']; ?>" 
                                <?php echo $college_id === $c['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="sort">
                    <i class="fas fa-sort"></i>
                    Сортировка
                </label>
                <select id="sort" name="sort" class="filter-input">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Сначала новые</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Сначала старые</option>
                </select>
            </div>
            <div class="filter-group" style="flex: 0 0 auto;">
                <button type="submit" class="video-button">
                    <i class="fas fa-filter"></i>
                    Применить фильтры
                </button>
            </div>
        </form>

        <?php if (!empty($tag)): ?>
            <div class="active-filter">
                Фильтр по тегу: <span class="active-tag"><?php echo htmlspecialchars($tag); ?></span>
                <a href="<?php echo remove_query_param('tag'); ?>" class="clear-filter"><i class="fas fa-times"></i></a>
            </div>
        <?php endif; ?>

        <div class="videos-grid">
            <?php if (isset($error)): ?>
                <div class="no-results">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php elseif (empty($videos)): ?>
                <div class="no-results">
                    <i class="fas fa-video-slash"></i>
                    <p>Видеоматериалы не найдены</p>
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
                            <h2 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h2>
                            <div class="video-info">
                                <p>
                                    <i class="fas fa-university"></i>
                                    <?php echo htmlspecialchars($video['college_name']); ?>
                                </p>
                                <p>
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo date('d.m.Y', strtotime($video['created_at'])); ?>
                                </p>
                            </div>
                            <?php if (!empty($video['description'])): ?>
                                <div class="video-description">
                                    <?php echo htmlspecialchars($video['description']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($video['tags'])): ?>
                                <div class="video-tags">
                                    <?php foreach (explode(',', $video['tags']) as $videoTag): ?>
                                        <?php $videoTag = trim($videoTag); ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['tag' => $videoTag])); ?>" 
                                           class="video-tag <?php echo $tag === $videoTag ? 'active' : ''; ?>">
                                            <?php echo htmlspecialchars($videoTag); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="video-actions">
                                <a href="video.php?id=<?php echo $video['id']; ?>" class="video-button">
                                    <i class="fas fa-play"></i>
                                    Смотреть
                                </a>
                                <a href="video.php?id=<?php echo $video['id']; ?>#info" class="video-button secondary">
                                    <i class="fas fa-info-circle"></i>
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Учебная видеоплатформа. Все права защищены.</p>
    </div>

    <?php
    function remove_query_param($param) {
        $params = $_GET;
        unset($params[$param]);
        return '?' . http_build_query($params);
    }
    ?>
</body>
</html> 