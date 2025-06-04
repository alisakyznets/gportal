<?php
// Проверка авторизации пользователя
function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_id']);
}

// Проверка роли пользователя
function isAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Редирект с сообщением
function redirect($url, $message = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: $url");
    exit();
}

// Вывод сообщений
function displayMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-info">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
}

// Безопасный вывод данных
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}