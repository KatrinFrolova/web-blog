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

// Получаем ID поста из URL (?id=1)
$postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$postId) {
    header('Location: /');
    exit;
}

$post = null;
$comments = [];
$error = '';

// --- 1. Логика добавления комментария ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $authorName = trim($_POST['author_name'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($authorName && $content) {
        try {
            // Вставляем комментарий
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, author_name, content) VALUES (?, ?, ?)");
            if ($stmt->execute([$postId, $authorName, $content])) {
                // Редирект на ту же страницу, чтобы избежать повторной отправки формы при обновлении
                header('Location: view_post.php?id=' . $postId . '#comments');
                exit;
            } else {
                $error = 'Не удалось сохранить комментарий.';
            }
        } catch (PDOException $e) {
            $error = 'Ошибка базы данных: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = 'Заполните имя и текст комментария.';
    }
}

// --- 2. Получаем пост, теги и комментарии ---
try {
    // Ищем пост
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: /?error=post_not_found');
        exit;
    }

    // --- ПОДГРУЗКА ТЕГОВ ДЛЯ ПОСТА ---
    $post['tags'] = []; // Инициализируем массив
    $tagStmt = $pdo->prepare('
        SELECT t.*
        FROM tags t
        JOIN post_tags pt ON pt.tag_id = t.id
        WHERE pt.post_id = ?
        ORDER BY t.name
    ');
    $tagStmt->execute([(int)$post['id']]);
    $post['tags'] = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
    // ---------------------------------

    // Ищем комментарии к этому посту (новые сверху)
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
    $stmt->execute([$postId]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = 'Ошибка загрузки данных: ' . htmlspecialchars($e->getMessage());
}

// Передаем данные в шаблон
$smarty->assign('post', $post);
$smarty->assign('comments', $comments);
$smarty->assign('error', $error);
$smarty->assign('isLoggedIn', isset($_SESSION['user_id']));

$smarty->display('view_post.tpl');
?>
