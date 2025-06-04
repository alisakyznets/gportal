<?php
// Начинаем сессию
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Уничтожаем все данные сессии
$_SESSION = array();

// Уничтожаем сессию
session_destroy();

// Перенаправляем на страницу входа с сообщением
header("Location: login.php?logout=success");
exit;