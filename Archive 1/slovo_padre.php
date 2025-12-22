<?php
if (isset($_REQUEST[session_name()])) session_start();
$auth = $_SESSION['auth'];
$name_user = $_SESSION['name_user'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'head.php'; ?>
<title>Слово архипастыря</title>
</head>
<body>
<div style="box-shadow: 0 0 20px rgba(0,0,0,0.5);">
<?php
include 'golova.php';
$slovo_padre = true;
include 'menu.php';
include 'function.php';
include 'db.php';
include 'content.php';
?>
<style>
/* ============================
   ОСНОВНЫЕ СТИЛИ (1 в 1 как на главной)
============================ */

* { box-sizing: border-box; }

body {
    margin: 0;
    padding: 0;
    font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    color: #333;
}

a {
    color: #4a4a95;
    text-decoration: none;
}

a:hover {
    color: #26266e;
    text-decoration: underline;
}

img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
}

/* Основной контейнер */
.page-wrapper {
    max-width: 1200px;
    margin: 0 auto 40px auto;
    padding: 0 16px 24px 16px;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(18px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.7);
}

/* Заголовки */
h1 {
    font-size: 28px;
    margin-top: 0;
    color: #6c2f2f;
    border-bottom: 3px solid rgba(240,208,200,0.6);
    padding-bottom: 6px;
}

/* Изображение */
.mitr-photo {
    float: left;
    width: 220px;
    border: 1px solid #C3D7D4;
    padding: 10px;
    margin: 0 15px 10px 0;
    border-radius: 12px;
    box-shadow: 2px 2px 5px rgba(0,0,0,0.3);
}

/* Секции */
.title {
    font-size: 1.2rem;
    font-weight: 700;
    margin-top: 20px;
    margin-bottom: 10px;
    color: #633;
}

/* Параграфы */
#osnovnoe p {
    line-height: 1.6;
    font-size: 0.98rem;
}

/* Табличные блоки в тексте */
b {
    color: #6c2f2f;
}

/* Обтекаемое изображение */
.clearfix::after {
    content: "";
    display: block;
    clear: both;
}
</style>
<div id="osnovnoe" class="clearfix">
<h1>Слово архипастыря</h1>
<?php
// Настройки
$num_elements = 10;
$table = "host1409556_barysh.news_mitropolia";
$section = "slovo";

// Текущая страница
if (!isset($_GET['page'])) $p = 1;
else {
    $p = intval($_GET['page']);
    if ($p < 1) $p = 1;
}

// Подсчитываем количество записей
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM $table WHERE section='slovo'"), 0, 0);
$num_pages = ceil($total / $num_elements);
if ($p > $num_pages) $p = $num_pages;
$start = ($p - 1) * $num_elements;

echo GetNav($p, $num_pages, "slovo_padre") . '<hr style="width: 100%" />';

// Выборка данных
$q = mysql_query("SELECT * FROM $table WHERE section='slovo' ORDER BY data DESC LIMIT $start, $num_elements");
if (mysql_num_rows($q) > 0) {
    while ($res = mysql_fetch_assoc($q)) {

        // Форматируем дату
        $dtn = $res['data'];
        $yyn = substr($dtn, 0, 4);
        $mmn = substr($dtn, 5, 2);
        $ddn = substr($dtn, 8, 2);
        $hours = substr($dtn, 11, 5);

        $monthes = array(
            "01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая","06"=>"июня",
            "07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября","11"=>"ноября","12"=>"декабря"
        );
        if ($ddn[0] == '0') $ddn = substr($ddn, 1);
        $mm1n = $monthes[$mmn];
        $ddttn = '<span class="date">'.$ddn.' '.$mm1n.' '.$yyn.' г. '.$hours.'</span>';

        // Текст 25 слов
        $plain = strip_tags($res['kratko']);
        $words = explode(' ', $plain);
        $short = implode(' ', array_slice($words, 0, 25));
        if (count($words) > 25) $short .= '...';

        // --- Вывод блоков ---

        echo '<div class="block_title">';
        echo '<span class="title"><a href="slovo_padre_show.php?link='.urlencode($res['link']).'">'.$res['tema'].'</a></span><br />'.$ddttn;
        echo '</div>';

        echo '<div style="float:left; margin-bottom:10px; border-bottom:1px #D7D7D7 solid">';

        if (!empty($res['oblozka'])) {
            echo '<div><img style="
                box-shadow:2px 2px 5px rgba(0,0,0,0.3);
                float:left;
                border:1px solid #C3D7D4;
                margin:0 10px 5px 10px;
                padding:10px;
                max-height:150px;
                width:auto;
            " src="'.$res['oblozka'].'" /></div>';
        }

        echo '<div style="margin-right:5px"><p>'.$short.'</p>';
        echo '<div class="zakladka" style="margin:0 0 0 20px"><br /></div></div></div>';
    }
}

echo GetNav($p, $num_pages, "slovo_padre") . '<hr style="width: 100%" />';
?>
</div>
<?php include 'footer.php'; ?>
</div>
</body>
</html>