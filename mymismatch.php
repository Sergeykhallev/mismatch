<?php
//открытие сессии
require_once ('startsession.php');

//вывод заголовка страницы
$page_title = 'Мои несоответствия';
require_once('header.php');

require_once('appvars.php');
require_once('connectvars.php');

// Прежде чем продолжать сценарий нужно проверить, вошел ли пользователь в приложение
if(!isset($_SESSION['user_id'])){
    echo '<p class="login">Пожалуйста, <a href="login.php"> войдите в приложение </a> чтобы получить доступ к этой странице.</p>';
    exit();
}

// Вывод навигационного меню
require_once ('navmenu.php');

// Соединение с базой данных
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

// поиск несоответствий производиться, только если пользователь уже внес в анкету свои значения признаков несоответствия

$query = "SELECT * FROM mismatch_response WHERE user_id = '" . $_SESSION['user_id'] . "'";
$data = mysqli_query($dbc, $query);

if (mysqli_num_rows($data) != 0) {
    // вначале извлечение значений признаков несоответствия из таблицы, содержащей информацию о признаках несоответствия пользователя
    // (для получения наименования признаков несоответствия используется объединение - JOIN)
    $query = "SELECT mr.response_id, mr.topic_id, mr.response, mt.name AS topic_name " .
        "FROM mismatch_response AS mr " .
        "INNER JOIN mismatch_topic AS mt USING (topic_id) " .
        "WHERE mr.user_id = '" . $_SESSION['user_id'] . "'";
    $data = mysqli_query($dbc, $query);
    $user_responses = array();
    while ($row = mysqli_fetch_array($data)) {
        array_push($user_responses, $row);
    }
}

// инициализация переменных, в которых будут сохранены результаты поиска $mismatch_score = 0
$mismatch_score = 0;
$mismatch_user_id = -1;
$mismatch_topics = array();

// проход в цикле записей в таблице, содержащей информацию о пользователях,
// и сравнение значений признаков несоответствия пользователя с такими же значениями других пользователей

$query = "SELECT user_id FROM mismatch_user WHERE user_id != '" . $_SESSION['user_id'] . "'";
$data = mysqli_query($dbc, $query);
while ($row = mysqli_fetch_array($data)) {
    // извлечение значений признаков несоответствия для пользователя (кандидат для наилучшего несоответствия)
    $query2 = "SELECT response_id, topic_id, response FROM mismatch_response WHERE user_id = '" . $row['user_id'] . "'";
    $data2 = mysqli_query($dbc, $query2);
    $mismatch_responses = array();
    while ($row2 = mysqli_fetch_array($data2)) {
        array_push($mismatch_responses, $row2);
    }
// сравнение значений признаков несоответствия и вычисления оценки несоответствия

    $score = 0;
    $topics = array();
    for ($i = 0; $i < count($user_responses); $i++) {
        if ($user_responses[$i]['response'] + $mismatch_responses[$i]['response'] == 3) {
            $score += 1;
            array_push($topics, $user_responses[$i]['topic_name']);
        }
    }

// оценка несоответствия текущего пользователя сравнивается с наилучшей оценкой на данный момент
    if ($score > $mismatch_score) {
    // найдено наилучшее несоответствие, поэтому переменные, отслеживающие параметры процесса поиска, обновляются.
        $mismatch_score = $score;
        $mismatch_user_id = $row['user_id'];
        $mismatch_topics = array_slice($topics, 0);
    }
}

// проверка того, найдено ли соответствие
if ($mismatch_user_id != -1) {
    $query = "SELECT username, first_name, last_name, city, state, picture FROM mismatch_user WHERE user_id = '$mismatch_user_id'";
    $data = mysqli_query($dbc, $query);
    if (mysqli_num_rows($data) == 1) {
    // запись пользователя с наилучшим несоответствием найдена в таблице, вывод информации об этом пользователе
        $row = mysqli_fetch_array($data);
        echo '<table><tr><td class="label">';
        if (!empty($row['first_name']) && !empty($row['last_name'])) {
            echo $row['first_name'] . ' ' . $row['last_name'] . '<br />';
        }
        if (!empty($row['city']) && !empty($row['state'])) {
            echo $row['city'] . ', ' . $row['state'] . '<br />';
        }
        echo '</td><td>';
        if (!empty($row['picture'])) {
            echo '<img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="Profile Picture" /><br />';
        }
        echo '</td></tr></table>';
    // вывод значений признаков несоответствия
        echo '<h4>Вы не соответствуете по следующим ' . count($mismatch_topics) . ' признакам:</h4>';
        foreach ($mismatch_topics as $topic) {
            echo $topic . '<br />';
        }
    // вывод гиперссылки на профиль пользователя с наилучшим несоответствием
        echo '<h4>Просмотр<a href=viewprofile.php?user_id=' . $mismatch_user_id . '>' . $row['first_name'] . ' профиля</a>.</h4>';
    }
  }
  else {
    echo '<p>Вы должны <a href="questionnaire.php">заполнить анкету</a> прежде чем для вас может быть найдено несоответствие.</p>';
}

mysqli_close($dbc);

// добавление нижнего колонтитула
require_once('footer.php');









