<?php
// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Настройки подключения к базе данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'gportal');
define('DB_USER', 'root');
define('DB_PASS', '');

// Инициализация сессии только если она еще не начата
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Глобальная переменная для подключения к БД
global $pdo;

// Подключение к БД с обработкой ошибок
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Автозагрузка классов (если будем использовать ООП)
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});