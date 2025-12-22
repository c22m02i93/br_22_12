<?php
if (isset($_REQUEST[session_name()])) session_start();
$auth = $_SESSION['auth'];
$name_user = $_SESSION['name_user'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'head.php'; ?>
<title>Архиерейское служение</title>
<style>
.news-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
    transition: box-shadow 0.3s ease;
    display: flex;
    flex-direction: row;
}
.news-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
.news-card-image {
    flex-shrink: 0;
    width: 250px;
    height: 200px;
    object-fit: cover;
    display: block;
}
.news-card-image-placeholder {
    flex-shrink: 0;
    width: 250px;
    height: 200px;
    background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 12px;
}
.news-card-content {
    padding: 20px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}
.news-card-date {
    font-size: 12px;
    color: #999;
    margin-bottom: 8px;
    text-transform: uppercase;
}
.news-card-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 12px;
    line-height: 1.4;
    color: #333;
    flex-grow: 1;
}
.news-card-title a {
    color: #333;
    text-decoration: none;
}
.news-card-title a:hover {
    color: #0066cc;
    text-decoration: underline;
}
.news-card-text {
    font-size: 14px;
    line-height: 1.6;
    color: #666;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.news-card-footer {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    border-top: 1px solid #eee;
    padding-top: 12px;
}
.btn-read-more {
    display: inline-block;
    padding: 10px 20px;
    background: #0066cc;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    font-weight: bold;
    transition: background 0.3s ease;
}
.btn-read-more:hover {
    background: #0052a3;
}

@media (max-width: 768px) {
    .news-card {
        flex-direction: column;
    }
    .news-card-image,
    .news-card-image-placeholder {
        width: 100%;
        height: 200px;
    }
}
</style>
</head>
<body>
<div style="box-shadow: 0 0 20px rgba(0,0,0,0.5);">
<?php
include 'golova.php';
$raspisanie = true;
include 'menu.php';
include 'function.php';
include 'db.php';
include 'content.php';
?>

<div id="osnovnoe">
<h1>Архиерейское служение</h1>
<?php
// Настройки
$num_elements = 10;
$table = "host1409556_barysh.news_mitropolia";
$section = "arhipastry";

// Пагинация
if (!isset($_GET['page'])) $p = 1;
else {
    $p = intval($_GET['page']);
    if ($p < 1) $p = 1;
}

$total = mysql_result(mysql_query("SELECT COUNT(*) FROM $table WHERE section='$section'"), 0, 0);
$num_pages = ceil($total / $num_elements);
if ($p > $num_pages) $p = $num_pages;
$start = ($p - 1) * $num_elements;

echo GetNav($p, $num_pages, "raspisanie") . '<hr />';

// Выборка данных
$q = mysql_query("SELECT * FROM $table WHERE section='$section' ORDER BY data DESC LIMIT $start, $num_elements");
if (mysql_num_rows($q) > 0) {
    
    while ($res = mysql_fetch_assoc($q)) {
        // Форматирование даты
        $dtn = $res['data'];
        $yyn = substr($dtn, 0, 4);
        $mmn = substr($dtn, 5, 2);
        $ddn = substr($dtn, 8, 2);
        $hours = substr($dtn, 11, 5);
        $monthes = array("01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая","06"=>"июня","07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября","11"=>"ноября","12"=>"декабря");
        if ($ddn[0] == '0') $ddn = substr($ddn, 1);
        $mm1n = $monthes[$mmn];
        $formatted_date = $ddn.' '.$mm1n.' '.$yyn.' г. '.$hours;

        // Текст — первые 15-20 слов
        $plain = strip_tags($res['kratko']);
        $words = explode(' ', $plain);
        $short = implode(' ', array_slice($words, 0, 18));
        if (count($words) > 18) $short .= '...';

        // Вывод карточки горизонтальная
        echo '<div class="news-card">';
        
        if (!empty($res['oblozka'])) {
            echo '<img src="'.$res['oblozka'].'" alt="'.$res['tema'].'" class="news-card-image" />';
        } else {
            echo '<div class="news-card-image-placeholder">Нет изображения</div>';
        }
        
        echo '<div class="news-card-content">';
        echo '<div class="news-card-date">'.$formatted_date.'</div>';
        echo '<div class="news-card-title"><a href="'.htmlspecialchars($res['link']).'" target="_blank">'.$res['tema'].'</a></div>';
        echo '<div class="news-card-text">'.$short.'</div>';
        echo '<div class="news-card-footer">';
        echo '<a href="'.htmlspecialchars($res['link']).'" target="_blank" class="btn-read-more">Читать далее</a>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

echo '<hr />' . GetNav($p, $num_pages, "raspisanie");
?>
</div>
<?php include 'footer.php'; ?>
</div>
</body>
</html>