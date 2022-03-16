<?php
require_once('connectvars.php');

// начало сессии
session_start();

// очищаем переменную error
$error_msg = "";

//  если пользователь не вошел попытка войти
if (!isset($_SESSION['user_id'])) {
    if (isset($_POST['submit'])) {
        // подключение к базе данных
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // подключение введенных пользователем данных для аутентификации
        $user_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
        $user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));

        if (!empty($user_username) && !empty($user_password)) {
            // поиск имени пользователя и пароле в базе дынных
            $query = "SELECT user_id, username FROM mismatch_user WHERE username = '$user_username' AND password = SHA('$user_password')";
            $data = mysqli_query($dbc, $query);

            if (mysqli_num_rows($data) == 1) {
                // Вход в приложение прошел успешно, присваиваем значение идентификатора пользователя и его имени
                // переменным сессии (и куки) и переадресуем браузер на главную страницу
                $row = mysqli_fetch_array($data);
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                setcookie('user_id', $row['user_id'], time() + (60 * 60 * 24 * 30));    // срок действия истекает через 30 дней
                setcookie('username', $row['username'], time() + (60 * 60 * 24 * 30));  // срок действия истекает через 30 дней
                $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
                header('Location: ' . $home_url);
            }
            else {
                // имя пользователя/его пароль введены не верно, создание сообщение об ошибке
                $error_msg = 'Извините, для того, чтобы войти в приложение, вы должны ввести правильное имя и пароль';
            }
        }
        else {
            // Имя пользователя/пароль не были введены, поэтому создание сообщение об ошибке
            $error_msg = 'Извините, вы должны ввести свое имя пользователя и пароль для входа в систему.';
        }
    }
}


$page_title = 'Вход';
require_once('header.php');

// Если переменная сеанса пуста, отобразите любое сообщение об ошибке и форму входа
// в противном случае подтвердите вход в систему
if (empty($_SESSION['user_id'])) {
    echo '<p class="error">' . $error_msg . '</p>';
    ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <fieldset>
            <legend>Регистрация</legend>
            <label for="username">Имя:</label>
            <input type="text" name="username" value="<?php if (!empty($user_username)) echo $user_username; ?>" /><br />
            <label for="password">Пароль:</label>
            <input type="password" name="password" />
        </fieldset>
        <input type="submit" value="Регистрация" name="submit" />
    </form>

    <?php
}
else {
    // Подтвердите успешный вход в систему
    echo('<p class="login">Вы вошли в систему как' . $_SESSION['username'] . '.</p>');
}
?>

<?php
//вывод нижнего колонтитула
require_once ('footer.php');
?>