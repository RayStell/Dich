<?php
session_start();
require_once 'config/database.php';

// Если пользователь уже авторизован, перенаправляем на главную страницу
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$message = '';
$error = '';
$showForm = false;
$token = $_GET['token'] ?? '';

// Проверяем валидность токена
if (empty($token)) {
    $error = 'Неверная ссылка для сброса пароля';
} else {
    try {
        // Ищем пользователя с таким токеном
        $stmt = $pdo->prepare("SELECT id, username, reset_token_expires FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Проверяем, найден ли пользователь и не истек ли срок действия токена
        if ($user && strtotime($user['reset_token_expires']) > time()) {
            $showForm = true;
            $userId = $user['id'];
            $username = $user['username'];
        } else {
            $error = 'Ссылка для сброса пароля недействительна или срок её действия истек';
        }
    } catch(PDOException $e) {
        $error = "Произошла ошибка: " . $e->getMessage();
    }
}

// Обработка формы сброса пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['confirm_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $userId = $_POST['user_id'];
    
    if (empty($password)) {
        $error = 'Пожалуйста, введите пароль';
        $showForm = true;
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
        $showForm = true;
    } else {
        try {
            // Обновляем пароль и удаляем токен сброса
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
            $stmt->execute([$hashed_password, $userId]);
            
            $message = "Ваш пароль успешно обновлен. Теперь вы можете <a href='login.php'>войти в систему</a>.";
            $showForm = false;
        } catch(PDOException $e) {
            $error = "Произошла ошибка при обновлении пароля: " . $e->getMessage();
            $showForm = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .reset-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .reset-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .reset-form {
            padding: 20px 0;
        }
        
        .reset-button {
            width: 100%;
            margin-top: 10px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h1>Сброс пароля</h1>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($showForm): ?>
            <form class="reset-form" method="post" action="">
                <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
                
                <div class="form-group">
                    <label for="password">Новый пароль:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Подтвердите пароль:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="reset-button">Сохранить новый пароль</button>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            <a href="login.php">Вернуться на страницу входа</a>
        </div>
    </div>
</body>
</html> 