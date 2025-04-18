<?php
session_start();
require_once 'config/database.php';

if (isset($_GET['admin']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'videos';
    include 'admin_panel.php';
    exit;
} else {
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учебная видеоплатформа</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
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
        }
        
        .user-menu a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 20px;
            background-color: #3498db;
            transition: all 0.3s ease;
        }
        
        .user-menu a:hover {
            background-color: #2980b9;
        }
        
        .user-menu span {
            margin-right: 15px;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #3498db, #8e44ad);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        
        .hero-section h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-section p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: 0.9;
        }
        
        .home-container {
            max-width: 1200px;
            margin: -50px auto 40px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .feature-card-image {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 60px;
            color: white;
        }
        
        .feature-card-image.colleges {
            background-color: #3498db;
        }
        
        .feature-card-image.videos {
            background-color: #2ecc71;
        }
        
        .feature-card-image.upload {
            background-color: #e74c3c;
        }
        
        .feature-card-content {
            padding: 25px;
        }
        
        .feature-card-content h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.4rem;
            color: #2c3e50;
        }
        
        .feature-card-content p {
            margin: 0 0 20px;
            color: #7f8c8d;
            line-height: 1.6;
        }
        
        .card-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .card-button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .card-button.colleges {
            background-color: #3498db;
        }
        
        .card-button.videos {
            background-color: #2ecc71;
        }
        
        .card-button.upload {
            background-color: #e74c3c;
        }
        
        .card-button.colleges:hover {
            background-color: #2980b9;
        }
        
        .card-button.videos:hover {
            background-color: #27ae60;
        }
        
        .card-button.upload:hover {
            background-color: #c0392b;
        }
        
        .admin-panel-link {
            margin-top: 40px;
            text-align: center;
        }
        
        .admin-panel-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 30px;
            background-color: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .admin-panel-button:hover {
            background-color: #2c3e50;
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px 0;
            margin-top: 50px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php">Учебная видеоплатформа</a>
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

    <div class="hero-section">
        <h1>Добро пожаловать на Учебную видеоплатформу</h1>
        <p>Получите доступ к лучшим учебным материалам для профессионального и личностного роста</p>
    </div>

    <div class="home-container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-card-image colleges">
                    <i class="fas fa-university"></i>
                </div>
                <div class="feature-card-content">
                    <h3>Учебные заведения</h3>
                    <p>Просматривайте список учебных заведений, ознакомьтесь с их программами и предложениями.</p>
                    <a href="colleges_list.php" class="card-button colleges">
                        <i class="fas fa-arrow-right"></i>
                        Перейти к списку
                    </a>
                </div>
            </div>
            
            <div class="feature-card">
                <div class="feature-card-image videos">
                    <i class="fas fa-play-circle"></i>
                </div>
                <div class="feature-card-content">
                    <h3>Видеоматериалы</h3>
                    <p>Смотрите образовательные видео по различным дисциплинам и темам обучения.</p>
                    <a href="videos_list.php" class="card-button videos">
                        <i class="fas fa-play"></i>
                        Смотреть видео
                    </a>
                </div>
            </div>
            
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div class="feature-card">
                    <div class="feature-card-image upload">
                        <i class="fas fa-upload"></i>
                    </div>
                    <div class="feature-card-content">
                        <h3>Загрузка видео</h3>
                        <p>Загружайте собственные образовательные видеоматериалы для обучения студентов.</p>
                        <a href="upload_video.php" class="card-button upload">
                            <i class="fas fa-cloud-upload-alt"></i>
                            Загрузить видео
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <div class="admin-panel-link">
                <a href="index.php?admin=1" class="admin-panel-button">
                    <i class="fas fa-cog"></i>
                    Перейти в панель администратора
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Учебная видеоплатформа. Все права защищены.</p>
    </div>
</body>
</html>
<?php
}
?> 