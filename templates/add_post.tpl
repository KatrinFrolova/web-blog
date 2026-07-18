<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body { background-color: #121212; color: #e0e0e0; }
        .container { margin-top: 40px; }

        .form-control, .form-select { 
            background-color: #1e1e1e; 
            color: #fff; 
            border-color: #333; 
        }
        .form-control:focus, .form-select:focus {
            border-color: #5dade2;
            box-shadow: 0 0 0 0.2rem rgba(93, 173, 226, 0.25);
        }

        .btn-primary { 
            background-color: #FFD700; 
            border-color: #FFD700; 
            color: #000; 
        }
        .btn-primary:hover { 
            background-color: #FFD100; 
            border-color: #FFD100; 
        }

        .tags-section {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #333;
            margin-bottom: 20px;
        }
        .tags-section h4 {
            margin-top: 0;
            color: #ccc;
            font-size: 1rem;
            border-bottom: 1px solid #444;
            padding-bottom: 10px;
        }
        .form-check-input:checked {
            accent-color: #FFD700;
        }
        .form-check-label {
            color: #ddd;
        }
        .form-check-label:hover {
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-dark text-white">

<div class="container">
    <h2 class="mb-4">{$title}</h2>

    {if $msg}
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {$msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    {/if}

    <form method="POST" action="">
        <input type="hidden" name="action" value="add_post">

        <div class="mb-3">
            <label for="title" class="form-label">Заголовок поста</label>
            <input type="text" class="form-control" id="title" name="title" required placeholder="Например: Мой первый пост">
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Текст поста</label>
            <textarea class="form-control" id="content" name="content" rows="6" required placeholder="Напишите содержание поста..."></textarea>
        </div>

        {* --- БЛОК ТЕГОВ --- *}
        <div class="tags-section">
            <h4><i class="bi bi-tag me-1"></i>Выберите теги для поста</h4>
            
            {if $allTags}
                <div class="row g-2">
                    {foreach from=$allTags item=tag}
                        <div class="col-md-4 col-sm-6">
                            <div class="form-check">
                                {*  проверка наличия тега *}
                                {if !empty($selectedTags) && in_array($tag.id, $selectedTags)}
                                    <input class="form-check-input" type="checkbox" name="tags[]" value="{$tag.id}" id="tag_{$tag.id}" checked>
                                {else}
                                    <input class="form-check-input" type="checkbox" name="tags[]" value="{$tag.id}" id="tag_{$tag.id}">
                                {/if}
                                <label class="form-check-label" for="tag_{$tag.id}">{$tag.name}</label>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {else}
                <p class="text-muted small">Теги пока не загружены. Проверьте таблицу tags в базе данных.</p>
            {/if}
        </div>

        <button type="submit" class="btn btn-primary">Опубликовать пост</button>
        <a href="/" class="btn btn-outline-light ms-2">Отмена</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
