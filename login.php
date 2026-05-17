<?php

session_start();
header('Content-Type: text/html; charset=UTF-8');

// Если уже авторизован - перенаправляем
if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

$host = 'localhost';
$dbname = 'u82813';           
$username_db = 'u82813';
$password_db = '4313992';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username_db,
        $password_db,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$error_message = '';

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['pass'] ?? '');
    
    if (empty($login) || empty($password)) {
        $error_message = 'Заполните логин и пароль';
    } else {
        $stmt = $pdo->prepare("SELECT id, login, password_hash FROM task5_applications WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['login'] = $user['login'];
            $_SESSION['uid'] = $user['id'];
            header('Location: index.php');
            exit();
        } else {
            $error_message = 'Неверный логин или пароль';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Вход для изменения данных</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="login_page">
        <div class="login-box">
            <h1>Вход</h1>
            <?php if ($error_message): ?>
            <div class="error">
                <?= htmlspecialchars($error_message) ?>
            </div>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="login" placeholder="Логин" required>
                <input type="password" name="pass" placeholder="Пароль" required>
                <button type="submit">Войти</button>
            </form>
            <a href="index.php">Вернуться к анкете</a>
        </div>
    </div>
</body>

</html>