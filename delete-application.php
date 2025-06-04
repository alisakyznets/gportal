<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Необходимо авторизоваться');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = (int)$_POST['application_id'];
    
    // Получаем заявку
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE application_id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();
    
    // Проверка прав (только автор и только если статус "на рассмотрении")
    if (!$application || $application['user_id'] != $_SESSION['user_id'] || $application['status_id'] != 3) {
        redirect('profile.php', 'Нельзя удалить эту заявку');
    }
    
    // Удаление заявки
    $stmt = $pdo->prepare("DELETE FROM applications WHERE application_id = ?");
    $stmt->execute([$application_id]);
    
    // Удаление изображений
    if (file_exists($application['path_to_image_before'])) {
        unlink($application['path_to_image_before']);
    }
    if ($application['path_to_image_after'] && file_exists($application['path_to_image_after'])) {
        unlink($application['path_to_image_after']);
    }
    
    redirect('profile.php', 'Заявка успешно удалена');
} else {
    redirect('profile.php');
}