{extends file="layout.tpl"}

{block name="content"}
    <div class="container mt-5">
        
        {if $dbError}
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Ошибка БД: {$dbError}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        {/if}

        {* Панель фильтрации по тегам *}
        {if $filterTagSlug}
            <div class="mb-4 p-3 bg-secondary bg-opacity-25 border rounded">
                <h5 class="fw-bold text-white">
                    <i class="bi bi-tag me-2"></i>Фильтр: по тегу «{$filterTagSlug|escape}»
                </h5>
                <a href="/" class="btn btn-sm btn-outline-light">← Все посты</a>
            </div>
        {/if}

        {if $posts}
            {foreach from=$posts item=post}
                <article class="blog-post-card mb-4">
                    <header class="post-header">
                        <h2 class="post-title">
                            <a href="view_post.php?id={$post.id}" class="text-white text-decoration-none">
                                {$post.title|escape}
                            </a>
                        </h2>
                        
                        <div class="post-meta d-flex flex-wrap align-items-center gap-3">
                            <span>
                                <i class="bi bi-person"></i> Автор: <strong class="author-name">{$post.author_name|default:"Аноним"|escape}</strong>
                            </span>
                            <span class="post-date">
                                <i class="bi bi-clock"></i> Дата: {$post.created_at|date_format:"%d.%m.%Y %H:%M"}
                            </span>

                            {* Блок тегов *}
                            {if $post.tags}
                                <div class="d-flex flex-wrap gap-1 mt-1 mt-md-0 align-items-center">
                                    {foreach from=$post.tags item=tag}
                                        <a href="tag.php?slug={$tag.slug}" 
                                           class="badge bg-secondary text-white me-1"
                                           title="Все посты по тегу «{$tag.name|escape}»">
                                            {$tag.name|escape}
                                        </a>
                                    {/foreach}
                                </div>
                            {/if}

                            <!-- БЛОК КНОПОК -->
                            <div class="ms-auto d-flex gap-2 align-items-center flex-wrap flex-md-nowrap">
                                
                                {* КНОПКА ПОДПИСКИ (только для залогиненных) *}
                                {if $isLoggedIn}
                                    {* ПРОВЕРКА: Если пользователь уже подписан (поле subscription_id не NULL) *}
                                    {if $post.subscription_id}
                                        <button type="button" class="btn btn-sm btn-success" disabled title="Вы уже подписаны">
                                            <i class="bi bi-check-circle-fill"></i> Подписан
                                        </button>
                                    {else}
                                        {* ПРОВЕРКА: Если это НЕ автор поста (сравнение по ID) *}
                                        {if $post.user_id != $currentUserId}
                                            <form action="/subscribe.php" method="POST" class="d-inline">
                                                <input type="hidden" name="author_id" value="{$post.user_id}">
                                                <input type="hidden" name="action" value="subscribe">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-plus-lg"></i> Подписаться
                                                </button>
                                            </form>
                                        {else}
                                            {* Если это автор поста — показываем заглушку *}
                                            <span class="btn btn-sm btn-secondary" style="cursor: not-allowed;" title="Вы не можете подписаться на себя">
                                                <i class="bi bi-person-slash"></i> Вы автор
                                            </span>
                                        {/if}
                                    {/if}
                                {else}
                                    {* Если не залогинен — подсказка *}
                                    <span class="badge bg-secondary text-dark" style="font-size: 0.8rem;" title="Войдите, чтобы подписаться">
                                        <i class="bi bi-lock me-1"></i> Войдите для подписки
                                    </span>
                                {/if}

                                {* КНОПКА КОММЕНТИРОВАТЬ (видна всем) *}
                                <a href="view_post.php?id={$post.id}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-chat-dots"></i> Комментировать
                                </a>

                                {* КНОПКИ РЕДАКТИРОВАНИЯ И УДАЛЕНИЯ (только для автора) *}
                                {if $isLoggedIn && $currentUserId == $post.user_id}
                                    <a href="edit_post.php?id={$post.id}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                    <form action="delete_post.php" method="POST" style="display:inline;" onsubmit="return confirm('Вы уверены, что хотите удалить этот пост?');">
                                        <input type="hidden" name="post_id" value="{$post.id}">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Удалить
                                        </button>
                                    </form>
                                {/if}
                            </div>
                        </div>
                    </header>
                    
                    <hr class="my-3" style="border-color: #555;">
                    
                    <div class="post-body">
                        {$post.content|escape}
                    </div>
                </article>
            {/foreach}
        {else}
            <p class="text-muted">
                {if $filterTagSlug}
                    По тегу «{$filterTagSlug|escape}» пока нет постов.
                {elseif $isFeedMode}
                    Пока нет постов от авторов, на которых вы подписаны.
                {else}
                    Пока нет постов для отображения.
                {/if}
            </p>
        {/if}
    </div>
{/block}
