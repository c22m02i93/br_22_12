<?php
// НОВОСТЬ ДНЯ

$news_day = mysql_query("SELECT * FROM host1409556_barysh.news_day");
$new_day = mysql_fetch_array($news_day);
$dtn_day = $new_day['data'];

$yyn = substr($dtn_day,0,4);
$mmn = substr($dtn_day,5,2);
$ddn = substr($dtn_day,8,2);

// Русские месяцы
$months = [
    "01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая",
    "06"=>"июня","07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября",
    "11"=>"ноября","12"=>"декабря"
];
$mm1n = $months[$mmn];

if ($ddn[0] === "0") $ddn = substr($ddn,1);

$hours = substr($dtn_day,11,5);

$ddttn = '<span class="date">'.$ddn.' '.$mm1n.' '.$yyn.' г. '.$hours.'</span>';

$patterns = [
    '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
    '/\n/','/(?:\/{3})/','/(?:\|{3})/','/@[^@]+@/',
    '/(?:\{{3})/','/(?:\}{3})/','/\[/', '/\]/'
];
$replace  = [ '${2}','</p><p>','','','','','','','' ];

$text = preg_replace($patterns,$replace,$new_day['text']);

// Вывод
?>
<div id="new_day">
<?php
if ($new_day['облоzka'])
    echo '<div style="box-shadow:2px 2px 5px rgba(0,0,0,0.3);display:inline;float:left;border:1px solid #C3D7D4;margin:0 10px 5px 10px;padding:10px">
            <a href="'.$new_day['page'].'_show.php?data='.$new_day['data'].'">
                <img src="DAY/'.$new_day['облоzka'].'.jpg" />
            </a>
          </div>';

echo '<div class="block_title">
        <span class="title"><a href="'.$new_day['page'].'_show.php?data='.$new_day['data'].'">'.$new_day['tema'].'</a></span><br />'
        .$ddttn.
        '<span style="color:#777">';

if ($new_day['page'] == 'news') {
    $newvid = mysql_fetch_array(mysql_query("SELECT * FROM host1409556_barysh.news_eparhia WHERE data = '$dtn_day'"));
    if ($newvid['video']) echo ' (+ Видео)';
    echo ' <img src="IMG/views.png" /> '.$newvid['views'].'</span>';
}

if ($new_day['page'] == 'anons') {
    $newvid = mysql_fetch_array(mysql_query("SELECT * FROM host1409556_barysh.anons WHERE data = '$dtn_day'"));
    echo ' <img src="IMG/views.png" /> '.$newvid['views'].'</span>';
}

if ($new_day['page'] == 'pub') {
    $newvid = mysql_fetch_array(mysql_query("SELECT * FROM host1409556_barysh.publikacii WHERE data = '$dtn_day'"));
    echo ' <img src="IMG/views.png" /> '.$newvid['views'].'</span>';
}

if ($new_day['page'] == 'slovo_padre') {
    $newvid = mysql_fetch_array(mysql_query("SELECT * FROM host1409556_barysh.padre WHERE data = '$dtn_day'"));
    echo ' <img src="IMG/views.png" /> '.$newvid['views'].'</span>';
}

echo '</div><br />';
echo '<p>'.$text.'</p>';
?>
</div>