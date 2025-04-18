<?php
session_start();
require_once 'config/database.php';

// Проверка авторизации администратора
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Проверка ID видео
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$video_id = $_GET['id'];
$video = null;
$colleges = [];
$error = '';
$success = '';

// Получение информации о видео
try {
    $stmt = $pdo->prepare("
        SELECT v.*, c.name as college_name
        FROM videos v
        LEFT JOIN colleges c ON v.college_id = c.id
        WHERE v.id = ?
    ");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$video) {
        $error = 'Видео не найдено';
    }
    
    // Получение списка колледжей
    $stmt = $pdo->query("SELECT id, name FROM colleges ORDER BY name");
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = 'Ошибка базы данных: ' . $e->getMessage();
}

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($video)) {
    // Проверка обязательных полей
    if (empty($_POST['title'])) {
        $error = 'Введите название видео';
    } elseif (empty($_POST['college_id'])) {
        $error = 'Выберите учебное заведение';
    } else {
        try {
            // Подготовка и выполнение запроса на обновление
            $stmt = $pdo->prepare("
                UPDATE videos
                SET title = ?, description = ?, college_id = ?, tags = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $_POST['title'],
                $_POST['description'] ?? null,
                $_POST['college_id'],
                $_POST['tags'] ?? null,
                $video_id
            ]);
            
            if ($result) {
                $success = 'Видео успешно обновлено';
                
                // Обновление данных видео для отображения
                $stmt = $pdo->prepare("
                    SELECT v.*, c.name as college_name
                    FROM videos v
                    LEFT JOIN colleges c ON v.college_id = c.id
                    WHERE v.id = ?
                ");
                $stmt->execute([$video_id]);
                $video = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = 'Ошибка при обновлении видео';
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
    <title>Редактирование видео</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .edit-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .edit-header {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .video-preview {
            margin: 20px 0;
            max-width: 100%;
        }
        
        .video-preview video {
            width: 100%;
            max-height: 400px;
            background-color: #000;
            border-radius: 5px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .submit-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        
        .submit-button:hover {
            background-color: #45a049;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #555;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
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

    <div class="edit-container">
        <a href="video.php?id=<?php echo $video_id; ?>" class="back-link">← Вернуться к видео</a>
        
        <div class="edit-header">
            <h1>Редактирование видео</h1>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($video): ?>
            <div class="video-preview">
                <video controls>
                    <source src="<?php echo htmlspecialchars($video['video_path']); ?>" type="video/mp4">
                    Ваш браузер не поддерживает HTML5 видео.
                </video>
            </div>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="title">Название видео*:</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($video['title']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Описание:</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($video['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="college_id">Учебное заведение*:</label>
                    <select id="college_id" name="college_id" required>
                        <option value="">-- Выберите учебное заведение --</option>
                        <?php foreach ($colleges as $college): ?>
                            <option value="<?php echo $college['id']; ?>" <?php echo $college['id'] == $video['college_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($college['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tags">Теги (через запятую):</label>
                    <input type="text" id="tags" name="tags" placeholder="Программирование, Дизайн, Инженерия" value="<?php echo htmlspecialchars($video['tags'] ?? ''); ?>">
                </div>
                
                <button type="submit" class="submit-button">Сохранить изменения</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html> 