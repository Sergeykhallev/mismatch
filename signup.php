<?php
require_once('header.php');
$page_title = 'Войти';

require_once('appvars.php');
require_once('connectvars.php');

// соединение с базой данных
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (isset($_POST['submit'])) {
    // извлечение данных из суперглобального массива POST
    $username = mysqli_real_escape_string($dbc, trim($_POST['username']));
    $password1 = mysqli_real_escape_string($dbc, trim($_POST['password1']));
    $password2 = mysqli_real_escape_string($dbc, trim($_POST['password2']));

    if (!empty($username) && !empty($password1) && !empty($password2) && ($password1 == $password2)) {
        // проверка того что никто из уже записанных пользователей не пользуется таким же именем как то которое ввел новый пользователь
        $query = "SELECT * FROM mismatch_user WHERE username = '$username'";
        $data = mysqli_query($dbc, $query);
        if (mysqli_num_rows($data) == 0) {
            // имя, введенное пользователем, не используется по этому добавляем его в базу данных
            $query = "INSERT INTO mismatch_user (username, password, join_date) VALUES ('$username', SHA('$password1'), NOW())";
            mysqli_query($dbc, $query);

            // вывод подтверждения пользователю
            echo '<p>Ваша новая учетная запись создана. Вы можете войти в приложение и<a href="editprofile.php">отредактировать свой профиль</a>.</p>';

            mysqli_close($dbc);
            exit();
        }
        else {
            // учетная запись уже с таким именем уже существует в базе данных, по этому выводиться сообщение об ошибке
            echo '<p class="error">учетная запись с таким именем уже существует. Введите, пожалуйста, другое</p>';
            $username = "";
        }
    }
    else {
        echo '<p class="error">Вы должны ввести данные для создания учетной записи, в том числе пароль дважды.</p>';
    }
}

mysqli_close($dbc);
?>

<p>Введите пожалуйста, ваше имя и пароль для создания учетной записи в приложении &quot;Несоответствия&quot;\.</p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
        <legend>Входные данные</legend>
        <label for="username">Имя:</label>
        <input type="text" id="username" name="username" value="<?php if (!empty($username)) echo $username; ?>" /><br />
        <label for="password1">Пароль:</label>
        <input type="password" id="password1" name="password1" /><br />
        <label for="password2">Повторите пароль:</label>
        <input type="password" id="password2" name="password2" /><br />
    </fieldset>
    <input type="submit" value="войти" name="submit" />
</form>

<?php
//вывод нижнего колонтитула
require_once ('footer.php');
?>