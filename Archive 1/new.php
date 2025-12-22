<?
if (isset($_REQUEST[session_name()])) session_start();
$auth = $_SESSION['auth'];
$name_user = $_SESSION['name_user'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <? include 'head.php'; ?>
    <title>Новости епархии</title>

    <!-- Стили -->
    <link rel="stylesheet" href="/Index1.css">
    <link rel="stylesheet" href="/header.css">
    <link rel="stylesheet" href="/all.min.css">
</head>

<body>

<div class="page-wrapper">

    <? include 'golova.php'; ?>
    <? $new = yes; ?>
    <? include 'menu.php'; ?>
    <? include 'function.php'; ?>
    <? include 'content.php'; ?>

    <div id="osnovnoe" class="main-column">

        <h1 class="section-title">Новости епархии</h1>

        <?
        // Номер страницы
        if (!isset($_GET['page'])) {
            $p = 1;
        } else {
            $p = addslashes(strip_tags(trim($_GET['page'])));
            if ($p < 1) $p = 1;
        }

        $num_elements = 10;
        $cutoff = "2025-11-01 00:00:00";

        // Подсчёт количества новостей
        $total_local = mysql_result(mysql_query("SELECT COUNT(*) FROM host1409556_barysh.news_eparhia"), 0, 0);
        $total_external = mysql_result(mysql_query("SELECT COUNT(*) FROM host1409556_barysh.news_mitropolia WHERE section='barysh_tag' AND data >= '$cutoff'"), 0, 0);

        $total = $total_local + $total_external;
        $num_pages = $total > 0 ? ceil($total / $num_elements) : 1;

        if ($p > $num_pages) $p = $num_pages;

        $start = ($p - 1) * $num_elements;
        if ($start < 0) $start = 0;

        echo '<div class="mb-3">'.GetNav($p, $num_pages, "news").'</div>';

        // Запрос всех новостей
        $sel = "
            SELECT * FROM (
                SELECT data, tema, kratko, oblozka, video, views, NULL AS link, 'local' AS source
                FROM host1409556_barysh.news_eparhia
            UNION ALL
                SELECT data, tema, kratko, oblozka, NULL AS video, NULL AS views, link, 'mitropolia' AS source
                FROM host1409556_barysh.news_mitropolia
                WHERE section='barysh_tag' AND data >= '$cutoff'
            ) AS u
            ORDER BY data DESC
            LIMIT $start, $num_elements
        ";

        $query = mysql_query($sel);
        if (mysql_num_rows($query) > 0) {

            while ($row = mysql_fetch_assoc($query)) {

                $dtn = $row['data'];
                $yyn = substr($dtn, 0, 4);
                $mmn = substr($dtn, 5, 2);
                $ddn = (int)substr($dtn, 8, 2);

                $months = [
                    "01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая","06"=>"июня",
                    "07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября","11"=>"ноября","12"=>"декабря"
                ];
                $mm1n = $months[$mmn];

                // Текст
                $text = $row['kratko'];
                if ($row['source'] == 'local') {
                    $patterns = [
                        '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
                        '/\n/', '/(?:\/{3})/', '/(?:\|{3})/', '/@[^@]+@/', '/(?:\{{3})/', '/(?:\}{3})/'
                    ];
                    $replace = ['${2}', '</p><p>', '', '', '', '', ''];
                    $text = preg_replace($patterns, $replace, $text);
                } else {
                    $text = nl2br($text);
                }

                // Ссылка
                $title_link = ($row['source']=='local') ? 'news_show.php?data='.$row['data'] : $row['link'];
                $target = ($row['source']=='local') ? '_self' : '_blank';

                // Обложка
                $img_url = '';
                if (!empty($row['oblozka'])) {
                    if ($row['source']=='local') {
                        $img_url = 'FOTO_MINI/'.$row['oblozka'].'.jpg';
                    } else {
                        if (strpos($row['oblozka'], 'http') === 0) $img_url = $row['oblozka'];
                        elseif (strpos($row['oblozka'], '//') === 0) $img_url = 'https:'.$row['oblozka'];
                        else $img_url = 'https://mitropolia-simbirsk.ru'.$row['oblozka'];
                    }
                }
        ?>

        <!-- КАРТОЧКА НОВОСТИ -->
        <div class="card news-item">

            <div class="news-item-header">
                <span class="title">
                    <a href="<?= $title_link ?>" target="<?= $target ?>">
                        <?= $row['tema'] ?>
                    </a>
                </span>

                <div class="news-item-meta">
                    <span class="date"><?= $ddn.' '.$mm1n.' '.$yyn ?></span>

                    <? if ($row['source']=='local' && $row['video']) { ?>
                        <span class="meta"><i class="fa fa-film"></i> Видео</span>
                    <? } ?>

                    <? if ($row['source']=='mitropolia') { ?>
                        <span class="meta"><i class="fa fa-globe"></i> митрополия</span>
                    <? } ?>
                </div>
            </div>

            <div class="news-item-body">
                <? if (!empty($img_url)) { ?>
                    <div class="news-item-image">
                        <a href="<?= $title_link ?>" target="<?= $target ?>">
                            <img src="<?= $img_url ?>" alt="">
                        </a>
                    </div>
                <? } ?>

                <div class="news-item-text">
                    <p><?= $text ?></p>

                    <? if ($row['source']=='local') { ?>
                        <div class="meta">
                            <i class="fa fa-eye"></i> <?= $row['views'] ?> просмотров
                        </div>
                    <? } ?>
                </div>
            </div>

        </div>

        <? } } ?>

        <!-- Навигация -->
        <div class="mb-3">
            <?= GetNav($p, $num_pages, "news"); ?>
        </div>

    </div>

    <? include 'footer.php'; ?>

</div>

</body>
</html>