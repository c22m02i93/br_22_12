<?php
// ===== ГЛАВНАЯ НОВОСТЬ ДНЯ — стиль как на скриншоте =====

// Получаем запись
$news_day = mysql_query("SELECT * FROM host1409556_barysh.news_day");
$new_day  = mysql_fetch_array($news_day);
$dtn_day  = $new_day['data'];

// Формат даты
$y = substr($dtn_day,0,4);
$m = substr($dtn_day,5,2);
$d = ltrim(substr($dtn_day,8,2), "0");
$t = substr($dtn_day,11,5);

$months = [
    "01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая",
    "06"=>"июня","07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября",
    "11"=>"ноября","12"=>"декабря"
];

$month_text = $months[$m];

$date_out = "$d $month_text $y г. $t";


// ===== ВЫБИРАЕМ КОЛИЧЕСТВО ПРОСМОТРОВ ====
$views = 0;
$video = false;

switch ($new_day['page']) {
    case 'news':
        $row = mysql_fetch_array(mysql_query("
            SELECT views, video FROM host1409556_barysh.news_eparhia
            WHERE data='$dtn_day'"));
        break;

    case 'anons':
        $row = mysql_fetch_array(mysql_query("
            SELECT views FROM host1409556_barysh.anons
            WHERE data='$dtn_day'"));
        break;

    case 'pub':
        $row = mysql_fetch_array(mysql_query("
            SELECT views FROM host1409556_barysh.publikacii
            WHERE data='$dtn_day'"));
        break;

    case 'slovo_padre':
        $row = mysql_fetch_array(mysql_query("
            SELECT views FROM host1409556_barysh.padre
            WHERE data='$dtn_day'"));
        break;
}

if (!empty($row['views'])) $views = $row['views'];
if (!empty($row['video'])) $video = true;


// ===== КОРРЕКТНЫЙ ТЕКСТ =====
$patterns = [
    '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
    '/\n/', '/(?:\/{3})/','/(?:\|{3})/','/@[^@]+@/',
    '/(?:\{{3})/','/(?:\}{3})/','/\[/', '/\]/'
];
$replace = [
    '${2}', '</p><p>', '', '', '',
    '', '', '', ''
];
$text = preg_replace($patterns, $replace, $new_day['text']);


// ========= ВЫВОД НОВОЙ СТИЛЬНОЙ КАРТОЧКИ =========
?>

<div class="main-news-card">

    <?php if (!empty($new_day['oblozka'])): ?>
        <div class="mn-img">
            <a href="<?= $new_day['page'] ?>_show.php?data=<?= $new_day['data'] ?>">
                <img src="DAY/<?= $new_day['oblozka'] ?>.jpg">
            </a>
        </div>
    <?php endif; ?>

    <div class="mn-body">
        <div class="mn-title">
            <a href="<?= $new_day['page'] ?>_show.php?data=<?= $new_day['data'] ?>">
                <?= $new_day['tema'] ?>
            </a>
        </div>

        <div class="mn-meta">
            <?= $date_out ?>
            <span class="mn-views">
                <?php if ($video): ?> (+ Видео) <?php endif; ?>
                <img src="IMG/views.png"> <?= $views ?>
            </span>
        </div>

        <div class="mn-text">
            <?= mb_substr(strip_tags($text), 0, 250) ?>...
        </div>
    </div>

</div>


<style>
.main-news-card {
    width: 100%;
    display: flex;
    gap: 18px;
    padding: 18px;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    margin-bottom: 25px;
}

.mn-img img {
    width: 260px;
    height: 170px;
    object-fit: cover;
    border-radius: 10px;
}

.mn-body { flex: 1; }

.mn-title a {
    font-size: 20px;
    font-weight: 600;
    color: #003b9a;
    text-decoration: none;
}
.mn-title a:hover { text-decoration: underline; }

.mn-meta {
    margin: 6px 0 10px 0;
    color: #777;
    font-size: 14px;
}
.mn-meta img {
    width: 16px;
    opacity: 0.7;
}
.mn-views { margin-left: 10px; }

.mn-text {
    font-size: 15px;
    color: #444;
    line-height: 1.45em;
}
</style>