<!-- Подключаем Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5 py-5">
    <div class="row justify-content-center">
        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4 fw-bold">{$title}</h3>

                    {if $msg}
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {$msg}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    {/if}

                    <form method="POST" action="">
                        <input type="hidden" name="action" value="{if $isRegister}register{else}login{/if}">

                        <div class="mb-3">
                            <label class="form-label small text-muted">Имя пользователя</label>
                            <input type="text" name="username" class="form-control form-control-lg" placeholder="Логин" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Пароль</label>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="Пароль" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                            {if $isRegister}Зарегистрироваться{else}Войти{/if}
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        {if !$isRegister}
                            <a href="?register=1" class="text-decoration-none text-primary">Нет аккаунта? Регистрация</a>
                        {else}
                            <a href="?" class="text-decoration-none text-primary">Уже есть аккаунт? Войти</a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
