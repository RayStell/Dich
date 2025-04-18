<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}

// Проверка данных
if (empty($_POST['name'])) {
    echo json_encode(['success' => false, 'message' => 'Название колледжа должно быть заполнено']);
    exit();
}

try {
    // Проверка существования колледжа с таким названием
    $stmt = $pdo->prepare("SELECT id FROM colleges WHERE name = ?");
    $stmt->execute([$_POST['name']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Колледж с таким названием уже существует']);
        exit();
    }
    
    // Добавление колледжа
    $sql = "INSERT INTO colleges (name, address, phone, email, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['name'],
        $_POST['address'] ?? '',
        $_POST['phone'] ?? '',
        $_POST['email'] ?? '',
        $_POST['description'] ?? ''
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Колледж успешно добавлен']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 