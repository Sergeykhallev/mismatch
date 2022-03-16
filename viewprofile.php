<?php
//открытие сессии
require_once ('startsession.php');

//вывод заголовка страницы
require_once ('header.php');
$page_title = 'Редактировать профиль';

//вывод констант
require_once('appvars.php');
require_once('connectvars.php');

// вывод навигационного меню
require_once ('navmenu.php');
require_once('appvars.php');


// Убедитесь, что пользователь вошел в систему, прежде чем идти дальше.
if (!isset($_SESSION['user_id'])) {
    echo '<p class="login">Пожалуйста <a href="login.php">Войдите</a> чтобы получить доступ к этой странице.</p>';
    exit();
}
else {
    echo('<p class="login">Вы вошли как ' . $_SESSION['username'] . '. <a href="logout.php">Выйти</a>.</p>');
}

// Подключение к базе данных
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Извлеките данные профиля из базы данных
if (!isset($_GET['user_id'])) {
    $query = "SELECT username, first_name, last_name, gender, birthdate, city, state, picture FROM mismatch_user WHERE user_id = '" . $_SESSION['user_id'] . "'";
}
else {
    $query = "SELECT username, first_name, last_name, gender, birthdate, city, state, picture FROM mismatch_user WHERE user_id = '" . $_GET['user_id'] . "'";
}
$data = mysqli_query($dbc, $query);

if (mysqli_num_rows($data) == 1) {
    // Строка пользователя была найдена, поэтому отобразите данные пользователя
    $row = mysqli_fetch_array($data);
    echo '<table>';
    if (!empty($row['username'])) {
        echo '<tr><td class="label">имя пользователя:</td><td>' . $row['username'] . '</td></tr>';
    }
    if (!empty($row['first_name'])) {
        echo '<tr><td class="label">имя:</td><td>' . $row['first_name'] . '</td></tr>';
    }
    if (!empty($row['last_name'])) {
        echo '<tr><td class="label">фамилия:</td><td>' . $row['last_name'] . '</td></tr>';
    }
    if (!empty($row['gender'])) {
        echo '<tr><td class="label">пол:</td><td>';
        if ($row['gender'] == 'F') {
            echo 'мужской';
        }
        else if ($row['gender'] == 'M') {
            echo 'женский';
        }
        else {
            echo '?';
        }
        echo '</td></tr>';
    }
    if (!empty($row['birthdate'])) {
        if (!isset($_GET['user_id']) || ($_SESSION['user_id'] == $_GET['user_id'])) {
            // Показать пользователю его собственную дату рождения
            echo '<tr><td class="label">Дата рождения:</td><td>' . $row['birthdate'] . '</td></tr>';
        }
        else {
            // Показывать только год рождения для всех остальных
            list($year, $month, $day) = explode('-', $row['birthdate']);
            echo '<tr><td class="label">Год рождения:</td><td>' . $year . '</td></tr>';
        }
    }
    if (!empty($row['city']) || !empty($row['state'])) {
        echo '<tr><td class="label">Местоположение:</td><td>' . $row['city'] . ', ' . $row['state'] . '</td></tr>';
    }
    if (!empty($row['picture'])) {
        echo '<tr><td class="label">Изображение:</td><td><img src="' . MM_UPLOADPATH . $row['picture'] .
            '" alt="Profile Picture" /></td></tr>';
    }
    echo '</table>';
    if (!isset($_GET['user_id']) || ($_SESSION['user_id'] == $_GET['user_id'])) {
        echo '<p>Хотел бы ты <a href="editprofile.php">отредактировать свой профиль</a>?</p>';
    }
} // Окончание проверки одной строки результатов пользователя
else {
    echo '<p class="error">Возникла проблема с доступом к вашему профилю.</p>';
}

mysqli_close($dbc);
?>

<?php
//вывод нижнего колонтитула
require_once ('footer.php');
?>