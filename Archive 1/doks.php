<?php
// -------------------- СЕССИЯ --------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth      = isset($_SESSION['auth']) ? (int)$_SESSION['auth'] : 0;
$name_user = isset($_SESSION['name_user']) ? $_SESSION['name_user'] : '';

$is_admin = ($auth === 1);

// -------------------- ПОДКЛЮЧЕНИЯ (без вывода) --------------------
include 'function.php';
include 'db.php';

// -------------------- CSRF ТОКЕН --------------------
if (empty($_SESSION['csrf_token'])) {
    // Совместимо со старыми PHP (без random_bytes)
    if (function_exists('openssl_random_pseudo_bytes')) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(16));
    } else {
        $_SESSION['csrf_token'] = md5(uniqid('', true));
    }
}

// -------------------- УДАЛЕНИЕ ДОКУМЕНТА (ТОЛЬКО АДМИН) --------------------
if (
    $is_admin &&
    isset($_POST['delete_doc']) &&
    isset($_POST['csrf_token']) &&
    $_POST['csrf_token'] === $_SESSION['csrf_token']
) {
    $del_date     = isset($_POST['del_date']) ? mysql_real_escape_string($_POST['del_date']) : '';
    $del_year     = isset($_POST['del_year']) ? (int)$_POST['del_year'] : 0;
    $del_nomer    = isset($_POST['del_nomer']) ? (int)$_POST['del_nomer'] : 0;
    $del_tematika = isset($_POST['del_tematika']) ? mysql_real_escape_string($_POST['del_tematika']) : '';

    if ($del_date !== '' && $del_year > 0 && $del_tematika !== '') {
        mysql_query("
            DELETE FROM host1409556_barysh.doks
            WHERE `date`     = '$del_date'
              AND `year`     = '$del_year'
              AND `nomer`    = '$del_nomer'
              AND `tematika` = '$del_tematika'
            LIMIT 1
        ");
    }

    // Редирект обратно на тот же раздел/страницу
    $back = 'doks.php';
    if (isset($_GET['tip']) && $_GET['tip'] !== '') {
        $back .= '?tip=' . urlencode($_GET['tip']);
        if (isset($_GET['page']) && $_GET['page'] !== '') {
            $back .= '&page=' . urlencode($_GET['page']);
        }
    }

    header("Location: " . $back);
    exit;
}

// -------------------- ПАРАМЕТРЫ РАЗДЕЛА --------------------
$tip = isset($_GET['tip']) ? addslashes(strip_tags(trim($_GET['tip']))) : '';

$tip_titles = array(
    'ukaz'          => 'Указы',
    'raspor'        => 'Распоряжения',
    'cirk'          => 'Циркуляры',
    'udostoverenie' => 'Удостоверения',
);

$section_title = ($tip && isset($tip_titles[$tip])) ? $tip_titles[$tip] : 'Документы';
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include 'head.php'; ?>
    <title><?php echo $section_title; ?></title>

    <link rel="stylesheet" href="/Index1.css">
    <link rel="stylesheet" href="/header.css">
    <link rel="stylesheet" href="/all.min.css">
</head>

<body>
<div class="page-wrapper">

    <?php include 'golova.php'; ?>
    <?php include 'menu.php'; ?>
    <?php include 'content.php'; ?>

    <div id="osnovnoe" class="main-column card news-list-block">

        <h1 class="section-title"><?php echo $section_title; ?></h1>

        <?php if (empty($tip)) { ?>

            <div class="news-item news-entry">
                <div class="news-entry__frame">
                    <div class="news-entry__content">
                        <div class="news-entry__text">
                            <p>Выберите раздел, чтобы просмотреть документы.</p>
                            <div class="news-entry__tags">
                                <a class="btn" href="doks.php?tip=ukaz">Указы</a>
                                <a class="btn" href="doks.php?tip=raspor">Распоряжения</a>
                                <a class="btn" href="doks.php?tip=cirk">Циркуляры</a>
                                <a class="btn" href="doks.php?tip=udostoverenie">Удостоверения</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } else { ?>

            <?php
            // -------------------- ПАГИНАЦИЯ --------------------
            if (!isset($_GET['page'])) {
                $p = 1;
            } else {
                $p = addslashes(strip_tags(trim($_GET['page'])));
                if ($p < 1) $p = 1;
            }

            $num_elements = 10;

            $total = mysql_result(
                mysql_query("SELECT COUNT(*) FROM host1409556_barysh.doks WHERE tematika = '$tip'"),
                0,
                0
            );

            $num_pages = ($total > 0) ? ceil($total / $num_elements) : 1;

            if ($p > $num_pages) $p = $num_pages;
            if ($p < 1) $p = 1;

            $start = ($p - 1) * $num_elements;
            if ($start < 0) $start = 0;

            echo '<div class="mb-3">' . GetNavtip($p, $num_pages, "doks", $tip) . '</div>';

            $sel = "SELECT * FROM host1409556_barysh.doks
                    WHERE tematika = '$tip'
                    ORDER BY date DESC
                    LIMIT $start, $num_elements";

            $query = mysql_query($sel);

            // -------------------- ВЫВОД --------------------
            if (mysql_num_rows($query) > 0) {
                while ($res = mysql_fetch_assoc($query)) {

                    $dtn = $res['date'];
                    $yyn = substr($dtn, 0, 4);
                    $mmn = substr($dtn, 5, 2);
                    $ddn = (int)substr($dtn, 8, 2);

                    $months = array(
                        "01" => "января",
                        "02" => "февраля",
                        "03" => "марта",
                        "04" => "апреля",
                        "05" => "мая",
                        "06" => "июня",
                        "07" => "июля",
                        "08" => "августа",
                        "09" => "сентября",
                        "10" => "октября",
                        "11" => "ноября",
                        "12" => "декабря"
                    );

                    $mm1n = isset($months[$mmn]) ? $months[$mmn] : "";
                    $date_text = $ddn . ' ' . $mm1n . ' ' . $yyn . ' года';

                    $patterns = array(
                        '/(?:\/{3})(.+)(?:\/{3})/U',
                        '/(?:\|{3})(.+)(?:\|{3})/U',
                        '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
                        '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})/i',
                        '/(?:\{{3})([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*(?:@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})))(?:\}{3})/',
                        '/\n/',
                        '/@R(\d+)[-]?([^@]*)@/',
                        '/@L(\d+)[-]?([^@]*)@/',
                        '/(?:\[{3})(([0-9]*[^\]{3}]*)*)(?:\]{3})/'
                    );

                    $replace = array(
                        '<i>${1}</i>',
                        '<b>${1}</b>',
                        '<a href="${1}" target="_blank">${2}</a>',
                        '<a href="${1}" target="_blank">${1}</a>',
                        '<a href="mailto:${1}">${1}</a>',
                        '</p><p>',
                        '<span class="photos"><a href="FOTO/${1}.jpg" rel="example_group"><img style="border: 1px solid #C3D7D4; margin: 5px 10px 5px 10px;display: block;float: right;box-shadow: 2px 2px 5px rgba(0,0,0,0.3); padding: 10px" src="FOTO_MINI/${1}.jpg" alt="${2}" title="${2}" /></a></span>',
                        '<span class="photos"><a href="FOTO/${1}.jpg" rel="example_group"><img style="border: 1px solid #C3D7D4; margin: 5px 10px 5px 10px;display: block;float: left;box-shadow: 2px 2px 5px rgba(0,0,0,0.3); padding: 10px" src="FOTO_MINI/${1}.jpg" alt="${2}" title="${2}" /></a></span>',
                        '<div style="text-align: center; font-weight: bolder; width:100%; color:#743C00">${1}</div>'
                    );

                    $text = preg_replace($patterns, $replace, $res['text']);

                    // Тип документа
                    switch ($res['tematika']) {
                        case 'ukaz':
                            $doc_type = 'Указ';
                            break;
                        case 'raspor':
                            $doc_type = 'Распоряжение';
                            break;
                        case 'cirk':
                            $doc_type = 'Циркуляр';
                            break;
                        case 'udostoverenie':
                            $doc_type = 'Удостоверение о рукоположении в сан ' . $res['name'];
                            break;
                        default:
                            $doc_type = $section_title;
                    }

                    // Правый блок с именем (не для удостоверений)
                    $right_title = '';
                    if ($res['tematika'] != 'udostoverenie' && !empty($res['name'])) {
                        $right_title = '<div style="padding-left: 25%; padding-right: 15px; float:right;
                            text-align: right; margin-top: 5px; margin-bottom: 5px">
                            <i>' . $res['name'] . '</i>
                        </div><br /><br />';
                    }
                    ?>

                    <div class="news-item news-entry">
                        <div class="news-entry__frame">

                            <div class="news-entry__title-row">
                                <span class="news-entry__title">
                                    <?php echo $doc_type; ?>
                                    <?php if (!empty($res['nomer'])) { ?>
                                        № <?php echo $res['nomer']; ?>
                                    <?php } ?>
                                    от <?php echo $date_text; ?>
                                </span>

                                <?php echo $right_title; ?>
                            </div>

                            <div class="news-entry__content">
                                <div class="news-entry__text">
                                    <p><?php echo $text; ?></p>
                                </div>

                                <?php if ($is_admin) { ?>
                                    <div style="margin-top:10px; text-align:right;">
                                        <form method="post"
                                              action="doks.php?tip=<?php echo urlencode($tip); ?>&page=<?php echo urlencode($p); ?>"
                                              onsubmit="return confirm('Удалить этот документ?');"
                                              style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <input type="hidden" name="delete_doc" value="1">

                                            <input type="hidden" name="del_date" value="<?php echo htmlspecialchars($res['date']); ?>">
                                            <input type="hidden" name="del_year" value="<?php echo (int)$res['year']; ?>">
                                            <input type="hidden" name="del_nomer" value="<?php echo (int)$res['nomer']; ?>">
                                            <input type="hidden" name="del_tematika" value="<?php echo htmlspecialchars($res['tematika']); ?>">

                                            <button type="submit" class="btn" style="background:#b30000;color:#fff;border:0;">
                                                Удалить
                                            </button>
                                        </form>
                                    </div>
                                <?php } ?>

                            </div>

                        </div>
                    </div>

                <?php
                }
            }
            ?>

            <div class="mb-3">
                <?php echo GetNavtip($p, $num_pages, "doks", $tip); ?>
            </div>

        <?php } ?>

    </div>

    <?php include 'footer.php'; ?>

</div>
</body>
</html>