<?php
session_start();
require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}

// Проверка данных
if (empty($_POST['collegeId']) || empty($_POST['name'])) {
    echo json_encode(['success' => false, 'message' => 'Обязательные поля должны быть заполнены']);
    exit();
}

try {
    // Проверка существования колледжа с таким названием
    $stmt = $pdo->prepare("SELECT id FROM colleges WHERE name = ? AND id != ?");
    $stmt->execute([$_POST['name'], $_POST['collegeId']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Колледж с таким названием уже существует']);
        exit();
    }
    
    // Обновление данных колледжа
    $sql = "UPDATE colleges SET name = ?, address = ?, phone = ?, email = ?, description = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['name'],
        $_POST['address'] ?? '',
        $_POST['phone'] ?? '',
        $_POST['email'] ?? '',
        $_POST['description'] ?? '',
        $_POST['collegeId']
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Данные колледжа успешно обновлены']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 