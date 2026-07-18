<?php
session_start();
require __DIR__ . '/../db.php';

$error = '';
$usernameInput = '';

// Если пользователь уже залогинен — сразу на главную
if (!empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $usernameInput = $username;

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Неверный логин или пароль";
                }
            } else {
                $error = "Пользователь не найден";
            }
        } catch (PDOException $e) {
            error_log("Ошибка при входе: " . $e->getMessage());
            $error = "Ошибка сервера. Попробуйте позже.";
        }
    } else {
        $error = "Заполните все поля";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        body {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .auth-container {
            max-width: 400px;
            width: 100%;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .auth-container h2 {
            text-align: center;
            color: #1a1a1a;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e1e1e1;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #2563eb;
        }

        .error-message {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .auth-footer {
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }

        .auth-footer a {
            color: #4f4f4f;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
            color: #3b82f6;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2>Вход</h2>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($usernameInput) ?>" required placeholder="Введите ваш логин">
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>

            <button type="submit" class="submit-btn">Войти</button>
        </form>

        <div class="auth-footer">
            Нет аккаунта? <a href="register.php">Регистрация</a>
        </div>
    </div>
</body>
</html>
