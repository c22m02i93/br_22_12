<?php

// ============================
// БАЗОВЫЕ СЛУЖЕБНЫЕ ФУНКЦИИ
// ============================

function db() {
    static $db = null;
    if ($db === null) {
        $db = dbConnect();
    }
    return $db;
}

function yearRus($number, $one, $two, $five) {
    $n = abs($number) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $five;
    if ($n1 > 1 && $n1 < 5) return $two;
    if ($n1 == 1) return $one;
    return $five;
}

function monthRus($mm) {
    $arr = array(
        "01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая",
        "06"=>"июня","07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября",
        "11"=>"ноября","12"=>"декабря"
    );
    return isset($arr[$mm]) ? $arr[$mm] : $mm;
}


// ============================
// НОВОСТЬ ДНЯ
// ============================

function getNewsDay() {
    $q = mysql_query("SELECT * FROM host1409556_barysh.news_day");
    return mysql_fetch_array($q);
}

function formatNewsText($text) {
    $patterns = array(
        '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
        '/\n/', '/(?:\/{3})/', '/(?:\|{3})/',
        '/@[^@]+@/', '/(?:\{{3})/', '/(?:\}{3})/',
        '/\[/', '/\]/'
    );
    $replace = array('${2}', '</p><p>', '', '', '', '', '', '', '');
    return preg_replace($patterns, $replace, $text);
}


// ============================
// КРЕСТНЫЙ ХОД
// ============================

function getHodToday() {
    $date = date("Y.m.d");
    $year = date("Y");
    $q = mysql_query("SELECT * FROM host1409556_barysh.krest_hod_$year WHERE data='$date' ORDER BY pribyv ASC");
    return $q;
}


// ============================
// КАЛЕНДАРЬ ЕПАРХИИ
// ============================

