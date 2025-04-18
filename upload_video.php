<?php
session_start();
require_once 'config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    // Получаем список колледжей для выбора
    $stmt = $pdo->query("SELECT id, name FROM colleges ORDER BY name");
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
    <title>Загрузка видео - Учебная видеоплатформа</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
            flex-grow: 1;
        }

        .upload-form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="file"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-input-button {
            display: inline-block;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px dashed #ddd;
            border-radius: 6px;
            color: #7f8c8d;
            text-align: center;
            width: 100%;
            transition: all 0.3s ease;
        }

        .file-input-wrapper:hover .file-input-button {
            background-color: #e9ecef;
            border-color: #3498db;
            color: #3498db;
        }

        .submit-button {
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .submit-button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .upload-form {
                padding: 20px;
            }

            .submit-button {
                width: 100%;
                justify-content: center;
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
            <h1>Загрузка видео</h1>
            <p>Загрузите образовательные материалы для студентов</p>
        </div>
    </div>

    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form class="upload-form" method="POST" action="actions/add_video.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-heading"></i>
                    Название видео*
                </label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i>
                    Описание
                </label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="college">
                    <i class="fas fa-university"></i>
                    Учебное заведение*
                </label>
                <select id="college" name="college_id" required>
                    <option value="">-- Выберите учебное заведение --</option>
                    <?php foreach ($colleges as $college): ?>
                        <option value="<?php echo $college['id']; ?>">
                            <?php echo htmlspecialchars($college['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tags">
                    <i class="fas fa-tags"></i>
                    Теги (через запятую)
                </label>
                <input type="text" id="tags" name="tags" placeholder="Программирование, Дизайн, Инженерия">
                <small>Добавьте теги для лучшей организации и поиска видео</small>
            </div>

            <div class="form-group">
                <label for="video_file">
                    <i class="fas fa-file-video"></i>
                    Видеофайл* (MP4, WebM, OGG)
                </label>
                <div class="file-input-wrapper">
                    <div class="file-input-button">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Выберите файл или перетащите его сюда
                    </div>
                    <input type="file" id="video_file" name="video_file" accept="video/*" required>
                </div>
                <small>Максимальный размер файла: 100MB</small>
            </div>

            <div class="form-group">
                <label for="thumbnail">
                    <i class="fas fa-image"></i>
                    Миниатюра (изображение)
                </label>
                <div class="file-input-wrapper">
                    <div class="file-input-button">
                        <i class="fas fa-image"></i>
                        Выберите изображение для миниатюры
                    </div>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                </div>
                <small>Рекомендуемый размер: 1280x720px, формат: JPG, PNG</small>
            </div>

            <button type="submit" class="submit-button">
                <i class="fas fa-cloud-upload-alt"></i>
                Загрузить видео
            </button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Учебная видеоплатформа. Все права защищены.</p>
    </div>

    <script>
        // Обновление текста кнопки при выборе файла
        document.getElementById('video_file').addEventListener('change', function(e) {
            const button = this.parentElement.querySelector('.file-input-button');
            if (this.files.length > 0) {
                button.innerHTML = `<i class="fas fa-check"></i> Выбран файл: ${this.files[0].name}`;
            } else {
                button.innerHTML = `<i class="fas fa-cloud-upload-alt"></i> Выберите файл или перетащите его сюда`;
            }
        });

        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const button = this.parentElement.querySelector('.file-input-button');
            if (this.files.length > 0) {
                button.innerHTML = `<i class="fas fa-check"></i> Выбрано изображение: ${this.files[0].name}`;
            } else {
                button.innerHTML = `<i class="fas fa-image"></i> Выберите изображение для миниатюры`;
            }
        });
    </script>
</body>
</html> 