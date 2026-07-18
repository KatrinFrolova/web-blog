{extends file="layout.tpl"} 
{block name="title"}Статьи по тегу: {$tag_name}{/block}

{block name="content"}
    <div class="container mt-5">
        <h1>Статьи по тегу «{$tag_name}»</h1>
        
        {if $posts|@count == 0}
            <p>К сожалению, статей с этим тегом пока нет.</p>
        {else}
            <div class="row">
                {foreach from=$posts item=post}
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 d-flex flex-column">
                            <!-- Если есть картинка поста, раскомментируй строку ниже -->
                            <!-- <img src="/uploads/{$post.image}" class="card-img-top" alt="{$post.title}"> -->
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{$post.title|escape}</h5>
                                <p class="card-text text-muted small">
                                    Автор: {$post.author_name|escape} • 
                                    Дата: {$post.created_at|date_format:"%d.%m.%Y"}
                                </p>
                                
                                {* ВЫВОД ПОЛНОГО ТЕКСТА ПОСТА *}
                                <div class="card-text flex-grow-1">
                                    {$post.content|escape}
                                </div>
                                
                        </div>
                    </div>
                {/foreach}
            </div>
        {/if}
    </div>
{/block}
