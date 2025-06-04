<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php', 'Необходимо авторизоваться');
}

// Получение данных текущего пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Получение заявок пользователя
$stmt = $pdo->prepare("
    SELECT a.*, c.category, s.status 
    FROM applications a
    JOIN category c ON a.category_id = c.category_id
    JOIN status s ON a.status_id = s.status_id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <div class="container">
        <h1>Добро пожаловать, <?= escape($user['fio']) ?>!</h1>
        
        <div class="profile-info">
            <p><strong>Логин:</strong> <?= escape($user['login']) ?></p>
            <p><strong>Email:</strong> <?= escape($user['email']) ?></p>
            <a href="create-application.php" class="btn">Создать новую заявку</a>
        </div>
        
        <h2>Мои заявки</h2>
        
        <?php if (empty($applications)): ?>
            <p>У вас пока нет заявок</p>
        <?php else: ?>
            <div class="applications-list">
                <?php foreach ($applications as $app): ?>
                <div class="application-item">
                    <div class="app-header">
                        <h3><?= escape($app['title']) ?></h3>
                        <span class="status-badge <?= strtolower(str_replace(' ', '-', $app['status'])) ?>">
                            <?= escape($app['status']) ?>
                        </span>
                    </div>
                    
                    <p><strong>Категория:</strong> <?= escape($app['category']) ?></p>
                    <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($app['created_at'])) ?></p>
                    
                    <div class="app-actions">
                        <a href="application-detail.php?id=<?= $app['application_id'] ?>" class="btn btn-small">Подробнее</a>
                        <?php if ($app['status'] === 'Заявка находится на рассмотрении'): ?>
                        <form action="delete-application.php" method="POST" class="inline-form">
                            <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                            <button type="submit" class="btn btn-small btn-danger">Удалить</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>