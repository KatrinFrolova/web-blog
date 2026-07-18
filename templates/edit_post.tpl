{extends file="layout.tpl"}

{block name="content"}
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark text-white border-0 shadow">
                    <div class="card-header bg-primary">
                        <h4 class="mb-0">Редактирование поста</h4>
                    </div>
                    <div class="card-body">
                        
                        {if $error}
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {$error}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        {/if}

                        <form action="" method="POST">
                            <!-- Скрытое поле с ID поста -->
                            <input type="hidden" name="id" value="{$post.id}">

                            <div class="mb-3">
                                <label for="title" class="form-label">Заголовок</label>
                                <input type="text" class="form-control bg-secondary text-white border-0" 
                                       id="title" name="title" value="{$post.title|escape}" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Текст поста</label>
                                <textarea class="form-control bg-secondary text-white border-0" 
                                          id="content" name="content" rows="10" required>{$post.content|escape}</textarea>
                            </div>

                            {* --- БЛОК ТЕГОВ --- *}
                            <div class="mb-4">
                                <label class="form-label fw-bold">Теги</label>
                                
                                {if $allTags}
                                    <div class="tags-section bg-secondary bg-opacity-25 p-3 rounded">
                                        <div class="row g-2">
                                            {foreach from=$allTags item=tag}
                                                <div class="col-md-4 col-sm-6 col-12">
                                                    <div class="form-check">
                                                        {* 
                                                           Сначала проверяем, что $selectedTags - это массив.
                                                        *}
                                                        {if $selectedTags|is_array && $tag.id|in_array:$selectedTags}

                                                            <input class="form-check-input" type="checkbox" name="tags[]" value="{$tag.id}" id="tag_{$tag.id}" checked>
                                                        {else}
                                                            <input class="form-check-input" type="checkbox" name="tags[]" value="{$tag.id}" id="tag_{$tag.id}">
                                                        {/if}
                                                        <label class="form-check-label text-white" for="tag_{$tag.id}">{$tag.name}</label>
                                                    </div>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                {else}
                                    <p class="text-muted small">Теги не загружены. Проверьте таблицу tags в базе данных.</p>
                                {/if}
                            </div>
                            {* ------------------------------------ *}

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">Сохранить изменения</button>
                                <a href="/" class="btn btn-outline-secondary">Отмена</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
