<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    redirect('../login.php', 'Доступ запрещен');
}

// Получение всех пользователей
$users = $pdo->query("SELECT * FROM users ORDER BY user_id DESC")->fetchAll();

// Обработка изменения роли пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['role'];
    
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    $stmt->execute([$new_role, $user_id]);
    
    $message = "Роль пользователя обновлена";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="admin-container">
        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">Главная</a></li>
                <li><a href="applications.php">Заявки</a></li>
                <li><a href="categories.php">Категории</a></li>
                <li class="active"><a href="users.php">Пользователи</a></li>
                <li><a href="../logout.php">Выход</a></li>
            </ul>
        </div>
        <div class="admin-content">
            <!-- Кнопка "Назад" для админа -->
            <div class="admin-back-btn">
                <a href="dashboard.php" class="btn-back">← Назад в админку</a>
            </div>
        <div class="admin-content">
            <h1>Управление пользователями</h1>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ФИО</th>
                        <th>Логин</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['user_id'] ?></td>
                        <td><?= escape($user['fio']) ?></td>
                        <td><?= escape($user['login']) ?></td>
                        <td><?= escape($user['email']) ?></td>
                        <td>
                            <form method="POST" class="role-form">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <select name="role" class="role-select">
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Пользователь</option>
                                </select>
                                <button type="submit" name="change_role" class="btn btn-small">Изменить</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                            <form method="POST" action="delete-user.php" class="inline-form">
                                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Удалить пользователя?')">Удалить</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>