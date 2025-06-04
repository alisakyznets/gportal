<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    redirect('../login.php', 'Доступ запрещен');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    
    // Нельзя удалить самого себя
    if ($user_id == $_SESSION['user_id']) {
        redirect('users.php', 'Вы не можете удалить себя');
    }
    
    try {
        // Удаляем пользователя
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        redirect('users.php', 'Пользователь успешно удален');
    } catch (PDOException $e) {
        redirect('users.php', 'Ошибка при удалении пользователя: ' . $e->getMessage());
    }
} else {
    redirect('users.php');
}