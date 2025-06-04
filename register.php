<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('profile.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Валидация данных
    $fio = trim($_POST['fio']);
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Проверка ФИО
    if (!preg_match('/^[а-яёА-ЯЁ\s-]+$/u', $fio)) {
        $errors['fio'] = "ФИО должно содержать только русские буквы, пробелы и дефисы";
    }

    // Проверка логина
    if (!preg_match('/^[a-zA-Z0-9]+$/', $login)) {
        $errors['login'] = "Логин должен содержать только латинские буквы и цифры";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetchColumn() > 0) {
            $errors['login'] = "Этот логин уже занят";
        }
    }

    // Проверка email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Некорректный email";
    }

    // Проверка пароля
    if (strlen($password) < 6) {
        $errors['password'] = "Пароль должен быть не менее 6 символов";
    } elseif ($password !== $password_confirm) {
        $errors['password_confirm'] = "Пароли не совпадают";
    }

    // Регистрация без хеширования пароля
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO users (fio, login, email, password, role) VALUES (?, ?, ?, ?, 'user')");
        $stmt->execute([$fio, $login, $email, $password]); // Пароль хранится открыто

        redirect('login.php', 'Регистрация успешна! Теперь вы можете войти.');
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
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
    <div class="register-container">
        <h1>Создать аккаунт</h1>
        
        <?php displayMessage(); ?>
        
        <form method="POST" novalidate>
            <div class="form-group with-icon user-icon">
                <label>ФИО:</label>
                <input type="text" name="fio" value="<?= isset($fio) ? escape($fio) : '' ?>" required placeholder="Иванов Иван Иванович">
                <?= isset($errors['fio']) ? '<span class="error-message">' . escape($errors['fio']) . '</span>' : '' ?>
            </div>
            
            <div class="form-group with-icon user-icon">
                <label>Логин:</label>
                <input type="text" name="login" value="<?= isset($login) ? escape($login) : '' ?>" required placeholder="Придумайте логин">
                <?= isset($errors['login']) ? '<span class="error-message">' . escape($errors['login']) . '</span>' : '' ?>
            </div>
            
            <div class="form-group with-icon email-icon">
                <label>Email:</label>
                <input type="email" name="email" value="<?= isset($email) ? escape($email) : '' ?>" required placeholder="example@mail.ru">
                <?= isset($errors['email']) ? '<span class="error-message">' . escape($errors['email']) . '</span>' : '' ?>
            </div>
            
            <div class="form-group with-icon password-icon">
                <label>Пароль:</label>
                <div class="password-wrapper">
                    <input type="password" name="password" required placeholder="Не менее 6 символов" id="reg-password">
                    <span class="password-toggle" id="reg-password-toggle">👁️</span>
                </div>
                <?= isset($errors['password']) ? '<span class="error-message">' . escape($errors['password']) . '</span>' : '' ?>
            </div>
            
            <div class="form-group with-icon password-icon">
                <label>Подтвердите пароль:</label>
                <div class="password-wrapper">
                    <input type="password" name="password_confirm" required placeholder="Повторите пароль" id="reg-confirm">
                    <span class="password-toggle" id="reg-confirm-toggle">👁️</span>
                </div>
                <?= isset($errors['password_confirm']) ? '<span class="error-message">' . escape($errors['password_confirm']) . '</span>' : '' ?>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" required checked>
                    Я согласен на обработку персональных данных
                </label>
            </div>
            
            <button type="submit" class="btn">Зарегистрироваться</button>
            
            <div class="form-footer">
                <p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>
            </div>
        </form>
    </div>

    <script>
        // Переключение видимости паролей
        document.getElementById('reg-password-toggle').addEventListener('click', function() {
            const passwordField = document.getElementById('reg-password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
        });

        document.getElementById('reg-confirm-toggle').addEventListener('click', function() {
            const passwordField = document.getElementById('reg-confirm');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '👁️‍🗨️';
        });

        // Валидация паролей в реальном времени
        const password = document.getElementById('reg-password');
        const confirm = document.getElementById('reg-confirm');

        function validatePasswords() {
            if (password.value !== confirm.value) {
                confirm.setCustomValidity("Пароли не совпадают");
            } else {
                confirm.setCustomValidity("");
            }
        }

        password.addEventListener('input', validatePasswords);
        confirm.addEventListener('input', validatePasswords);
    </script>
</body>
</html>