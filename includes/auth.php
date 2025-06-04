<?php
/**
 * Файл для проверки авторизации пользователя
 */

// Старт сессии, если еще не начата
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Проверяет, авторизован ли пользователь
 * @return bool Возвращает true, если пользователь авторизован
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Проверяет, является ли пользователь администратором
 * @return bool Возвращает true, если пользователь администратор
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Перенаправляет пользователя на указанную страницу
 * @param string $url URL для перенаправления
 * @param string|null $message Сообщение для отображения (сохраняется в сессии)
 */
function redirect($url, $message = null) {
    if ($message) {
        $_SESSION['message'] = $message;
    }
    header("Location: $url");
    exit();
}