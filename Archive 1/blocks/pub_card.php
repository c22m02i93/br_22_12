<?php
$dtn = $pub['data'];

$yyn = substr($dtn,0,4);
$mmn = substr($dtn,5,2);
$ddn = substr($dtn,8,2);
if ($ddn[0]=='0') $ddn = substr($ddn,1);

$months = [
"01"=>"€нвар€","02"=>"феврал€","03"=>"марта","04"=>"апрел€","05"=>"ма€",
"06"=>"июн€","07"=>"июл€","08"=>"августа","09"=>"сент€бр€","10"=>"окт€бр€",
"11"=>"но€бр€","12"=>"декабр€"
];
$mm1n = $months[$mmn];

$date_out = $ddn." ".$mm1n." ".$yyn." г. ".substr($dtn,11,5);

$text = strip_tags($pub['kratko']);
if (mb_strlen($text)>180) $text = mb_substr($text,0,180).'...';

$img = "FOTO_MINI/{$pub['oblozka']}.jpg";
?>

<a href="pub_show.php?data=<?= $pub['data'] ?>" style="text-decoration:none; color:inherit;">
<div class="news-card">

    <div class="news-card-img">
        <img src="<?= $img ?>" alt="">
    </div>

    <div class="news-card-body">
        <div class="news-card-title"><?= $pub['tema'] ?></div>
        <div class="news-card-meta">
            <span><?= $date_out ?></span>
            <span class="views"><img src="IMG/views.png"> <?= $pub['views'] ?></span>
        </div>
        <div class="news-card-text"><?= $text ?></div>
    </div>

</div>
</a>