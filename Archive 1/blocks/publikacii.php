<?php
// Современный блок «Публикации» — стиль 1 в 1 как у новостей

echo '<div class="index_block">
        <h2><a href="pub.php">Публикации</a></h2>
        <br />';

// Берём 3 последние публикации
$pub_all = mysql_query("
    SELECT *
    FROM host1409556_barysh.publikacii
    ORDER BY data DESC
    LIMIT 3
");

while ($pub = mysql_fetch_array($pub_all)) {

    // Дата
    $dtn = $pub['data'];
    $yyn = substr($dtn, 0, 4);
    $mmn = substr($dtn, 5, 2);
    $ddn = substr($dtn, 8, 2);
    if ($ddn[0] === '0') $ddn = substr($ddn, 1);
    $time = substr($dtn, 11, 5);

    $months = [
        "01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая",
        "06"=>"июня","07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября",
        "11"=>"ноября","12"=>"декабря"
    ];
    $mm1n = $months[$mmn];
    $date_out = $ddn." ".$mm1n." ".$yyn." г. ".$time;

    // Текст обрезаем
    $text = strip_tags($pub['kratko']);
    if (mb_strlen($text) > 180) {
        $text = mb_substr($text, 0, 180).'...';
    }

    // Картинка
    if (!empty($pub['oblozka'])) {
        $img = 'FOTO_MINI/'.$pub['oblozka'].'.jpg';
    } else {
        $img = 'IMG/no_image.jpg';
    }

    echo '
    <a href="pub_show.php?data='.$pub['data'].'" style="text-decoration:none; color:inherit;">
    <div class="news-card">

        <div class="news-card-img">
            <img src="'.$img.'" alt="">
        </div>

        <div class="news-card-body">

            <div class="news-card-title">'.$pub['tema'].'</div>

            <div class="news-card-meta">
                <span>'.$date_out.'</span>
                <span class="views"><img src="IMG/views.png"> '.$pub['views'].'</span>
            </div>

            <div class="news-card-text">'.$text.'</div>

        </div>

    </div>
    </a>
    ';
}

echo '</div><br />';
?>

<style>
.news-card {
    width: 100%;
    display: flex;
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.10);
    transition: 0.25s;
}
.news-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.18);
}

.news-card-img img {
    width: 180px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 15px;
}

.news-card-body {
    flex: 1;
}

.news-card-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 6px;
}

.news-card-meta {
    font-size: 14px;
    color: #999;
    margin-bottom: 6px;
}

.news-card-meta img {
    width: 16px;
    opacity: 0.7;
}

.news-card-text {
    font-size: 15px;
    color: #444;
    line-height: 1.4em;
}

.views {
    margin-left: 10px;
}
</style>