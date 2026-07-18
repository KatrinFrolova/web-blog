<?php
session_start();

// --- 1. Поиск файла Smarty.class.php ---
$possiblePaths = [
    __DIR__ . '/../smarty/libs/Smarty.class.php',
    __DIR__ . '/../smarty/Smarty.class.php',
    __DIR__ . '/../libs/Smarty.class.php'
];

$smartyFile = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $smartyFile = $path;
        break;
    }
}

if (!$smartyFile) {
    echo "<h2>❌ Ошибка: Файл Smarty.class.php не найден!</h2>";
    echo "<p>Проверь пути:</p><ul>";
    foreach ($possiblePaths as $p) {
        $status = file_exists($p) ? "✅" : "❌";
        echo "<li>" . htmlspecialchars($p) . " <b>($status)</b></li>";
    }
    echo "</ul>";
    exit;
}

require_once $smartyFile;
require __DIR__ . '/../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUserId = $_SESSION['user_id'];
$msg = '';
$error = '';

// Инициализируем переменные пустыми значениями по умолчанию
$postData = ['id' => 0, 'title' => '', 'content' => ''];
$allTags = [];
$selectedTags = []; 

// --- 2. Логика формы (сохранение и загрузка) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    // Безопасное получение массива тегов из POST
    $selectedTagsInput = isset($_POST['tags']) && is_array($_POST['tags']) ? $_POST['tags'] : [];

    if ($postId && $title && $content) {
        try {
            // Проверка прав: только автор может редактировать
            $checkStmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
            $checkStmt->execute([$postId]);
            $author = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if (!$author || $author['user_id'] != $currentUserId) {
                $error = 'Доступ запрещён: вы не являетесь автором этого поста.';
            } else {
                // Транзакция: обновляем пост + теги
                $pdo->beginTransaction();

                // Обновляем сам пост
                $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
                if (!$stmt->execute([$title, $content, $postId])) {
                    throw new Exception('Ошибка обновления данных поста');
                }

                // Удаляем старые связи тегов
                $deleteTagsStmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
                $deleteTagsStmt->execute([$postId]);

                // Добавляем новые выбранные теги
                if (!empty($selectedTagsInput)) {
                    $insertTagStmt = $pdo->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
                    foreach ($selectedTagsInput as $tagId) {
                        $tagId = (int)$tagId;
                        if ($tagId > 0) {
                            $insertTagStmt->execute([$postId, $tagId]);
                        }
                    }
                }

                $pdo->commit();
                header('Location: index.php?msg=Пост успешно обновлён');
                exit;
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Ошибка базы данных: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = 'Заполните заголовок и текст поста.';
    }
} 
elseif (isset($_GET['id'])) {
    $postId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if ($postId) {
        try {
            // Загружаем пост
            $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
            $stmt->execute([$postId]);
            $postData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Проверка существования и прав доступа
            if (!$postData || $postData['user_id'] != $currentUserId) {
                header('Location: index.php?error=Нет доступа к этому посту');
                exit;
            }

            // Загружаем ВСЕ теги для отображения в чекбоксах
            $tagsStmt = $pdo->query("SELECT * FROM tags ORDER BY name");
            $allTags = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);

            // Загружаем ID тегов, уже привязанных к этому посту
            $selectedStmt = $pdo->prepare("SELECT tag_id FROM post_tags WHERE post_id = ?");
            $selectedStmt->execute([$postId]);
            $rows = $selectedStmt->fetchAll(PDO::FETCH_COLUMN);
            
            // $selectedTags всегда будет массивом
            $selectedTags = is_array($rows) ? $rows : [];

        } catch (PDOException $e) {
            header('Location: index.php?error=' . htmlspecialchars($e->getMessage()));
            exit;
        }
    } else {
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}

// --- 3. Настройка путей и очистка кэша Smarty ---
$templateDir = __DIR__ . '/../templates';
$compileDir  = __DIR__ . '/../templates_c';

if (!is_dir($templateDir)) {
    die("❌ Ошибка: Папка шаблонов не найдена! Путь: " . $templateDir);
}

if (!is_dir($compileDir)) {
    if (!mkdir($compileDir, 0777, true)) {
        die("❌ Ошибка: Не удалось создать папку кэша: " . $compileDir);
    }
} elseif (!is_writable($compileDir)) {
    die("❌ Ошибка: Нет прав на запись в папку кэша: " . $compileDir);
}

$files = glob($compileDir . '/*'); 
foreach ($files as $file) {
    if (is_file($file) && strpos($file, 'edit_post') !== false) {
        unlink($file);
    }
}

// Инициализация Smarty
$smarty = new Smarty();
$smarty->template_dir = $templateDir;
$smarty->compile_dir  = $compileDir;
$smarty->debugging = false; // В продакшене лучше выключить
$smarty->caching = false;

// Передача данных в шаблон
$smarty->assign('msg', $msg);
$smarty->assign('error', $error);
$smarty->assign('post', $postData);
$smarty->assign('allTags', $allTags);
$smarty->assign('selectedTags', $selectedTags); // Сюда гарантированно придет массив []

$smarty->display('edit_post.tpl');
?>
