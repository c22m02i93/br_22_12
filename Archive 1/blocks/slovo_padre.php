<?php
// ===== СЛОВО АРХИПАСТЫРЯ — 2 карточки в новом стиле =====
?>

<h2 class="section-header">
    <a href="slovo_padre.php">Слово архипастыря</a>
</h2>

<?php
// Берём 2 последних материала
$padre_all = mysql_query("
    SELECT tema, kratko, data, oblozka, link
    FROM host1409556_barysh.news_mitropolia
    WHERE section = 'slovo'
    ORDER BY data DESC
    LIMIT 2
");

while ($news = mysql_fetch_array($padre_all)) {

    // ==== ДАТА ====
    $dtn = $news['data'];

    $y = substr($dtn,0,4);
    $m = substr($dtn,5,2);
    $d = ltrim(substr($dtn,8,2), "0");
    $t = substr($dtn,11,5);

    $months = [
        "01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая",
        "06"=>"июня","07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября",
        "11"=>"ноября","12"=>"декабря"
    ];
    $m_text = $months[$m];

    $date_out = "$d $m_text $y г. $t";


    // ==== ТЕКСТ ====
    $text = strip_tags($news['kratko']);
    $text_short = mb_substr($text, 0, 260) . '…';


    // ==== ВЫВОД КАРТОЧКИ ====
?>
<div class="padre-card">

    <?php if (!empty($news['oblozka'])): ?>
        <div class="padre-img">
            <a href="<?= $news['link'] ?>" target="_blank">
                <img src="<?= $news['oblozka'] ?>">
            </a>
        </div>
    <?php endif; ?>

    <div class="padre-body">

        <div class="padre-title">
            <a href="<?= $news['link'] ?>" target="_blank">
                <?= $news['tema'] ?>
            </a>
        </div>

        <div class="padre-meta">
            <?= $date_out ?>
        </div>

        <div class="padre-text">
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
    border-bottom: 5px solid #E6E0C6;
    padding-bottom: 4px;
}
.section-header a {
    color: #7A6D42;
    text-decoration: none;
    border-bottom: 5px solid #E6E0C6;
}

/* Карточка */
.padre-card {
    width: 100%;
    display: flex;
    gap: 18px;
    padding: 18px;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 14px rgba(0,0,0,0.13);
    margin-bottom: 22px;
    transition: 0.25s;
}
.padre-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.18);
}

/* Картинка */
.padre-img img {
    width: 180px;
    height: 125px;
    object-fit: cover;
    border-radius: 10px;
}

/* Контент */
.padre-body { flex: 1; }

.padre-title a {
    font-size: 18px;
    font-weight: 600;
    color: #003b9a;
    text-decoration: none;
}
.padre-title a:hover { text-decoration: underline; }

.padre-meta {
    margin: 6px 0 10px 0;
    color: #777;
    font-size: 14px;
}

.padre-text {
    font-size: 15px;
    color: #444;
    line-height: 1.45em;
}
</style>