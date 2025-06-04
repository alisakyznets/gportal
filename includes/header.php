<?php
if (!defined('HEADER_INCLUDED')) {
    define('HEADER_INCLUDED', true);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Городской портал</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="/">Городской портал</a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="/">Главная</a></li>
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin() && basename($_SERVER['PHP_SELF']) !== 'dashboard.php'): ?>
                        <?php endif; ?>
                        <li><a href="../profile.php">Личный кабинет</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin/dashboard.php">Админ-панель</a></li>
                        <?php endif; ?>
                        <li><a href="../logout.php">Выйти</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Вход</a></li>
                        <li><a href="register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
<?php } ?>