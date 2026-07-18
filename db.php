<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Настройки БД 
$host = '127.0.1.29';
$port = 3306;
$db   = 'blog_db';
$user = 'root';
$pass = ''; 

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES    => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Если не можем подключиться — показываем понятную ошибку и останавливаемся
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// 3. Логика авторизации
$currentUserId = $_SESSION['user_id'] ?? null;
$isLoggedIn = !empty($currentUserId);
$currentUser = null;

if ($isLoggedIn) {
    try {
        $stmt = $pdo->prepare('SELECT id, username FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$currentUserId]);
        $currentUser = $stmt->fetch();
        
        if (!$currentUser) {
            // Если пользователя нет в БД (удалили), сбрасываем сессию
            unset($_SESSION['user_id']);
            unset($_SESSION['username']);
            $isLoggedIn = false;
            $currentUserId = null;
        }
    } catch (PDOException $e) {
        error_log('Ошибка получения данных пользователя: ' . $e->getMessage());
        $isLoggedIn = false;
        $currentUserId = null;
    }
}

