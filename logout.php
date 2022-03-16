<?php
// Если пользователь вошел в систему, удалите переменные сеанса, чтобы выйти из них
session_start();
if (isset($_SESSION['user_id'])) {
    // Удалите переменные сеанса, очистив массив $_SESSION
    $_SESSION = array();

    // Удалите сессионный файл cookie, установив срок его действия на час назад (3600)
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600);
    }

    // Уничтожить сеанс
    session_destroy();
}

// Удалите файлы cookie идентификатора пользователя и имени пользователя, установив срок их действия на час
setcookie('user_id', '', time() - 3600);
setcookie('username', '', time() - 3600);

// Перенаправление на домашнюю страницу
$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
header('Location: ' . $home_url);