function getEparchyCalendar() {

    $today_d = date("d");
    $today_m = date("m");
    $year = date("Y");

    $calendarDateKey = $today_m . "." . $today_d;
    $angelDateKey = $today_d . "." . $today_m;

    $q = mysql_query("
        SELECT id, name, san, rozd, diak, presv, monah, angel
        FROM host1409556_barysh.klir
        WHERE status='штатный' AND (
            rozd LIKE '%$calendarDateKey' OR 
            diak LIKE '%$calendarDateKey' OR 
            presv LIKE '%$calendarDateKey' OR 
            monah LIKE '%$calendarDateKey' OR 
            angel LIKE '%$angelDateKey%'
        )
        ORDER BY name ASC
    ");

    $events = array(
        'birthday' => array(),
        'diakon'   => array(),
        'ierey'    => array(),
        'monah'    => array(),
        'angel'    => array()
    );

    while ($k = mysql_fetch_assoc($q)) {

        // День рождения
        if (!empty($k['rozd']) && substr($k['rozd'], 5, 5) == $calendarDateKey) {
            $yy = substr($k['rozd'], 0, 4);
            $res = $year - $yy;
            $events['birthday'][] =
                '<div style="margin-bottom: 5px"><a href="/klirik.php?id='.$k['id'].'" target="_blank">'.
                $k['san'].' '.$k['name'].'</a> - день рождения <b>'.
                $res.' '.yearRus($res,"год","года","лет").'</b></div>';
        }

        // Диаконская хиротония
        if (!empty($k['diak']) && substr($k['diak'], 5, 5) == $calendarDateKey) {
            $yy = substr($k['diak'], 0, 4);
            $res = $year - $yy;
            $events['diakon'][] =
                '<div style="margin-bottom:5px"><a href="/klirik.php?id='.$k['id'].'" target="_blank">'.
                $k['san'].' '.$k['name'].'</a> - диаконская хиротония <b>'.
                $res.' '.yearRus($res,"год","года","лет").'</b></div>';
        }

        // Иерейская хиротония
        if (!empty($k['presv']) && substr($k['presv'], 5, 5) == $calendarDateKey) {
            $yy = substr($k['presv'], 0, 4);
            $res = $year - $yy;
            $events['ierey'][] =
                '<div style="margin-bottom:5px"><a href="/klirik.php?id='.$k['id'].'" target="_blank">'.
                $k['san'].' '.$k['name'].'</a> - иерейская хиротония <b>'.
                $res.' '.yearRus($res,"год","года","лет").'</b></div>';
        }

        // Монашеский постриг
        if (!empty($k['monah']) && substr($k['monah'], 5, 5) == $calendarDateKey) {
            $yy = substr($k['monah'], 0, 4);
            $res = $year - $yy;
            $events['monah'][] =
                '<div style="margin-bottom:5px"><a href="/klirik.php?id='.$k['id'].'" target="_blank">'.
                $k['san'].' '.$k['name'].'</a> - монашеский постриг <b>'.
                $res.' '.yearRus($res,"год","года","лет").'</b></div>';
        }

        // День ангела
        if (!empty($k['angel']) && strpos($k['angel'], $angelDateKey) !== false) {
            $events['angel'][] =
                '<div style="margin-bottom:5px"><a href="/klirik.php?id='.$k['id'].'" target="_blank">'.
                $k['san'].' '.$k['name'].'</a> - день ангела</div>';
        }
    }

    return $events;
}


// ============================
// ПРЕСТОЛЬНЫЕ ПРАЗДНИКИ
// ============================

function getPrestolToday() {
    $d = date("d");
    $m = date("m");
    $q = mysql_query("SELECT id, name FROM host1409556_barysh.prihods WHERE angel LIKE '%$d.$m%' ORDER BY name ASC");

    $out = array();
    while ($p = mysql_fetch_array($q)) {
        $out[] = '<div style="margin-bottom:5px"><a href="/prihod.php?id='.$p['id'].'" target="_blank">'.$p['name'].'</a></div>';
    }
    return $out;
}


// ============================
// АНОНСЫ
// ============================

function getAnons($limit = 2, $excludeDate = null) {
    $where = $excludeDate ? "WHERE data != '$excludeDate'" : "";
    return mysql_query("SELECT * FROM host1409556_barysh.anons $where ORDER BY data DESC LIMIT $limit");
}


// ============================
// ПУБЛИКАЦИИ
// ============================

function getPublish($limit = 3, $excludeDate = null) {
    $where = $excludeDate ? "WHERE data != '$excludeDate'" : "";
    return mysql_query("SELECT * FROM host1409556_barysh.publikacii $where ORDER BY data DESC LIMIT $limit");
}


// ============================
// ВИДЕО
// ============================

function getVideos($limit = 2) {
    return mysql_query("SELECT * FROM host1409556_barysh.video ORDER BY id DESC LIMIT $limit");
}


// ============================
// СМЕШАННЫЕ НОВОСТИ
// ============================

function getMixedNews($excludeToday) {
    $sql = "
        SELECT * FROM (
          SELECT tema, kratko, data, oblozka, NULL AS link, 'local' AS source, video, views
          FROM host1409556_barysh.news_eparhia
          WHERE data != '$excludeToday'
          UNION ALL
          SELECT tema, kratko, data, oblozka, link, 'mitropolia' AS source, NULL AS video, NULL AS views
          FROM host1409556_barysh.news_mitropolia
          WHERE section='barysh_tag' AND data >= '2025-11-01 00:00:00'
        ) AS u
        ORDER BY u.data DESC
        LIMIT 3
    ";

    return mysql_query($sql);
}


// ============================
// СЛОВО АРХИПАСТЫРЯ
// ============================

function getSlovoPadre() {
    return mysql_query("
        SELECT tema, kratko, data, oblozka, link
        FROM host1409556_barysh.news_mitropolia
        WHERE section='slovo' AND data >= '2025-11-01 00:00:00'
        ORDER BY data DESC
        LIMIT 2
    ");
}


// ============================
// ВИДЕО БЛОК
// ============================

function renderVideoBlock($limit = 2) {

    $q = getVideos($limit);
    $html = '';

    while ($vid = mysql_fetch_array($q)) {

        $dtn  = $vid['data'];
        $y    = substr($dtn, 0, 4);
        $m    = substr($dtn, 5, 2);
        $d    = ltrim(substr($dtn, 8, 2), "0");
        $time = substr($dtn, 11, 5);

        $dateHuman = '<span class="date">'.$d.' '.monthRus($m).' '.$y.' г. '.$time.'</span>';

        // Исправляем width
        $code = preg_replace('/width="46\%"/', 'width="100%"', $vid['kod']);

        // пытаемся найти связанную новость
        $relatedQ = mysql_query("SELECT * FROM host1409556_barysh.news_eparhia WHERE video='".$vid['kod']."'");
        $related  = mysql_fetch_array($relatedQ);

        // вывод
        $html .= '<div class="index_block" style="text-align:left"><div style="margin-left:5px"><span class="title">';

        if ($related) {
            $html .= '<a href="news_show.php?data='.$related['data'].'">'.$vid['tema'].'</a>';
        } else {
            $html .= $vid['tema'];
        }

        $html .= '</span><br />'.$dateHuman.'<br /><br />'.$code.'</div></div>';
    }

    return $html;
}


// ============================
// АРХИЕРЕЙСКИЕ СЛУЖБЫ (ГЛАВНАЯ)
// ============================

function getRaspisanieForIndex()
{
    mysql_query("SET NAMES 'cp1251'");

    $today = date("Y.m.d");

    // 1) Берём грядущие
    $sql1 = "
        SELECT *
        FROM host1409556_barysh.raspisanie
        WHERE data >= '$today'
        ORDER BY data ASC
        LIMIT 10
    ";

    $q = mysql_query($sql1);

    if (!$q) {
        echo "<pre>Ошибка расписания (грядущие): " . mysql_error() . "</pre>";
        return false;
    }

    // 2) Если ничего нет — 2 последние прошедшие
    if (mysql_num_rows($q) == 0) {

        $sql2 = "
            SELECT *
            FROM host1409556_barysh.raspisanie
            WHERE data < '$today'
            ORDER BY data DESC
            LIMIT 2
        ";

        $q = mysql_query($sql2);

        if (!$q) {
            echo "<pre>Ошибка расписания (прошедшие): " . mysql_error() . "</pre>";
            return false;
        }
    }

    return $q;
}

?>