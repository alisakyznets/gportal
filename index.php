<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Проверяем подключение к БД
if (!isset($pdo)) {
    die("Ошибка: Не удалось подключиться к базе данных");
}

// Получаем последние решенные заявки для блока "до/после"
$solvedApplications = $pdo->query("
    SELECT a.title, a.description, a.path_to_image_before, a.path_to_image_after, c.category 
    FROM applications a
    JOIN category c ON a.category_id = c.category_id
    WHERE a.status_id = 1 AND a.path_to_image_after IS NOT NULL
    ORDER BY a.created_at DESC
    LIMIT 4
")->fetchAll();

// Получаем общую статистику
$stats = [
    'solved' => $pdo->query("SELECT COUNT(*) FROM applications WHERE status_id = 1")->fetchColumn(),
    'pending' => $pdo->query("SELECT COUNT(*) FROM applications WHERE status_id = 3")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM category")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Городской портал - Главная</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <main class="main-content">
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Сделаем наш город лучше вместе!</h1>
                    <p>Сообщайте о проблемах, участвуйте в обсуждениях, отслеживайте изменения</p>
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-large">Присоединиться</a>
                    <?php else: ?>
                        <a href="create-application.php" class="btn btn-large">Создать заявку</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="stats">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= $stats['solved'] ?></div>
                        <div class="stat-label">Решённых проблем</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $stats['pending'] ?></div>
                        <div class="stat-label">Заявок в работе</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $stats['categories'] ?></div>
                        <div class="stat-label">Категорий проблем</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= $stats['users'] ?></div>
                        <div class="stat-label">Активных участников</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="before-after">
            <div class="container">
                <h2>Последние решённые проблемы</h2>
                <div class="ba-grid">
                    <?php foreach ($solvedApplications as $app): ?>
                    <div class="ba-item">
                        <div class="ba-images">
                            <div class="ba-before">
                                <img src="<?= escape($app['path_to_image_before']) ?>" alt="До">
                                <div class="ba-label">До</div>
                            </div>
                            <div class="ba-after">
                                <img src="<?= escape($app['path_to_image_after']) ?>" alt="После">
                                <div class="ba-label">После</div>
                            </div>
                        </div>
                        <div class="ba-details">
                            <h3><?= escape($app['title']) ?></h3>
                            <p><?= escape($app['description']) ?></p>
                            <div class="ba-category"><?= escape($app['category']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="how-it-works">
            <div class="container">
                <h2>Как это работает?</h2>
                <div class="steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h3>Зарегистрируйтесь</h3>
                        <p>Создайте аккаунт, чтобы получить доступ ко всем возможностям портала</p>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h3>Сообщите о проблеме</h3>
                        <p>Опишите проблему, добавьте фотографию и укажите местоположение</p>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h3>Отслеживайте статус</h3>
                        <p>Следите за ходом решения вашей заявки в личном кабинете</p>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <h3>Увидьте результат</h3>
                        <p>Получайте уведомления о решении проблемы и смотрите фото "до и после"</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3>Городской портал</h3>
                    <p>Платформа для взаимодействия жителей и администрации</p>
                </div>
                <div class="footer-links">
                    <h4>Быстрые ссылки</h4>
                    <ul>
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="login.php">Войти</a></li>
                        <li><a href="register.php">Регистрация</a></li>
                        <li><a href="create-application.php">Создать заявку</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4>Контакты</h4>
                    <p>Email: alisa.kuznetsova.05@bk.ru</p>
                    <p>Телефон: +7 (977) 451-13-86</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Городской портал.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>