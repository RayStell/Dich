<?php
session_start();
require_once 'config/database.php';

// Получение параметров фильтрации
$category = isset($_GET['category']) ? $_GET['category'] : null;
$college_id = isset($_GET['college_id']) ? (int)$_GET['college_id'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Базовый SQL запрос
$sql = "SELECT v.*, c.name as college_name, 
        COALESCE(v.views, 0) as view_count,
        DATE_FORMAT(v.created_at, '%d.%m.%Y') as formatted_date
        FROM videos v 
        LEFT JOIN colleges c ON v.college_id = c.id 
        WHERE 1=1";
$params = [];

// Добавление условий фильтрации
if ($category) {
    $sql .= " AND v.category = ?";
    $params[] = $category;
}
if ($college_id) {
    $sql .= " AND v.college_id = ?";
    $params[] = $college_id;
}
if ($search) {
    $sql .= " AND (v.title LIKE ? OR v.description LIKE ? OR c.name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql .= " ORDER BY v.created_at DESC";

try {
    // Получение списка колледжей для фильтра
    $collegesStmt = $pdo->query("SELECT id, name FROM colleges ORDER BY name");
    $colleges = $collegesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Получение видео
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

        .user-menu span {
            margin-right: 15px;
            color: #ecf0f1;
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
            font-size: 2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .filters {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .filter-input {
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
        }

        .video-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .video-thumbnail {
            position: relative;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            background-color: #2c3e50;
            overflow: hidden;
        }

        .video-thumbnail i {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 48px;
            color: white;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }

        .video-card:hover .video-thumbnail i {
            opacity: 1;
        }

        .video-content {
            padding: 20px;
        }

        .video-title {
            margin: 0 0 10px;
            font-size: 1.2rem;
            color: #2c3e50;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .video-info {
            padding: 15px;
        }

        .video-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .video-stats {
            display: flex;
            gap: 15px;
        }

        .video-stat {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .video-category {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: #e9f2fe;
            color: #3498db;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .video-description {
            color: #34495e;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
        }

        .video-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .video-action:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            grid-column: 1/-1;
        }

        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination a {
            padding: 8px 16px;
            background-color: white;
            border-radius: 20px;
            color: #3498db;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background-color: #3498db;
            color: white;
            transform: translateY(-2px);
        }

        .pagination .active {
            background-color: #3498db;
            color: white;
        }

        @media (max-width: 768px) {
            .filters {
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
            <h1>Видеоматериалы</h1>
        </div>
    </div>

    <div class="container">
        <form class="filters" method="GET" action="">
            <div class="filter-group">
                <label for="search">Поиск</label>
                <input type="text" id="search" name="search" class="filter-input" 
                       placeholder="Поиск по названию..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="filter-group">
                <label for="category">Категория</label>
                <select id="category" name="category" class="filter-input">
                    <option value="">Все категории</option>
                    <option value="lecture" <?php echo $category === 'lecture' ? 'selected' : ''; ?>>Лекции</option>
                    <option value="practice" <?php echo $category === 'practice' ? 'selected' : ''; ?>>Практика</option>
                    <option value="seminar" <?php echo $category === 'seminar' ? 'selected' : ''; ?>>Семинары</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="college">Учебное заведение</label>
                <select id="college" name="college_id" class="filter-input">
                    <option value="">Все учебные заведения</option>
                    <?php foreach ($colleges as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $college_id === $c['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group" style="justify-content: flex-end;">
                <button type="submit" class="video-action">
                    <i class="fas fa-search"></i>
                    Применить фильтры
                </button>
            </div>
        </form>

        <div class="videos-grid">
            <?php if (isset($error)): ?>
                <div class="no-results">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #e74c3c; margin-bottom: 20px;"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php elseif (empty($videos)): ?>
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 48px; color: #bdc3c7; margin-bottom: 20px;"></i>
                    <p>Видеоматериалы не найдены</p>
                </div>
            <?php else: ?>
                <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <div class="video-thumbnail">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="video-content">
                            <h2 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h2>
                            <div class="video-info">
                                <div class="video-title"><?= htmlspecialchars($video['title']) ?></div>
                                <div class="video-category">
                                    <?= htmlspecialchars($video['category']) ?>
                                </div>
                                <div class="video-meta">
                                    <div class="video-stats">
                                        <div class="video-stat">
                                            <i class="fas fa-eye"></i>
                                            <?= number_format($video['view_count']) ?>
                                        </div>
                                        <div class="video-stat">
                                            <i class="fas fa-calendar"></i>
                                            <?= $video['formatted_date'] ?>
                                        </div>
                                    </div>
                                    <div class="college-name">
                                        <i class="fas fa-university"></i>
                                        <?= htmlspecialchars($video['college_name']) ?>
                                    </div>
                                </div>
                                <div class="video-description">
                                    <?= nl2br(htmlspecialchars(substr($video['description'], 0, 100))) ?>...
                                </div>
                            </div>
                            <a href="video.php?id=<?php echo $video['id']; ?>" class="video-action">
                                <i class="fas fa-play"></i>
                                Смотреть
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <a href="#" class="active">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Учебная видеоплатформа. Все права защищены.</p>
    </div>
</body>
</html> 