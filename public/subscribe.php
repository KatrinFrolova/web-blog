<?php
session_start();

require_once __DIR__ . '/../db.php'; 

// корректно получаем ID текущего пользователя
$currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Проверка авторизации
if (!$currentUserId || $currentUserId <= 0) {
    header('Location: /login.php'); // Лучше вести на страницу входа, а не auth.php, если такой нет
    exit;
}

// Получение ID автора
$authorId = filter_input(INPUT_POST, 'author_id', FILTER_VALIDATE_INT);

// Валидация
if (!$authorId || $authorId <= 0 || $authorId == $currentUserId) {
    // Защита от подписки на себя или неверных данных
    $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header('Location: ' . $referer);
    exit;
}

try {
    $pdo->beginTransaction();

    // Проверка: есть ли уже подписка
    $checkStmt = $pdo->prepare('SELECT id FROM subscriptions WHERE subscriber_id = ? AND author_id = ? LIMIT 1');
    $checkStmt->execute([$currentUserId, $authorId]);
    
    if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
        // Уже подписан — просто возвращаемся назад без сообщения 
        $pdo->commit();
        $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
        header('Location: ' . $referer);
        exit;
    }

    // Создаём подписку
    $insertStmt = $pdo->prepare('INSERT INTO subscriptions (subscriber_id, author_id, subscribed_at) VALUES (?, ?, NOW())');
    $insertStmt->execute([$currentUserId, $authorId]);

    $pdo->commit();

    // Формируем URL с параметром msg=subscribed
    $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    
    $query = parse_url($referer, PHP_URL_QUERY);
    $base = strtok($referer, '?'); 
    
    if (empty($query)) {
        $newUrl = $base . '?msg=subscribed';
    } else {
        parse_str($query, $params);
        $params['msg'] = 'subscribed';
        $newUrl = $base . '?' . http_build_query($params);
    }

    header('Location: ' . $newUrl);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Subscription error: ' . $e->getMessage());
    
    // При ошибке тоже возвращаем назад
    $referer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header('Location: ' . $referer);
    exit;
}
?>
