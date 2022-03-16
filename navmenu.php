<?php
// Создайте навигационное меню
echo '<hr>';
if (isset($_SESSION['username'])) {
    echo '&#10084; <a href="index.php">Главная страница</a><br />';
    echo '&#10084; <a href="viewprofile.php">Просмотр профиля</a><br />';
    echo '&#10084; <a href="editprofile.php">Редактирование профиля</a><br />';
    echo '&#10084; <a href="questionnaire.php">Анкета</a><br />';
    echo '&#10084; <a href="mymismatch.php">Мои несоответствия</a><br />';
    echo '&#10084; <a href="logout.php">Выход из приложения (' . $_SESSION['username'] . ')</a>';
}
else {
    echo '&#10084; <a href="login.php">Вход в приложение</a><br />';
    echo '&#10084; <a href="signup.php">Создание учетной записи</a>';
}
echo '</hr>';
