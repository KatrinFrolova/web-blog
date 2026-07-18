{extends file="layout.tpl"}

{block name="content"}
<div class="container py-4">
    <!-- Карточка поста -->
    <article class="blog-post-card bg-dark text-white border-0 shadow-sm">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
            <div>
                <h1 class="post-title fw-bold mb-1">{$post.title|escape}</h1>
                
                <div class="post-meta d-flex flex-wrap gap-3 align-items-center mb-2 text-muted small">
                    <span class="author-name">
                        <i class="bi bi-person me-1"></i>Автор: <strong class="text-white">{$post.author_name|default:"Аноним"|escape}</strong>
                    </span>
                    <span class="post-date">
                        <i class="bi bi-clock me-1"></i>Дата: {$post.created_at|date_format:"%d.%m.%Y %H:%M"}
                    </span>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- БЛОК ДЕЙСТВИЙ: Подписка / Редактирование / Удаление -->
            <!-- ========================================== -->
            <div class="ms-auto d-flex gap-2 flex-wrap justify-content-end">
                
                {if $isLoggedIn}
                    {* Кнопка подписки *}
                    {if $post.subscription_id}
                        <button type="button" class="btn btn-sm btn-success" disabled title="Вы уже подписаны">
                            <i class="bi bi-check-circle-fill"></i> Подписан
                        </button>
                    {else}
                        {if $post.user_id != $currentUserId}
                            <form action="subscribe.php" method="POST" class="d-inline">
                                <input type="hidden" name="author_id" value="{$post.user_id}">
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-lg"></i> Подписаться
                                </button>
                            </form>
                        {else}
                            <span class="btn btn-sm btn-secondary" style="cursor: not-allowed;" title="Нельзя подписаться на себя">
                                <i class="bi bi-person-slash"></i> Вы автор
                            </span>
                        {/if}
                    {/if}

                    {* Кнопки редактирования и удаления (только автору) *}
                    {if $currentUserId == $post.user_id}
                        <a href="edit_post.php?id={$post.id}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil"></i> Редактировать
                        </a>
                        <form action="delete_post.php" method="POST" style="display:inline;" onsubmit="return confirm('Вы уверены, что хотите удалить этот пост?');">
                            <input type="hidden" name="post_id" value="{$post.id}">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Удалить
                            </button>
                        </form>
                    {/if}
                {else}
                    {* Для гостей: подсказка про подписку *}
                    <span class="badge bg-secondary text-dark" style="font-size: 0.8rem;" title="Войдите, чтобы подписаться">
                        <i class="bi bi-lock me-1"></i> Войдите для подписки
                    </span>
                {/if}
            </div>
        </div>

        {* Блок тегов *}
        {if $post.tags}
            <div class="d-flex flex-wrap gap-2 mt-2 mb-4 align-items-center">
                <span class="fw-bold text-white me-2">Теги:</span>
                {foreach from=$post.tags item=tag}
                    <a href="tag.php?slug={$tag.slug}" 
                       class="badge bg-secondary text-white"
                       title="Все посты по тегу «{$tag.name|escape}»">
                        {$tag.name|escape}
                    </a>
                {/foreach}
            </div>
        {/if}

        <hr class="border-secondary my-4 opacity-25">

        <div class="post-body">
            {$post.content|escape}
        </div>
    </article>

    <hr class="my-5 border-secondary opacity-25">

    <!-- Секция комментариев -->
    <section class="comments-section" id="comments">
        <h2 class="mb-4 text-white fw-bold">Комментарии ({$comments|count})</h2>

        {* Уведомление об ошибке или успехе (если контроллер передает $error или $msg) *}
        {if $error}
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {$error}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        {/if}

        {if $msg}
            {if $msg == 'comment_added'}
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    🎉 Ваш комментарий успешно добавлен!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            {else}
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {$msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            {/if}
        {/if}

        <!-- Форма комментария -->
        <div class="card bg-secondary bg-opacity-25 border-0 mb-4">
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="add_comment" value="1">
                    
                    {if $isLoggedIn}
                        {* Если пользователь залогинен: имя скрыто и подставлено автоматически *}
                        <input type="hidden" name="author_name" value="{$currentUser|escape}">
                        <p class="text-muted mb-3 small">
                            Комментарий от: <strong>{$currentUser|escape}</strong> (вы авторизованы)
                        </p>
                    {else}
                        {* Если гость: поле имени видно и обязательно *}
                        <div class="mb-3">
                            <label for="author_name" class="form-label text-white">Ваше имя</label>
                            <input type="text" class="form-control bg-dark text-white border-0" 
                                   id="author_name" name="author_name" required placeholder="Как к вам обращаться?">
                        </div>
                    {/if}
                    
                    <div class="mb-3">
                        <label for="content" class="form-label text-white">Комментарий</label>
                        <textarea class="form-control bg-dark text-white border-0" 
                                  id="content" name="content" rows="4" required placeholder="Напишите что-нибудь..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary text-dark fw-bold px-4 py-2">
                        Отправить комментарий
                    </button>
                </form>
            </div>
        </div>

        <!-- Список комментариев -->
        {if $comments}
            <div class="list-group bg-transparent border-0">
                {foreach from=$comments item=comment}
                    <div class="list-group-item list-group-item-action bg-transparent border border-secondary rounded-1 text-white mb-2">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1 fw-bold text-white">{$comment.author_name|escape}</h5>
                                <small class="text-muted opacity-75">{$comment.created_at|date_format:"%d.%m.%Y %H:%M"}</small>
                            </div>
                        </div>
                        <p class="mb-0 mt-2">{$comment.content|escape}</p>
                    </div>
                {/foreach}
            </div>
        {else}
            <p class="text-muted">Пока нет комментариев. Будьте первым!</p>
        {/if}
    </section>
</div>
{/block}
