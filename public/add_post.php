<?php

require __DIR__ . '/../db.php';
session_start();

$rootDir = realpath(__DIR__ . '/..');
if (!$rootDir) {
    die('Ошибка: не удалось определить корень проекта.');
}

$smartyLibPath = $rootDir . '/smarty/libs/Smarty.class.php';
if (!file_exists($smartyLibPath)) {
    die('Ошибка: файл Smarty.class.php не найден по пути: ' . $smartyLibPath);
}
require $smartyLibPath;

$smarty = new Smarty();
$smarty->template_dir = $rootDir . '/templates';
$smarty->compile_dir = $rootDir . '/templates_c';
$smarty->cache_dir = $rootDir . '/cache';
$smarty->config_dir = $rootDir . '/configs';
$smarty->debugging = false;
$smarty->caching = false;

// Проверка авторизации
$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header('Location: /auth.php');
    exit;
}

// Переменные для передачи в шаблон
$msg = '';
$allTags = [];
$selectedTags = []; // Инициализируем как пустой массив

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_post') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    // Гарантируем, что selectedTags всегда массив
    $selectedTags = is_array($_POST['tags']) ? $_POST['tags'] : [];

    if (empty($title) || empty($content)) {
        $msg = 'Заполните заголовок и текст поста.';
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Сохраняем пост
            $stmt = $pdo->prepare('INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$_SESSION['user_id'], $title, $content]);
            $postId = (int)$pdo->lastInsertId();

            // 2. Привязываем теги
            if (!empty($selectedTags)) {
                foreach ($selectedTags as $tagId) {
                    $tagId = (int)$tagId;
                    if ($tagId > 0) {
                        $stmtTag = $pdo->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
                        $stmtTag->execute([$postId, $tagId]);
                    }
                }
            }

            $pdo->commit();
            header('Location: /?msg=post_added');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $msg = 'Ошибка базы данных: ' . htmlspecialchars($e->getMessage());
        }
    }
}

// Получаем все теги
try {
    $stmtTags = $pdo->query('SELECT * FROM tags ORDER BY name');
    $allTags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $msg .= ' Ошибка при загрузке тегов: ' . htmlspecialchars($e->getMessage()) . ' Проверьте, что таблица tags создана.';
}

// Передаем данные в шаблон
$smarty->assign('title', 'Написать пост');
$smarty->assign('msg', $msg);
$smarty->assign('isLoggedIn', $isLoggedIn);
$smarty->assign('currentUser', $_SESSION['username'] ?? null);
$smarty->assign('allTags', $allTags);
$smarty->assign('selectedTags', $selectedTags); 

try {
    $smarty->display('add_post.tpl');
} catch (Exception $e) {
    echo '<h2 style="color:red">Ошибка шаблона Smarty</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>Проверьте, что файл <code>templates/add_post.tpl</code> существует.</p>';
}
?>

