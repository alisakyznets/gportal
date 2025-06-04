<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    redirect('../login.php', 'Доступ запрещен');
}

// Обработка добавления категории
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    
    if (!empty($category_name)) {
        $stmt = $pdo->prepare("INSERT INTO category (category) VALUES (?)");
        $stmt->execute([$category_name]);
        $message = "Категория успешно добавлена";
    } else {
        $error = "Введите название категории";
    }
}

// Обработка удаления категории
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    try {
        // Начинаем транзакцию для удаления связанных данных
        $pdo->beginTransaction();
        
        // Удаляем заявки в этой категории
        $stmt = $pdo->prepare("DELETE FROM applications WHERE category_id = ?");
        $stmt->execute([$category_id]);
        
        // Удаляем саму категорию
        $stmt = $pdo->prepare("DELETE FROM category WHERE category_id = ?");
        $stmt->execute([$category_id]);
        
        $pdo->commit();
        $message = "Категория и связанные заявки успешно удалены";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Ошибка при удалении: " . $e->getMessage();
    }
}

// Получение всех категорий
$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление категориями</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <div class="admin-container">
        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">Главная</a></li>
                <li><a href="applications.php">Заявки</a></li>
                <li class="active"><a href="categories.php">Категории</a></li>
                <li><a href="users.php">Пользователи</a></li>
                <li><a href="../logout.php">Выход</a></li>
            </ul>
        </div>
        <div class="admin-content">
            <!-- Кнопка "Назад" для админа -->
            <div class="admin-back-btn">
                <a href="dashboard.php" class="btn-back">← Назад в админку</a>
            </div>
        <div class="admin-content">
            <h1>Управление категориями</h1>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="admin-form">
                <h2>Добавить новую категорию</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Название категории:</label>
                        <input type="text" name="category_name" required>
                    </div>
                    <button type="submit" name="add_category" class="btn">Добавить</button>
                </form>
            </div>
            
            <h2>Список категорий</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= $category['category_id'] ?></td>
                        <td><?= escape($category['category']) ?></td>
                        <td>
                            <a href="?delete=<?= $category['category_id'] ?>" class="btn btn-danger" onclick="return confirm('Удалить категорию и все связанные заявки?')">Удалить</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>