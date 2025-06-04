<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Проверка прав администратора
if (!isAdmin()) {
    redirect('/login.php', 'Доступ запрещен');
}

// Получение статистики
$stats = [
    'total_apps'    => $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn(),
    'new_apps'      => $pdo->query("SELECT COUNT(*) FROM applications WHERE status_id = 3")->fetchColumn(),
    'solved_apps'   => $pdo->query("SELECT COUNT(*) FROM applications WHERE status_id = 1")->fetchColumn(),
    'rejected_apps' => $pdo->query("SELECT COUNT(*) FROM applications WHERE status_id = 2")->fetchColumn(),
    'total_users'   => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'new_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(), 
];

// Получение последних заявок
$recentApplications = $pdo->query("
    SELECT 
        a.application_id,
        a.title,
        a.created_at,
        u.fio,
        c.category,
        s.status
    FROM applications a
    JOIN users u ON a.user_id = u.user_id
    JOIN category c ON a.category_id = c.category_id
    JOIN status s ON a.status_id = s.status_id
    ORDER BY a.created_at DESC
    LIMIT 10
")->fetchAll();

// Получение последних пользователей
$recentUsers = $pdo->query("
    SELECT 
        user_id,
        fio, 
        login, 
        email
    FROM users
    ORDER BY user_id DESC
    LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="admin-container">
        <div class="sidebar">
            <ul>
                <li class="active"><a href="dashboard.php">Главная</a></li>
                <li><a href="applications.php">Заявки</a></li>
                <li><a href="categories.php">Категории</a></li>
                <li><a href="users.php">Пользователи</a></li>
                <li><a href="../logout.php">Выход</a></li>
            </ul>
        </div>
        
        <div class="admin-content">
            <h1>Панель управления</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['total_apps'] ?></div>
                    <div class="stat-label">Всего заявок</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['new_apps'] ?></div>
                    <div class="stat-label">Новых заявок</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['solved_apps'] ?></div>
                    <div class="stat-label">Решённых</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['rejected_apps'] ?></div>
                    <div class="stat-label">Отклонённых</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['total_users'] ?></div>
                    <div class="stat-label">Пользователей</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-value"><?= $stats['new_users'] ?></div>
                    <div class="stat-label">Новых пользователей</div>
                </div>
            </div>
            
            <div class="admin-section">
                <div class="admin-column">
                    <h2>Последние заявки</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Автор</th>
                                <th>Статус</th>
                                <th>Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentApplications as $app): ?>
                                <tr>
                                    <td><?= $app['application_id'] ?></td>
                                    <td>
                                        <a href="../application-detail.php?id=<?= $app['application_id'] ?>">
                                            <?= escape($app['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= escape($app['fio']) ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower(str_replace(' ', '-', $app['status'])) ?>">
                                            <?= escape($app['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y', strtotime($app['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="admin-column">
                    <h2>Последние пользователи</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО</th>
                                <th>Логин</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?= $user['user_id'] ?></td>
                                    <td><?= escape($user['fio']) ?></td>
                                    <td><?= escape($user['login']) ?></td>
                                    <td><?= escape($user['email']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>