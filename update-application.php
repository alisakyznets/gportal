<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isAdmin()) {
    redirect('login.php', 'Доступ запрещен');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_application'])) {
    $application_id = (int)$_POST['application_id'];
    $status_id = (int)$_POST['status_id'];
    $rejection_reason = trim($_POST['rejection_reason'] ?? '');
    
    // Получаем текущую заявку
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE application_id = ?");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();
    
    if (!$application) {
        redirect('index.php', 'Заявка не найдена');
    }
    
    // Обработка загрузки изображения "после"
    $path_to_image_after = $application['path_to_image_after'];
    if (isset($_FILES['after_image']) && $_FILES['after_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png'];
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($file_info, $_FILES['after_image']['tmp_name']);
        
        if (in_array($mime_type, $allowed_types)) {
            $extension = $mime_type === 'image/jpeg' ? '.jpg' : '.png';
            $filename = uniqid('after_') . $extension;
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['after_image']['tmp_name'], $destination)) {
                $path_to_image_after = 'uploads/' . $filename;
                
                // Удаляем старое изображение "после", если оно было
                if ($application['path_to_image_after'] && file_exists($application['path_to_image_after'])) {
                    unlink($application['path_to_image_after']);
                }
            }
        }
    }
    
    // Обновление заявки
    $stmt = $pdo->prepare("
        UPDATE applications 
        SET 
            status_id = ?, 
            path_to_image_after = ?, 
            rejection_reason = ?
        WHERE application_id = ?
    ");
    $stmt->execute([
        $status_id,
        $path_to_image_after,
        $rejection_reason,
        $application_id
    ]);
    
    redirect('application-detail.php?id=' . $application_id, 'Заявка успешно обновлена');
} else {
    redirect('index.php');
}