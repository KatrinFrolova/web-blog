<?php
require __DIR__ . '/../db.php';
session_start();

$rootDir = realpath(__DIR__ . '/..');
require $rootDir . '/smarty/libs/Smarty.class.php';

$smarty = new Smarty();
$smarty->template_dir = $rootDir . '/templates';
$smarty->compile_dir = $rootDir . '/templates_c';
$smarty->cache_dir = $rootDir . '/cache';
$smarty->config_dir = $rootDir . '/configs';
$smarty->debugging = false;
$smarty->caching = false;

// 1. Определяем режим: Регистрация или Вход
$isRegister = isset($_GET['register']) && $_GET['register'] == '1';

// 2. Устанавливаем заголовок страницы
$title = $isRegister ? 'Регистрация' : 'Вход в систему';

// 3. Переменные сессии
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = $isLoggedIn ? ($_SESSION['username'] ?? null) : null;

// 4. ОБРАБОТКА ФОРМЫ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $msg = 'Пожалуйста, заполните все поля.';
    } else {
        try {
            if ($action === 'register') {
                // Проверка на существование
                $check = $pdo->prepare('SELECT id FROM users WHERE username = ?');
                $check->execute([$username]);
                if ($check->fetch()) {
                    $msg = 'Такой пользователь уже существует!';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                    $stmt->execute([$username, $hash]);
                    
                    // редирект на корень сайта (главную) с сообщением об успехе
                    header('Location: /?msg=success');
                    exit;
                }
            } elseif ($action === 'login') {
                $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // редирект на корень сайта (главную) без лишних путей
                    header('Location: /');
                    exit;
                } else {
                    $msg = 'Неверное имя пользователя или пароль.';
                }
            }
        } catch (PDOException $e) {
            $msg = 'Ошибка базы данных: ' . htmlspecialchars($e->getMessage());
        }
    }
}

// Получаем сообщение из GET (для показа на текущей странице, если была ошибка)
$msg = $_GET['msg'] ?? '';

// 5. Передаем переменные в шаблон
$smarty->assign('title', $title);
$smarty->assign('isRegister', $isRegister);
$smarty->assign('msg', $msg);
$smarty->assign('isLoggedIn', $isLoggedIn);
$smarty->assign('currentUser', $currentUser);

// 6. Показываем шаблон
$smarty->display('auth.tpl');
?>
