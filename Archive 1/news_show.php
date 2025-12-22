<? 
if (isset($_REQUEST[session_name()])) session_start ();
$auth=$_SESSION['auth'];
$name_user=$_SESSION['name_user'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php
$data= $_GET['data'];
$enter= $_GET['enter'];
$msg = $_GET['msg'];
$name = $_GET['name'];

$data = preg_replace("/%20/", " ", $data);

mysql_connect("localhost", "host1409556", "0f7cd928");
mysql_query("SET NAMES 'cp1251'");
if ($_GET['cron'] && $_GET['cron'] == '1') $table = 'news_eparhia_cron'; else $table = 'news_eparhia';
$news_all_wer = mysql_query("SELECT * FROM host1409556_barysh.$table WHERE data = '$data'");
$news_wer = mysql_fetch_array($news_all_wer); 

if ($enter || $auth == 1) {
    $views = $news_wer['views'];
} else {
    $views = $news_wer['views']+1;
    mysql_query("UPDATE host1409556_barysh.news_eparhia SET views = '$views' WHERE data = '$data'");
}
?>

<title><?php echo $news_wer['tema'];?> - Новости епархии</title>

<?php include 'head.php'; ?>

<!-- ? baguetteBox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/baguettebox.js@1.11.1/dist/baguetteBox.min.css">
<script src="https://cdn.jsdelivr.net/npm/baguettebox.js@1.11.1/dist/baguetteBox.min.js"></script>

</head>
<body>

<div style="box-shadow: 0 0 20px rgba(0,0,0,0.5);">

<?php
include 'golova.php';
include 'menu.php';
include 'content.php';
?>

<div id="osnovnoe">

<h1>Новости епархии</h1>

<?php   

$dtn = $news_wer['data']; 
$yyn = substr($dtn,0,4);
$mmn = substr($dtn,5,2);
$ddn = substr($dtn,8,2);

$months = array(
"01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая","06"=>"июня",
"07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября","11"=>"ноября","12"=>"декабря"
);

$mm1n = $months[$mmn];

$ddn = ltrim($ddn, "0");

$hours = substr($dtn,11,5); 
$ddttn = '<span class="date">'.$ddn.' '.$mm1n.' '.$yyn.' г. '.$hours.'</span>';

$patterns = array(
'/(?:\/{3})(.+)(?:\/{3})/U',
'/(?:\|{3})(.+)(?:\|{3})/U',
'/(?:\{{3})(http[s]*:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
'/(?:\{{3})(http[s]*:\/\/[^\s\[<\(\)\|]+)(?:\}{3})/i',
'/(?:\{{3})([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3}))(?:\}{3})/',
'/\n/',
'/@R(\d+)[-]?([^@]*)@/',
'/@L(\d+)[-]?([^@]*)@/',
'/(?:\[{3})(([0-9]*[^\]{3}]*)*)(?:\]{3})/'
);

$replace = array(
'<i>${1}</i>',
'<b>${1}</b>',
'<a href="${1}" target=_blank>${2}</a>',
'<a href="${1}" target=_blank>${1}</a>',
'<a href="mailto:${1}">${1}</a>',
'</p><p>',
'<span class="photos"><a href="FOTO/${1}.jpg" alt="${2}" title="${2}"><img style="border: 1px solid #C3D7D4; margin: 5px 10px 5px 10px;display: block;float: right;box-shadow: 2px 2px 5px rgba(0,0,0,0.3); padding: 10px" src="FOTO_MINI/${1}.jpg" /></a></span>',
'<span class="photos"><a href="FOTO/${1}.jpg" alt="${2}" title="${2}"><img style="border: 1px solid #C3D7D4; margin: 5px 10px 5px 10px;display: block;float: left;box-shadow: 2px 2px 5px rgba(0,0,0,0.3); padding: 10px" src="FOTO_MINI/${1}.jpg" /></a></span>',
'<div style="text-align: center; font-weight: bolder;Width:100%; color:#743C00">${1}</div>'
);

$text = preg_replace($patterns, $replace, $news_wer['text']);

if ($auth == 1) {
    echo '<a style="float: right; margin: 10px;" href="edit.php?table='.$table.'&data='.$data.'"><img src="IMG/edit.png" title="Редактировать" /></a>';
}

echo '<div class="block_title"><span class="title">'.$news_wer['tema'].'</span><br />'.$ddttn.'</div><br />';

echo '<div class="gallery">';

if ($news_wer['oblozka'])
    echo '<span class="photos"><a href="FOTO/'.$news_wer['oblozka'].'.jpg" alt="'.$news_wer['tema'].'" title="'.$news_wer['tema'].'"><img style="box-shadow: 2px 2px 5px rgba(0,0,0,0.3); display: inline;float: left;border: 1px solid #C3D7D4; margin: 0 10px 5px 10px; padding: 10px" src="FOTO_MINI/'.$news_wer['oblozka'].'.jpg"/></a></span>';

echo '<p>'.$text.'</p>';

if ($news_wer['video']) {
    echo '<hr /><p style="text-align: center;">'.$news_wer['video'].'</p>';
}

if ($news_wer['albom']) {
    echo '<hr /><div style="margin-left: 15px;">';
    $albom_mas = preg_split("/\s/", $news_wer['albom']);
    for ($j=0; $j<count($albom_mas); $j++) {
        echo '<span class="photos"><a href="FOTO/'.$albom_mas[$j].'.jpg"><img style="border: 1px solid #BEC7BE; box-shadow: 2px 2px 5px rgba(0,0,0,0.3);margin: 5px; padding: 10px" src="FOTO_MINI/'.$albom_mas[$j].'.jpg" /></a></span>';
    }
    echo '</div>';
}

echo '</div>'; // close gallery
?>

<br />
<p style="color:#444; font-style:italic">При копировании гиперссылка на <a style="color:#444; text-decoration:underline;" href="http://barysh-eparhia.ru/">сайт Барышской епархии</a> обязательна</p>
<hr />

<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="button" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj"></div> 

<?php
echo '<span class="views" style="margin: 0 0 0 20px">Просмотров: '.$views.'.</span>';
?>

</div>

<?php include 'footer.php'; ?>

</div>

<!-- ? Запуск lightbox -->
<script>
baguetteBox.run('.gallery', { animation: 'fadeIn' });
</script>

</body>
</html>