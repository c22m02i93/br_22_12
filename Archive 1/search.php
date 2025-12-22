<?
if (isset($_REQUEST[session_name()])) session_start();
$auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : null;
$name_user = isset($_SESSION['name_user']) ? $_SESSION['name_user'] : null;
?>
<!DOCTYPE html>
<html>
<head>
<?
include 'head.php';
?>
<title>Результаты поиска</title>

<style>
.search-card {
    border:1px solid #ccc;
    padding:15px;
    margin-bottom:15px;
    border-radius:8px;
    background:#fafafa;
}
.search-card:hover {
    background:#f0f0f0;
}
.search-card-title {
    font-size:18px;
    font-weight:bold;
    margin-bottom:5px;
}
.search-card-meta {
    font-size:13px;
    color:#666;
    margin-bottom:8px;
}
.search-card-text {
    font-size:15px;
    margin-bottom:10px;
}
.search-card-link {
    display:inline-block;
    padding:6px 12px;
    background:#005bbb;
    color:#fff;
    border-radius:4px;
    text-decoration:none;
    font-size:14px;
}
.search-card-link:hover {
    background:#004999;
}
</style>

</head>
<body>

<div style="box-shadow:0 0 20px rgba(0,0,0,0.5);">
<?
include 'golova.php';
include 'menu.php';
include 'db.php';      // подключение БД
include 'content.php';
?>

<div id="osnovnoe">
<h1>Результаты поиска</h1>

<input type="text" id="searchBox"
       placeholder="Введите запрос..."
       style="width:100%;padding:10px;font-size:18px;">

<div id="searchResults" style="margin-top:20px;"></div>

<?php
/* ========================================================
      AJAX-поиск для PHP 5.4 + mysql_connect + CP1251
   ======================================================== */

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {

    include 'db.php'; // на всякий случай

    $q_utf = trim(isset($_GET['q']) ? $_GET['q'] : '');
    if ($q_utf == '') {
        echo json_encode(array());
        exit;
    }

    // UTF-8 ? CP1251
    $q_cp  = iconv("UTF-8", "CP1251//IGNORE", $q_utf);
    $q_esc = mysql_real_escape_string($q_cp);

    $results = array();

    // Человеческие имена разделов
    $names = array(
        "news"             => "Новости",
        "publikacii"       => "Публикации",
        "doks"             => "Документы",
        "anons"            => "Анонсы",
        "news_day"         => "События дня",
        "news_eparhia"     => "Новости епархии",
        "news_eparhia_cron"=> "Новости епархии (архив)",
        "news_mitropolia"  => "Новости митрополии",
        "video"            => "Видео"
    );

    // Ссылки для некоторых таблиц
    $linkMap = array(
        "news"       => "news.php?id=",
        "publikacii" => "publikacii.php?id=",
        "doks"       => "dok.php?id=",
        "anons"      => "anons.php?id="
    );

    // Получаем список таблиц
    $tbl_q = mysql_query("SHOW TABLES", $GLOBALS['db']);
    if (!$tbl_q) {
        echo json_encode(array());
        exit;
    }

    while ($tbl = mysql_fetch_row($tbl_q)) {

        $table = $tbl[0];

        // Пропускаем явные служебные
        if ($table == "admin" || $table == "foto_col")
            continue;

        $col_q = mysql_query("SHOW COLUMNS FROM `$table`", $GLOBALS['db']);
        if (!$col_q) continue;

        $idField = null;
        $textFields = array();

        while ($col = mysql_fetch_assoc($col_q)) {
            $type = strtolower($col['Type']);

            if ($col['Key'] == 'PRI') {
                $idField = $col['Field'];
            }

            if (strpos($type,'char') !== false || strpos($type,'text') !== false) {
                $textFields[] = $col['Field'];
            }
        }

        if (!$idField || !count($textFields)) continue;

        // WHERE
        $whereParts = array();
        foreach ($textFields as $f) {
            $whereParts[] = "`$f` LIKE '%$q_esc%'";
        }
        $where = implode(" OR ", $whereParts);

        // SELECT
        $fields = "`$idField`, " . implode(", ", $textFields);
        $sql = "SELECT $fields FROM `$table` WHERE $where LIMIT 10";

        $rs = mysql_query($sql, $GLOBALS['db']);
        if (!$rs) continue;

        while ($row = mysql_fetch_assoc($rs)) {

            // конвертация CP1251 ? UTF-8 и чистка
            foreach ($row as $k => $v) {
                if (is_string($v)) {
                    $v = iconv("CP1251", "UTF-8//IGNORE", $v);
                    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', ' ', $v);
                    $row[$k] = $v;
                }
            }

            // заголовок пытаемся взять из полей 'tema', 'name' и т.п.
            $title = '';
            if (isset($row['tema']) && $row['tema'] != '') $title = $row['tema'];
            elseif (isset($row['name']) && $row['name'] != '') $title = $row['name'];
            elseif (isset($row['zag']) && $row['zag'] != '') $title = $row['zag'];

            // если заголовка нет — подставим название раздела + ID
            if ($title == '') {
                $title = $table . " #" . $row[$idField];
            }

            // краткий текст (snippet)
            $snippet = '';
            if (isset($row['text']) && $row['text'] != '') {
                $snippet = $row['text'];
            } else {
                // если поля text нет — берём склейку других полей
                $snippet = implode(" ", $row);
            }
            $snippet = mb_substr($snippet, 0, 300, 'UTF-8');

            // ссылка
            $link = '';
            if (isset($linkMap[$table])) {
                $link = $linkMap[$table] . $row[$idField];
            }

            // название раздела
            $section = isset($names[$table]) ? $names[$table] : $table;

            $results[] = array(
                "section" => $section,
                "table"   => $table,
                "id"      => $row[$idField],
                "title"   => $title,
                "snippet" => $snippet,
                "link"    => $link
            );
        }
    }

    echo json_encode($results, JSON_UNESCAPED_UNICODE);
    exit;
}
?>

<script>
(function() {
    var box = document.getElementById('searchBox');
    var out = document.getElementById('searchResults');

    box.addEventListener('input', function() {
        var q = box.value.replace(/^\s+|\s+$/g, '');
        if (q.length < 2) {
            out.innerHTML = "";
            return;
        }

        fetch('?ajax=1&q=' + encodeURIComponent(q))
            .then(function(r) { return r.text(); })
            .then(function(text) {

                var data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    out.innerHTML = "<pre style='color:red;white-space:pre-wrap'>" + text + "</pre>";
                    return;
                }

                if (!data || !data.length) {
                    out.innerHTML = "<p>Ничего не найдено.</p>";
                    return;
                }

                var html = "";
                data.forEach(function(row) {

                    var linkHtml = "";
                    if (row.link && row.link !== "") {
                        linkHtml = '<a href="' + row.link + '" class="search-card-link">Открыть ?</a>';
                    }

                    html += ''
                        + '<div class="search-card">'
                        +   '<div class="search-card-title">' + row.title + '</div>'
                        +   '<div class="search-card-meta">' + row.section + ' · ID: ' + row.id + '</div>'
                        +   '<div class="search-card-text">' + row.snippet + '...</div>'
                        +   linkHtml
                        + '</div>';
                });

                out.innerHTML = html;
            })
            .catch(function() {
                out.innerHTML = "<p style='color:red;'>Ошибка загрузки.</p>";
            });
    });
})();
</script>

</div>

<?
include 'footer.php';
?>
</div>

</body>
</html>