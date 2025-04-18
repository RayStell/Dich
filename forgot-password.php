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

// Обработка формы запроса сброса пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = 'Пожалуйста, введите ваш email';
    } else {
        try {
            // Проверяем, существует ли пользователь с таким email
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Генерируем уникальный токен
                $token = bin2hex(random_bytes(32));
                
                // Устанавливаем срок действия токена (24 часа)
                $expires = date('Y-m-d H:i:s', time() + 86400);
                
                // Сохраняем токен и срок его действия в базе данных
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
                $stmt->execute([$token, $expires, $user['id']]);
                
                // Формируем ссылку для сброса пароля
                $resetLink = "http://{$_SERVER['HTTP_HOST']}/project/reset-password.php?token=$token";
                
                // Отправляем email
                $to = $email;
                $subject = "Восстановление пароля";
                $message_body = "Здравствуйте, {$user['username']}!\n\n";
                $message_body .= "Вы запросили сброс пароля. Пожалуйста, перейдите по следующей ссылке, чтобы задать новый пароль:\n\n";
                $message_body .= $resetLink . "\n\n";
                $message_body .= "Если вы не запрашивали сброс пароля, проигнорируйте это сообщение.\n\n";
                $message_body .= "Ссылка действительна в течение 24 часов.";
                
                $headers = "From: noreply@example.com\r\n";
                $headers .= "Reply-To: noreply@example.com\r\n";
                
                if (mail($to, $subject, $message_body, $headers)) {
                    $message = "Инструкции по сбросу пароля отправлены на ваш email.";
                } else {
                    $error = "Не удалось отправить email. Пожалуйста, попробуйте позже.";
                }
            } else {
                // Для безопасности не сообщаем, что пользователь не найден
                $message = "Если указанный email зарегистрирован в системе, инструкции по сбросу пароля будут отправлены.";
            }
        } catch(PDOException $e) {
            $error = "Произошла ошибка: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля</title>
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
            <h1>Восстановление пароля</h1>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form class="reset-form" method="post" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <small>Введите email, указанный при регистрации</small>
            </div>
            
            <button type="submit" class="reset-button">Отправить инструкции</button>
        </form>
        
        <div class="login-link">
            <a href="login.php">Вернуться на страницу входа</a>
        </div>
    </div>
</body>
</html> 