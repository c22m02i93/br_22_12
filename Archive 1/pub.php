<?
if (isset($_REQUEST[session_name()])) session_start();
$auth = $_SESSION['auth'];
$name_user = $_SESSION['name_user'];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <? include 'head.php'; ?>
    <title>ѕубликации</title>

    <link rel="stylesheet" href="/Index1.css">
    <link rel="stylesheet" href="/header.css">
    <link rel="stylesheet" href="/all.min.css">
</head>

<body>

<div class="page-wrapper">

    <? include 'golova.php'; ?>
    <? $pub = yes; ?>
    <? include 'menu.php'; ?>
    <? include 'function.php'; ?>
    <? include 'db.php'; ?>
    <? include 'content.php'; ?>

    <div id="osnovnoe" class="main-column card news-list-block">

        <h1 class="section-title"> ѕубликации </h1>

        <?
        // Ќомер страницы
        if (!isset($_GET['page'])) {
            $p = 1;
        } else {
            $p = addslashes(strip_tags(trim($_GET['page'])));
            if ($p < 1) $p = 1;
        }

        $num_elements = 10;
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM host1409556_barysh.publikacii"), 0, 0);
        $num_pages = $total > 0 ? ceil($total / $num_elements) : 1;

        if ($p > $num_pages) $p = $num_pages;
        if ($p < 1) $p = 1;

        $start = ($p - 1) * $num_elements;
        if ($start < 0) $start = 0;

        echo '<div class="mb-3">' . GetNav($p, $num_pages, "pub") . '</div>';

        $sel = "SELECT * FROM host1409556_barysh.publikacii ORDER BY data DESC LIMIT $start, $num_elements";
        $query = mysql_query($sel);

        if (mysql_num_rows($query) > 0) {
            while ($res = mysql_fetch_assoc($query)) {

                $dtn = $res['data'];
                $yyn = substr($dtn, 0, 4);
                $mmn = substr($dtn, 5, 2);
                $ddn = (int)substr($dtn, 8, 2);

                $months = [
                    "01" => "€нвар€","02" => "феврал€","03" => "марта","04" => "апрел€","05" => "ма€","06" => "июн€",
                    "07" => "июл€","08" => "августа","09" => "сент€бр€","10" => "окт€бр€","11" => "но€бр€","12" => "декабр€"
                ];

                $mm1n = isset($months[$mmn]) ? $months[$mmn] : "";
                $date_text = $ddn . ' ' . $mm1n . ' ' . $yyn . ' года';
                $time_text = substr($dtn, 11, 5);

                $patterns = [
                    '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
                    '/\n/', '/(?:\/{3})/', '/(?:\|{3})/', '/@[^@]+@/', '/(?:\{{3})/', '/(?:\}{3})/'
                ];
                $replace = ['${2}', '</p><p>', '', '', '', '', ''];
                $text = preg_replace($patterns, $replace, $res['kratko']);

                $img_url = '';
                if (!empty($res['oblozka'])) {
                    $img_url = 'FOTO_MINI/' . $res['oblozka'] . '.jpg';
                }
        ?>

        <div class="news-item news-entry">

            <div class="news-entry__frame">

                <div class="news-entry__title-row">
                    <a class="news-entry__title" href="pub_show.php?data=<?= $res['data'] ?>">
                        <?= $res['tema'] ?>
                    </a>
                </div>

                <div class="news-entry__content">

                    <? if (!empty($img_url)) { ?>
                        <div class="news-entry__image">
                            <a href="pub_show.php?data=<?= $res['data'] ?>">
                                <img src="<?= $img_url ?>" alt="">
                            </a>
                        </div>
                    <? } ?>

                    <div class="news-entry__text">

                        <p><?= $text ?></p>

                        <div class="news-entry__meta-item news-entry__views">
                            <i class="fa-regular fa-calendar-days"></i> <?= $date_text ?>
                            <i class="fa-regular fa-clock"></i> <?= $time_text ?>
                            <i class="fa fa-eye"></i> <?= $res['views'] ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <?
            } // while
        } // if
        ?>

        <div class="mb-3">
            <?= GetNav($p, $num_pages, "pub"); ?>
        </div>

    </div>

    <? include 'footer.php'; ?>

</div>

</body>
</html>