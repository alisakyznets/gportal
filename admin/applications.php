<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    redirect('../login.php', 'Доступ запрещен');
}

// Фильтр по статусу
$status_filter = isset($_GET['status']) ? (int)$_GET['status'] : 0;

// Поиск
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Обработка действий с заявками
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        // Обновление статуса заявки
        $stmt = $pdo->prepare("UPDATE applications SET status_id = ? WHERE application_id = ?");
        $stmt->execute([$_POST['status_id'], $_POST['application_id']]);
        $message = "Статус заявки обновлен";
    }
    elseif (isset($_POST['delete_application'])) {
        // Удаление заявки
        $application_id = (int)$_POST['application_id'];
        $stmt = $pdo->prepare("DELETE FROM applications WHERE application_id = ?");
        $stmt->execute([$application_id]);
        $message = "Заявка успешно удалена";
    }
}

// Получение всех заявок с фильтрами
$sql = "
    SELECT a.*, u.fio, u.login, c.category, s.status 
    FROM applications a
    JOIN users u ON a.user_id = u.user_id
    JOIN category c ON a.category_id = c.category_id
    JOIN status s ON a.status_id = s.status_id
    WHERE 1
";

$params = [];

if ($status_filter > 0) {
    $sql .= " AND a.status_id = ?";
    $params[] = $status_filter;
}

if (!empty($search_query)) {
    $sql .= " AND (a.title LIKE ? OR a.description LIKE ? OR u.fio LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY a.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

// Получение всех статусов
$statuses = $pdo->query("SELECT * FROM status")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление заявками</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .application-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="admin-container">
        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">Главная</a></li>
                <li class="active"><a href="applications.php">Заявки</a></li>
                <li><a href="categories.php">Категории</a></li>
                <li><a href="users.php">Пользователи</a></li>
                <li><a href="../logout.php">Выход</a></li>
            </ul>
        </div>
        
        <div class="admin-content">
            <div class="admin-back-btn">
                <a href="dashboard.php" class="btn-back">← Назад в админку</a>
            </div>
            
            <h1>Управление заявками</h1>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>
            
            <!-- Фильтры и поиск -->
            <div class="filters-section">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label>Фильтр по статусу:</label>
                        <select name="status" onchange="this.form.submit()">
                            <option value="0" <?= $status_filter === 0 ? 'selected' : '' ?>>Все заявки</option>
                            <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['status_id'] ?>" <?= $status_filter == $status['status_id'] ? 'selected' : '' ?>>
                                <?= escape($status['status']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Поиск:</label>
                        <input type="text" name="search" value="<?= escape($search_query) ?>" placeholder="Поиск по названию, описанию или автору">
                        <button type="submit" class="btn">Найти</button>
                    </div>
                </form>
            </div>
            
            <!-- Таблица заявок -->
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Автор</th>
                        <th>Категория</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                    <tr>
                        <td><?= $app['application_id'] ?></td>
                        <td>
                            <a href="../application-detail.php?id=<?= $app['application_id'] ?>">
                                <?= escape($app['title']) ?>
                            </a>
                        </td>
                        <td><?= escape($app['fio']) ?> (<?= escape($app['login']) ?>)</td>
                        <td><?= escape($app['category']) ?></td>
                        <td>
                            <span class="status-badge <?= strtolower(str_replace(' ', '-', $app['status'])) ?>">
                                <?= escape($app['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($app['created_at'])) ?></td>
                        <td>
                            <div class="application-actions">
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                                    <select name="status_id" class="status-select">
                                        <?php foreach ($statuses as $status): ?>
                                        <option value="<?= $status['status_id'] ?>" <?= $status['status_id'] == $app['status_id'] ? 'selected' : '' ?>>
                                            <?= escape($status['status']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-small">Изменить</button>
                                </form>
                                
                                <form method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить эту заявку?');">
                                    <input type="hidden" name="application_id" value="<?= $app['application_id'] ?>">
                                    <button type="submit" name="delete_application" class="btn btn-small btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (empty($applications)): ?>
                <p class="no-results">Заявки не найдены</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>