<?php
//открытие сессии
require_once ('startsession.php');

//вывод заголовка страницы
require_once ('header.php');
$page_title = 'Где противоположности встречаются!';

//вывод констант
require_once('appvars.php');
require_once('connectvars.php');

// вывод навигационного меню
require_once ('navmenu.php');

// соединение с базой данных
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Извлекаем пользовательские данные из MySQL
$query = "SELECT user_id, first_name, picture FROM mismatch_user WHERE first_name IS NOT NULL ORDER BY join_date DESC LIMIT 5";
$data = mysqli_query($dbc, $query);

// прохождение в цикле массива данных пользователя, форматирование данных в виде HTML
echo '<h4>Последние участники:</h4>';
echo '<table>';
while ($row = mysqli_fetch_array($data)) {
    if (is_file(MM_UPLOADPATH . $row['picture']) && filesize(MM_UPLOADPATH . $row['picture']) > 0) {
        echo '<tr><td><img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="' . $row['first_name'] . '" /></td>';
    }
    else {
        echo '<tr><td><img src="' . MM_UPLOADPATH . 'nopic.jpg' . '" alt="' . $row['first_name'] . '" /></td>';
    }
    if (isset($_SESSION['user_id'])) {
        echo '<td><a href="viewprofile.php?user_id=' . $row['user_id'] . '">' . $row['first_name'] . '</a></td></tr>';
    }
    else {
        echo '<td>' . $row['first_name'] . '</td></tr>';
    }
}
echo '</table>';

mysqli_close($dbc);
?>

<?php
    //вывод нижнего колонтитула
    require_once ('footer.php');
?>