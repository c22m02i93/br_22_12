<?php
// ===== АНОНСЫ — стиль карточек как в новом дизайне =====

// Заголовок
?>
<h2 class="section-header">
    <a href="anons.php">Анонсы и объявления</a>
</h2>

<?php
// Не показывать новость дня
$dtn_day = $new_day['data'];

// 2 последних анонса
$rows = mysql_query("
    SELECT *
    FROM host1409556_barysh.anons
    WHERE data != '$dtn_day'
    ORDER BY data DESC
    LIMIT 2
");

while ($an = mysql_fetch_array($rows)) {

    // ДАТА
    $dtn = $an['data'];

    $y = substr($dtn,0,4);
    $m = substr($dtn,5,2);
    $d = ltrim(substr($dtn,8,2), '0');
    $t = substr($dtn,11,5);

    $months_short = [
        "01"=>"янв.","02"=>"фев.","03"=>"мар.","04"=>"апр.","05"=>"мая",
        "06"=>"июн.","07"=>"июл.","08"=>"авг.","09"=>"сен.","10"=>"окт.",
        "11"=>"нояб.","12"=>"дек."
    ];
    $m_text = $months_short[$m];

    $date_out = "$d $m_text $y г. $t";

    // ТЕКСТ
    $text = strip_tags($an['kratko']);
    $text_short = mb_substr($text, 0, 220).'…';
?>

<div class="anons-card">

    <div class="anons-body">

        <div class="anons-title">
            <a href="anons_show.php?data=<?= $an['data'] ?>">
                <?= $an['tema'] ?>
            </a>
        </div>

        <div class="anons-meta">
            <?= $date_out ?>
            <span class="views">
                <img src="IMG/views.png"> <?= $an['views'] ?>
            </span>
        </div>

        <div class="anons-text">
            <?= $text_short ?>
        </div>

    </div>
</div>

<?php } ?>


<style>
/* Заголовок секции */
.section-header {
    font-size: 22px;
    font-weight: bold;
    border-bottom: 5px solid #F0D0C8;
    padding-bottom: 4px;
    margin-top: 10px;
}
.section-header a {
    color: #A35241;
    text-decoration: none;
    border-bottom: 5px solid #F0D0C8;
}

/* Карточка */
.anons-card {
    width: 100%;
    background: #fff;
    border-radius: 14px;
    padding: 15px 18px;
    margin-bottom: 20px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.13);
    transition: 0.25s;
}
.anons-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.18);
}

/* Контент */
.anons-title a {
    font-size: 20px;
    font-weight: 600;
    color: #003b9a;
    text-decoration: none;
}
.anons-title a:hover { text-decoration: underline; }

.anons-meta {
    margin: 6px 0 10px 0;
    color: #777;
    font-size: 14px;
}
.anons-meta img {
    width: 16px;
    opacity: 0.7;
}
.views { margin-left: 10px; }

.anons-text {
    font-size: 15px;
    color: #444;
    line-height: 1.45em;
}
</style>