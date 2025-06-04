<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Проверка ID заявки
if (!isset($_GET['id'])) {
    redirect('index.php', 'Заявка не найдена');
}

$application_id = (int)$_GET['id'];

// Получение данных о заявке
$stmt = $pdo->prepare("
    SELECT a.*, u.fio, u.login, c.category, s.status 
    FROM applications a
    JOIN users u ON a.user_id = u.user_id
    JOIN category c ON a.category_id = c.category_id
    JOIN status s ON a.status_id = s.status_id
    WHERE a.application_id = ?
");
$stmt->execute([$application_id]);
$application = $stmt->fetch();

// Проверка существования заявки
if (!$application) {
    redirect('index.php', 'Заявка не найдена');
}

// Проверка прав доступа (только автор или администратор)
$is_owner = isLoggedIn() && ($_SESSION['user_id'] == $application['user_id']);
$is_admin = isAdmin();

if (!$is_owner && !$is_admin) {
    redirect('index.php', 'Доступ запрещен');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявка #<?= $application_id ?> - <?= escape($application['title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <div class="container">
        <!-- Кнопка "Назад" для администратора -->
        <?php if (isAdmin()): ?>
            <div class="admin-back-btn">
                <a href="admin/applications.php" class="btn-back">← К списку заявок</a>
            </div>
        <?php endif; ?>
        
        <div class="application-detail">
            <h1><?= escape($application['title']) ?></h1>
    
    <div class="container">
        <div class="application-detail">
            <h1><?= escape($application['title']) ?></h1>
            <div class="app-meta">
                <span class="status-badge <?= strtolower(str_replace(' ', '-', $application['status'])) ?>">
                    <?= escape($application['status']) ?>
                </span>
                <span>Категория: <?= escape($application['category']) ?></span>
                <span>Автор: <?= escape($application['fio']) ?></span>
                <span>Дата: <?= date('d.m.Y H:i', strtotime($application['created_at'])) ?></span>
            </div>
            
            <div class="app-content">
                <div class="app-images">
                    <div class="image-container">
                        <img src="<?= escape($application['path_to_image_before']) ?>" alt="Фото проблемы">
                        <div class="image-label">До</div>
                    </div>
                    <?php if ($application['path_to_image_after']): ?>
                    <div class="image-container">
                        <img src="<?= escape($application['path_to_image_after']) ?>" alt="Фото решения">
                        <div class="image-label">После</div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="app-description">
                    <h2>Описание проблемы</h2>
                    <p><?= nl2br(escape($application['description'])) ?></p>
                    
                    <?php if ($application['rejection_reason']): ?>
                    <div class="rejection-reason">
                        <h3>Причина отклонения:</h3>
                        <p><?= nl2br(escape($application['rejection_reason'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($is_owner && $application['status_id'] == 3): ?>
            <div class="app-actions">
                <form action="delete-application.php" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту заявку?');">
                    <input type="hidden" name="application_id" value="<?= $application_id ?>">
                    <button type="submit" class="btn btn-danger">Удалить заявку</button>
                </form>
            </div>
            <?php endif; ?>
            
            <?php if ($is_admin): ?>
            <div class="admin-actions">
                <h2>Действия администратора</h2>
                <form action="update-application.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="application_id" value="<?= $application_id ?>">
                    
                    <div class="form-group">
                        <label>Изменить статус:</label>
                        <select name="status_id" required>
                            <?php 
                            $statuses = $pdo->query("SELECT * FROM status")->fetchAll();
                            foreach ($statuses as $status): 
                            ?>
                            <option value="<?= $status['status_id'] ?>" <?= $status['status_id'] == $application['status_id'] ? 'selected' : '' ?>>
                                <?= escape($status['status']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Загрузить фото "После" (если решено):</label>
                        <input type="file" name="after_image" accept="image/jpeg,image/png">
                    </div>
                    
                    <div class="form-group">
                        <label>Причина отклонения (если отклонено):</label>
                        <textarea name="rejection_reason" rows="3"><?= escape($application['rejection_reason'] ?? '') ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_application" class="btn">Обновить заявку</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>