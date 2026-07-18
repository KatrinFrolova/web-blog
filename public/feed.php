<?php
// feed.php

// 1. Определяем корень проекта 
$rootDir = realpath(__DIR__ . '/..');
if (!$rootDir) {
    die('Ошибка: не удалось определить корень проекта.');
}

// 2. Подключаем БД и общую логику 
require $rootDir . '/db.php';

// 3. Подключаем библиотеку Smarty 
$smartyLibPath = $rootDir . '/smarty/libs/Smarty.class.php';
if (!file_exists($smartyLibPath)) {
    die('Ошибка: файл Smarty.class.php не найден по пути: ' . $smartyLibPath);
}
require $smartyLibPath;

// 4. Инициализируем Smarty
$smarty = new Smarty();
$smarty->template_dir = $rootDir . '/templates';
$smarty->compile_dir  = $rootDir . '/templates_c';
$smarty->cache_dir    = $rootDir . '/cache';
$smarty->config_dir   = $rootDir . '/configs';
$smarty->debugging    = false;
$smarty->caching      = false;

// 5. Проверка авторизации
// Если пользователь не залогинен ($currentUserId не установлен в db.php), кидаем на главную
if (!$currentUserId) {
    header('Location: index.php');
    exit;
}

// 6. Формируем ленту подписок
// Выбираем посты авторов, на которых подписан текущий пользователь
$stmt = $pdo->prepare("
    SELECT p.*, u.username AS author_name
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN subscriptions s ON s.author_id = u.id
    WHERE s.subscriber_id = :user_id
    ORDER BY p.created_at DESC
");

try {
    $stmt->execute([':user_id' => $currentUserId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Если ошибка в запросе (например, нет таблицы subscriptions), показываем ошибку, а не ломаем сайт
    error_log('Ошибка в feed.php: ' . $e->getMessage());
    $posts = [];
    $error = 'Не удалось загрузить ленту подписок. Проверьте наличие таблиц в БД.';
}

// 7. Передаем данные в шаблон
$smarty->assign('posts', $posts ?? []);
$smarty->assign('isLoggedIn', true);
$smarty->assign('currentUserId', $currentUserId);

// Если была ошибка БД, передадим её в шаблон (опционально)
if (isset($error)) {
    $smarty->assign('error', $error);
}

// 8. Отображаем шаблон
// Используем index.tpl, так как лента подписок по сути та же лента постов, только фильтрованная
$smarty->display('index.tpl');
?>
