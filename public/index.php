<?php

require __DIR__ . '/../db.php';

$rootDir = realpath(__DIR__ . '/..');
$smartyLibPath = $rootDir . '/smarty/libs/Smarty.class.php';

if (!file_exists($smartyLibPath)) {
    die("Smarty не найден. Проверьте папку smarty рядом с public.");
}

require $smartyLibPath;

$smarty = new Smarty();
$smarty->template_dir = $rootDir . '/templates';
$smarty->compile_dir = $rootDir . '/templates_c';
$smarty->cache_dir = $rootDir . '/cache';
$smarty->config_dir = $rootDir . '/configs';
$smarty->debugging = false;
$smarty->caching = false;

// --- Параметры запроса ---
$isFeedMode = isset($_GET['mode']) && $_GET['mode'] === 'feed';
$filterTagSlug = isset($_GET['tag']) ? trim($_GET['tag']) : null;

$isLoggedIn = isset($_SESSION['user_id']);
$currentUserId = $isLoggedIn ? (int)$_SESSION['user_id'] : null;
$currentUser = $isLoggedIn ? ($_SESSION['username'] ?? null) : null;

$posts = [];
$dbError = null;

try {
    if ($filterTagSlug) {
        // Сначала получаем ID тега по slug
        $stmtTag = $pdo->prepare('SELECT id FROM tags WHERE slug = ? LIMIT 1');
        $stmtTag->execute([$filterTagSlug]);
        $tagRow = $stmtTag->fetch(PDO::FETCH_ASSOC);

        if (!$tagRow) {
            $posts = [];
        } else {
            $tagId = (int)$tagRow['id'];

            if ($isLoggedIn) {
                $stmt = $pdo->prepare("
                    SELECT p.*, u.username AS author_name, s.id AS subscription_id
                    FROM posts p
                    JOIN users u ON p.user_id = u.id
                    LEFT JOIN subscriptions s ON s.subscriber_id = :user_id AND s.author_id = u.id
                    JOIN post_tags pt ON pt.post_id = p.id AND pt.tag_id = :tag_id
                    ORDER BY p.created_at DESC
                ");
                $stmt->execute([':user_id' => $currentUserId, ':tag_id' => $tagId]);
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $stmt = $pdo->query("
                    SELECT p.*, u.username AS author_name
                    FROM posts p
                    JOIN users u ON p.user_id = u.id
                    JOIN post_tags pt ON pt.post_id = p.id AND pt.tag_id = (SELECT id FROM tags WHERE slug = ?)
                    ORDER BY p.created_at DESC
                ");
                $stmt->execute([$filterTagSlug]);
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    } elseif ($isFeedMode) {
        // РЕЖИМ ЛЕНТЫ ПОДПИСОК 
        if (!$isLoggedIn) {
            $posts = [];
        } else {
            $stmt = $pdo->prepare("
                SELECT p.*, u.username AS author_name, s.id AS subscription_id
                FROM posts p
                JOIN users u ON p.user_id = u.id
                JOIN subscriptions s ON s.author_id = u.id
                WHERE s.subscriber_id = :user_id
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([':user_id' => $currentUserId]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        // ОБЫЧНЫЙ РЕЖИМ (ГЛАВНАЯ)
        if ($isLoggedIn) {
            $stmt = $pdo->prepare("
                SELECT p.*, u.username AS author_name, s.id AS subscription_id
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN subscriptions s ON s.subscriber_id = :user_id AND s.author_id = u.id
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([':user_id' => $currentUserId]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->query("
                SELECT p.*, u.username AS author_name
                FROM posts p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC
            ");
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    // --- Подгрузка тегов для каждого поста ---
    foreach ($posts as &$post) {
        $post['tags'] = []; 
        $stmtTags = $pdo->prepare('
            SELECT t.*
            FROM tags t
            JOIN post_tags pt ON pt.tag_id = t.id
            WHERE pt.post_id = ?
            ORDER BY t.name
        ');
        $stmtTags->execute([(int)$post['id']]);
        $post['tags'] = $stmtTags->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($post);

} catch (PDOException $e) {
    $dbError = $e->getMessage();
    $posts = [];
}

// --- ОБРАБОТКА СООБЩЕНИЙ ---
$msgText = null;

// 1. Сообщение об успешной регистрации 
if (isset($_GET['msg']) && $_GET['msg'] === 'success') {
    $msgText = 'Вы успешно зарегистрированы! Теперь можете войти.';
}

// 2. Сообщение об успешной подписке 
if (isset($_GET['msg']) && $_GET['msg'] === 'subscribed') {
    $msgText = 'Вы успешно подписаны!';
}

if ($msgText) {
    $smarty->assign('msg', $msgText);
}

if ($dbError) {
    $smarty->assign('dbError', $dbError);
}

$smarty->assign('posts', $posts);
$smarty->assign('isLoggedIn', $isLoggedIn);
$smarty->assign('currentUser', $currentUser);
$smarty->assign('currentUserId', $currentUserId);
$smarty->assign('isFeedMode', $isFeedMode);
$smarty->assign('filterTagSlug', $filterTagSlug);

$smarty->display('index.tpl');
?>

