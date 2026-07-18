<?php
require_once __DIR__ . '/../db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: index.php'); 
    exit;
}

// 1. Находим ID и имя тега
$stmt = $pdo->prepare('SELECT id, name FROM tags WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$tag = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Получаем посты (если тег найден)
$posts = [];
$tagName = 'Неизвестный тег';

if ($tag) {
    $tagId = $tag['id'];
    $tagName = $tag['name'];

    $stmt = $pdo->prepare('
        SELECT p.*, u.username AS author_name
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN post_tags pt ON pt.post_id = p.id
        WHERE pt.tag_id = ?
        ORDER BY p.created_at DESC
    ');
    $stmt->execute([$tagId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статьи по тегу: <?= htmlspecialchars($tagName) ?></title>
    <style>
        body { font-family: sans-serif; max-width: 1000px; margin: 40px auto; padding: 0 20px; color: #333; }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #f4f4f4;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .back-link:hover {
            background-color: #e0e0e0;
        }

        h1 { border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .post-card { border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .post-title { color: #2c3e50; margin-top: 0; }
        .post-meta { color: #7f8c8d; font-size: 0.9em; margin-bottom: 15px; }
        .post-content { color: #555; margin-bottom: 15px; line-height: 1.6; }
        .no-posts { color: #999; font-style: italic; }
        .error-message { color: #d9534f; background: #f9f2f4; padding: 15px; border-radius: 4px; }
    </style>
</head>
<body>
    <!-- Кнопка возврата на главную -->
    <a href="index.php" class="back-link">&larr; На главную</a>

    <?php if (!$tag): ?>
        <!-- Красивый вывод ошибки, если тега нет -->
        <div class="error-message">
            <h1>Тег не найден</h1>
            <p>К сожалению, статей с таким тегом не существует. Вернитесь на <a href="index.php"><strong>главную страницу</strong></a>.</p>
        </div>
    <?php else: ?>
        <h1>Статьи по тегу «<?= htmlspecialchars($tagName) ?>»</h1>

        <?php if (count($posts) === 0): ?>
            <p class="no-posts">По этому тегу пока нет ни одной статьи.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <article class="post-card">
                    <h2 class="post-title"><?= htmlspecialchars($post['title']) ?></h2>
                    <div class="post-meta">
                        Автор: <?= htmlspecialchars($post['author_name']) ?> • 
                        Дата: <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                    </div>
                    
                    <!-- ВЫВОД ПОЛНОГО ТЕКСТА БЕЗ ОБРЕЗКИ -->
                    <div class="post-content">
                        <?= htmlspecialchars($post['content']) ?>
                    </div>
                
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>

