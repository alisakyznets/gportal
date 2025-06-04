<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Если пользователь уже авторизован, перенаправляем
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/dashboard.php' : 'profile.php');
}

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    // Поиск пользователя в БД
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    // Проверка пароля
    if ($user && $password === $user['password']) {
        // Успешная авторизация
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['fio'] = $user['fio'];
        
        redirect(isAdmin() ? 'admin/dashboard.php' : 'profile.php', 'Добро пожаловать!');
    } else {
        $error = "Неверный логин или пароль";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Вход в систему</h1>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= escape($error) ?></div>
        <?php endif; ?>
        
        <?php 
        // Показываем сообщение о выходе, если оно есть
        if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
            echo '<div class="alert alert-info">Вы успешно вышли из системы</div>';
        }
        ?>
        
        <form method="POST">
            <div class="form-group with-icon user-icon">
                <label>Логин:</label>
                <input type="text" name="login" required placeholder="Введите ваш логин">
            </div>
            
            <div class="form-group with-icon password-icon">
                <label>Пароль:</label>
                <div class="password-wrapper">
                    <input type="password" name="password" required placeholder="Введите ваш пароль" id="password-field">
                    <span class="password-toggle" id="password-toggle">👁️</span>
                </div>
            </div>
            
            <button type="submit" class="btn">Войти</button>
            
            <div class="form-footer">
                <p>Еще нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
            </div>
        </form>
    </div>

    <script>
        // Переключение видимости пароля
        document.getElementById('password-toggle').addEventListener('click', function() {
            const passwordField = document.getElementById('password-field');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
        });
    </script>
</body>
</html>