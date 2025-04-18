<?php
session_start();
require_once 'config/database.php';

try {
    // Получаем список всех городов
    $stmt = $pdo->query("SELECT DISTINCT SUBSTRING_INDEX(address, ',', -1) as city FROM colleges WHERE address IS NOT NULL ORDER BY city");
    $cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Получаем параметры фильтрации
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $city = isset($_GET['city']) ? trim($_GET['city']) : '';

    // Формируем SQL запрос с учетом фильтров
    $sql = "SELECT c.*, 
            (SELECT COUNT(*) FROM videos v WHERE v.college_id = c.id) as video_count,
            DATE_FORMAT(c.created_at, '%d.%m.%Y') as formatted_date
            FROM colleges c WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (c.name LIKE ? OR c.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if (!empty($city)) {
        $sql .= " AND c.address LIKE ?";
        $params[] = "%$city%";
    }

    $sql .= " ORDER BY c.name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = 'Ошибка базы данных: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учебные заведения - Учебная видеоплатформа</title>
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

        .colleges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .college-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .college-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .college-info {
            padding: 20px;
        }

        .college-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .college-meta {
            display: grid;
            gap: 10px;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: #3498db;
            width: 16px;
        }

        .college-stats {
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .college-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .action-button {
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }

        .action-button.primary {
            background: #3498db;
            color: white;
        }

        .action-button.secondary {
            background: #e9f2fe;
            color: #3498db;
        }

        .action-button:hover {
            opacity: 0.9;
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

            .colleges-grid {
                grid-template-columns: 1fr;
            }

            .college-actions {
                flex-direction: column;
            }
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
            <h1>Учебные заведения</h1>
            <p>Найдите учебное заведение и получите доступ к образовательным материалам</p>
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
                <label for="city">
                    <i class="fas fa-map-marker-alt"></i>
                    Город
                </label>
                <select id="city" name="city" class="filter-input">
                    <option value="">Все города</option>
                    <?php foreach ($cities as $c): ?>
                        <option value="<?php echo htmlspecialchars($c); ?>" 
                                <?php echo $city === $c ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group" style="flex: 0 0 auto;">
                <button type="submit" class="college-button">
                    <i class="fas fa-filter"></i>
                    Применить фильтры
                </button>
            </div>
        </form>

        <div class="colleges-grid">
            <?php if (isset($error)): ?>
                <div class="no-results">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php elseif (empty($colleges)): ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <p>Учебные заведения не найдены</p>
                </div>
            <?php else: ?>
                <?php foreach ($colleges as $college): ?>
                    <div class="college-card">
                        <div class="college-info">
                            <div class="college-title">
                                <?= htmlspecialchars($college['name']) ?>
                            </div>
                            <div class="college-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= $college['address'] ? htmlspecialchars($college['address']) : 'Не указан' ?>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-phone"></i>
                                    <?= $college['phone'] ? htmlspecialchars($college['phone']) : 'Не указан' ?>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-envelope"></i>
                                    <?= $college['email'] ? htmlspecialchars($college['email']) : 'Не указан' ?>
                                </div>
                            </div>
                            <?php if (!empty($college['description'])): ?>
                                <div class="college-description">
                                    <?= nl2br(htmlspecialchars(substr($college['description'], 0, 150))) ?>...
                                </div>
                            <?php endif; ?>
                            <div class="college-actions">
                                <a href="videos.php?college_id=<?= $college['id'] ?>" class="action-button primary">
                                    <i class="fas fa-play"></i>
                                    Видеоматериалы
                                </a>
                                <a href="college.php?id=<?= $college['id'] ?>" class="action-button secondary">
                                    <i class="fas fa-info-circle"></i>
                                    Подробнее
                                </a>
                            </div>
                        </div>
                        <div class="college-stats">
                            <div class="stat-item">
                                <i class="fas fa-video"></i>
                                <?= number_format($college['video_count']) ?> видео
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <?= $college['formatted_date'] ?>
                            </div>
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