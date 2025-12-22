<?php
// ===== ¬»ƒ≈ќ Ч 2 последних ролика =====
?>

<h2 class="section-header">
    <a href="video.php">¬идео</a>
</h2>

<?php
$vid_all = mysql_query("
    SELECT *
    FROM host1409556_barysh.video
    ORDER BY id DESC
    LIMIT 2
");

while ($vid = mysql_fetch_array($vid_all)) {

    // --- ƒата ---
    $dtn = $vid['data'];
    $y   = substr($dtn, 0, 4);
    $m   = substr($dtn, 5, 2);
    $d   = ltrim(substr($dtn, 8, 2), '0');
    $t   = substr($dtn, 11, 5);

    $months = [
        "01"=>"€нвар€","02"=>"феврал€","03"=>"марта","04"=>"апрел€","05"=>"ма€",
        "06"=>"июн€","07"=>"июл€","08"=>"августа","09"=>"сент€бр€","10"=>"окт€бр€",
        "11"=>"но€бр€","12"=>"декабр€"
    ];
    $m_text = $months[$m];

    $date_out = "$d $m_text $y г. $t";

    // --- »щем св€занную новость по коду видео ---
    $news_wer = mysql_fetch_array(mysql_query("
        SELECT data
        FROM host1409556_barysh.news_eparhia
        WHERE video = '".mysql_real_escape_string($vid['kod'])."'
        LIMIT 1
    "));

    $news_link = $news_wer && !empty($news_wer['data'])
        ? 'news_show.php?data=' . $news_wer['data']
        : $vid['link']; // если есть внешний линк или можно оставить пустым

    // --- „истим код плеера: делаем ширину 100% ---
    $code = $vid['kod'];
    $code = preg_replace('/width="46\%"/i', 'width="100%"', $code);
    $code = preg_replace('/width="\d+"/i', 'width="100%"', $code);
    $code = preg_replace('/height="\d+"/i', 'height="260"', $code);

    // ---  раткий текст / тема ---
    $title = $vid['tema'];
    $text  = strip_tags($vid['opisanie'] ?? '');
    if (empty($text)) {
        $text = $title;
    }
    if (mb_strlen($text) > 200) {
        $text = mb_substr($text, 0, 200).'Е';
    }
    ?>

    <div class="video-card">

        <div class="video-embed">
            <?= $code ?>
        </div>

        <div class="video-body">
            <div class="video-title">
                <?php if (!empty($news_link)): ?>
                    <a href="<?= $news_link ?>" target="_blank">
                        <?= $title ?>
                    </a>
                <?php else: ?>
                    <?= $title ?>
                <?php endif; ?>
            </div>

            <div class="video-meta">
                <?= $date_out ?>
            </div>

            <div class="video-text">
                <?= $text ?>
            </div>
        </div>

    </div>

<?php } ?>


<style>
.video-card {
    width: 100%;
    background: #fff;
    border-radius: 14px;
    padding: 14px;
    margin-bottom: 18px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.13);
    transition: 0.25s;
}
.video-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.19);
}

.video-embed {
    width: 100%;
    margin-bottom: 10px;
}
.video-embed iframe {
    border-radius: 10px;
}

.video-body { }

.video-title a {
    font-size: 17px;
    font-weight: 600;
    color: #003b9a;
    text-decoration: none;
}
.video-title a:hover {
    text-decoration: underline;
}

.video-meta {
    margin: 4px 0 8px 0;
    color: #777;
    font-size: 14px;
}

.video-text {
    font-size: 14px;
    color: #444;
    line-height: 1.4em;
}
</style>