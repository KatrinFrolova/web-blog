<?php
/* Smarty version 3.1.48, created on 2026-07-18 21:14:56
  from 'D:\OSPanel\home\myblog.local\templates\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.48',
  'unifunc' => 'content_6a5bc2a0b48148_86683128',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dde5028680fc6a74479f5e69ad504fa23a34cd0c' => 
    array (
      0 => 'D:\\OSPanel\\home\\myblog.local\\templates\\index.tpl',
      1 => 1784398356,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6a5bc2a0b48148_86683128 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_13860091016a5bc2a09116c4_04210463', "content");
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, "layout.tpl");
}
/* {block "content"} */
class Block_13860091016a5bc2a09116c4_04210463 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_13860091016a5bc2a09116c4_04210463',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\OSPanel\\home\\myblog.local\\smarty\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
?>

    <div class="container mt-5">
        
        <?php if ($_smarty_tpl->tpl_vars['dbError']->value) {?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Ошибка БД: <?php echo $_smarty_tpl->tpl_vars['dbError']->value;?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['filterTagSlug']->value) {?>
            <div class="mb-4 p-3 bg-secondary bg-opacity-25 border rounded">
                <h5 class="fw-bold text-white">
                    <i class="bi bi-tag me-2"></i>Фильтр: по тегу «<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['filterTagSlug']->value, ENT_QUOTES, 'UTF-8', true);?>
»
                </h5>
                <a href="/" class="btn btn-sm btn-outline-light">← Все посты</a>
            </div>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['posts']->value) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['posts']->value, 'post');
$_smarty_tpl->tpl_vars['post']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['post']->value) {
$_smarty_tpl->tpl_vars['post']->do_else = false;
?>
                <article class="blog-post-card mb-4">
                    <header class="post-header">
                        <h2 class="post-title">
                            <a href="view_post.php?id=<?php echo $_smarty_tpl->tpl_vars['post']->value['id'];?>
" class="text-white text-decoration-none">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['post']->value['title'], ENT_QUOTES, 'UTF-8', true);?>

                            </a>
                        </h2>
                        
                        <div class="post-meta d-flex flex-wrap align-items-center gap-3">
                            <span>
                                <i class="bi bi-person"></i> Автор: <strong class="author-name"><?php echo htmlspecialchars((($tmp = @$_smarty_tpl->tpl_vars['post']->value['author_name'])===null||$tmp==='' ? "Аноним" : $tmp), ENT_QUOTES, 'UTF-8', true);?>
</strong>
                            </span>
                            <span class="post-date">
                                <i class="bi bi-clock"></i> Дата: <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['post']->value['created_at'],"%d.%m.%Y %H:%M");?>

                            </span>

                                                        <?php if ($_smarty_tpl->tpl_vars['post']->value['tags']) {?>
                                <div class="d-flex flex-wrap gap-1 mt-1 mt-md-0 align-items-center">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['post']->value['tags'], 'tag');
$_smarty_tpl->tpl_vars['tag']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tag']->value) {
$_smarty_tpl->tpl_vars['tag']->do_else = false;
?>
                                        <a href="tag.php?slug=<?php echo $_smarty_tpl->tpl_vars['tag']->value['slug'];?>
" 
                                           class="badge bg-secondary text-white me-1"
                                           title="Все посты по тегу «<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tag']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
»">
                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tag']->value['name'], ENT_QUOTES, 'UTF-8', true);?>

                                        </a>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                </div>
                            <?php }?>

                            <!-- БЛОК КНОПОК -->
                            <div class="ms-auto d-flex gap-2 align-items-center flex-wrap flex-md-nowrap">
                                
                                                                <?php if ($_smarty_tpl->tpl_vars['isLoggedIn']->value) {?>
                                                                        <?php if ($_smarty_tpl->tpl_vars['post']->value['subscription_id']) {?>
                                        <button type="button" class="btn btn-sm btn-success" disabled title="Вы уже подписаны">
                                            <i class="bi bi-check-circle-fill"></i> Подписан
                                        </button>
                                    <?php } else { ?>
                                                                                <?php if ($_smarty_tpl->tpl_vars['post']->value['user_id'] != $_smarty_tpl->tpl_vars['currentUserId']->value) {?>
                                            <form action="/subscribe.php" method="POST" class="d-inline">
                                                <input type="hidden" name="author_id" value="<?php echo $_smarty_tpl->tpl_vars['post']->value['user_id'];?>
">
                                                <input type="hidden" name="action" value="subscribe">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-plus-lg"></i> Подписаться
                                                </button>
                                            </form>
                                        <?php } else { ?>
                                                                                        <span class="btn btn-sm btn-secondary" style="cursor: not-allowed;" title="Вы не можете подписаться на себя">
                                                <i class="bi bi-person-slash"></i> Вы автор
                                            </span>
                                        <?php }?>
                                    <?php }?>
                                <?php } else { ?>
                                                                        <span class="badge bg-secondary text-dark" style="font-size: 0.8rem;" title="Войдите, чтобы подписаться">
                                        <i class="bi bi-lock me-1"></i> Войдите для подписки
                                    </span>
                                <?php }?>

                                                                <a href="view_post.php?id=<?php echo $_smarty_tpl->tpl_vars['post']->value['id'];?>
" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-chat-dots"></i> Комментировать
                                </a>

                                                                <?php if ($_smarty_tpl->tpl_vars['isLoggedIn']->value && $_smarty_tpl->tpl_vars['currentUserId']->value == $_smarty_tpl->tpl_vars['post']->value['user_id']) {?>
                                    <a href="edit_post.php?id=<?php echo $_smarty_tpl->tpl_vars['post']->value['id'];?>
" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                    <form action="delete_post.php" method="POST" style="display:inline;" onsubmit="return confirm('Вы уверены, что хотите удалить этот пост?');">
                                        <input type="hidden" name="post_id" value="<?php echo $_smarty_tpl->tpl_vars['post']->value['id'];?>
">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Удалить
                                        </button>
                                    </form>
                                <?php }?>
                            </div>
                        </div>
                    </header>
                    
                    <hr class="my-3" style="border-color: #555;">
                    
                    <div class="post-body">
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['post']->value['content'], ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                </article>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php } else { ?>
            <p class="text-muted">
                <?php if ($_smarty_tpl->tpl_vars['filterTagSlug']->value) {?>
                    По тегу «<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['filterTagSlug']->value, ENT_QUOTES, 'UTF-8', true);?>
» пока нет постов.
                <?php } elseif ($_smarty_tpl->tpl_vars['isFeedMode']->value) {?>
                    Пока нет постов от авторов, на которых вы подписаны.
                <?php } else { ?>
                    Пока нет постов для отображения.
                <?php }?>
            </p>
        <?php }?>
    </div>
<?php
}
}
/* {/block "content"} */
}
