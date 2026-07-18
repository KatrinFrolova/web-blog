<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Lume</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
<style>
    /* --- Глобальные стили страницы --- */
    body { 
        background-color: #121212; 
        color: #e0e0e0; 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* --- Стили для карточек постов --- */
    .blog-post-card {
        background-color: #1e1e1e;
        border: 1px solid #444;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.5);
        color: #e0e0e0;
    }

    .blog-post-card .post-title {
        color: #ffffff;
        margin-top: 0;
        font-weight: 700;
        font-size: 1.8rem;
        border-bottom: 2px solid #5dade2;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .blog-post-card .post-meta {
        margin-bottom: 20px;
        display: flex;
        gap: 20px;
        font-size: 0.9rem;
    }

    .blog-post-card .post-meta .author-name {
        color: #ffffff;
        font-weight: 600;
    }

    .blog-post-card .post-meta .post-date {
        color: #aaaaaa;
    }

    .blog-post-card .post-body {
        line-height: 1.8;
        color: #dcdcdc;
        white-space: pre-wrap;
    }

    .blog-post-card .post-body p {
        margin-bottom: 15px;
    }

    .form-control { 
        background-color: #2c2c2c; 
        color: white; 
        border-color: #444; 
    }
    .form-control:focus { 
        border-color: #5dade2; 
        box-shadow: 0 0 0 0.2rem rgba(93, 173, 226, 0.25); 
    }

    .nav-link { color: #ccc; }
    .nav-link:hover { color: #fff; text-decoration: underline; }
    
    .btn-dark { background-color: #333; color: white; }
    .btn-dark:hover { background-color: #555; }
</style>

</head>
<body>

<header class="p-3 text-bg-dark">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <strong>Lume</strong>
            </a>

            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                {if $isLoggedIn}
                    <li>
                        <a href="add_post.php" class="nav-link px-2 text-white">Написать пост</a>
                    </li>
                    
                    <!-- Кнопка "Лента подписок" -->
                    <li>
                        <a href="feed.php" class="nav-link px-2 text-white">
                            <i class="bi bi-rss"></i> Лента подписок
                        </a>
                    </li>
                {/if}
            </ul>

            <div class="text-end">
                {if $isLoggedIn}
                    <span class="me-3 text-white">Привет, {$currentUser}</span>
                    <form action="/logout.php" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-outline-light me-2">Выйти</button>
                    </form>
                {else}
                    <a href="/login.php" class="btn btn-outline-light me-2 text-decoration-none">Войти</a>
                    <a href="/register.php" class="btn btn-warning">Регистрация</a>
                {/if}
            </div>
        </div>
    </div>
</header>

<!-- ========================================== -->
<!-- Уведомление об успехе/ошибке -->
<!-- ========================================== -->
{if $msg}
    <div class="container mt-3">
        {if $msg == 'subscribed'}
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>🎉 Отлично!</strong> Вы успешно подписались на автора.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        {elseif $msg == 'unsubscribed'}
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                Вы отписались от автора.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        {else}
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {$msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        {/if}
    </div>
{/if}

{if $dbError}
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Ошибка базы данных: {$dbError}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
{/if}
<!-- ========================================== -->

<!-- Сюда вставляется контент из index.tpl и других страниц -->
{block name="content"}{/block}

<footer class="mt-5 text-center text-body-secondary">
    &copy; 2026 Lume
</footer>

<!-- Bootstrap JS Bundle (нужен для работы alert-dismissible) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

