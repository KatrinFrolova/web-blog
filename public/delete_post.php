<?php
session_start();

// Для отладки ошибок 
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../db.php';

// 1. Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=session_expired');
    exit;
}

$currentUserId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

    if (!$postId) {
        header('Location: index.php?error=Неверный ID поста');
        exit;
    }

    try {
        // 2. Получаем пост и проверяем, принадлежит ли он текущему пользователю
        $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$post) {
            header('Location: index.php?error=Пост не найден');
            exit;
        }

        // 3. сравниваем ID
        if ($post['user_id'] != $currentUserId) {
            header('Location: index.php?error=Вы не можете удалять чужие посты');
            exit;
        }

        // 4. Удаляем пост
        $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        if ($deleteStmt->execute([$postId])) {
            header('Location: index.php?msg=Пост успешно удалён');
            exit;
        } else {
            header('Location: index.php?error=Ошибка базы данных при удалении');
            exit;
        }

    } catch (Exception $e) {
        header('Location: index.php?error=Техническая ошибка: ' . $e->getMessage());
        exit;
    }
}

header('Location: index.php');
exit;
?>
