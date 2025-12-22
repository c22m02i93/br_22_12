<?php
/**
 * Единый логический модуль сайта
 * ВАЖНО: НИКАКОГО HTML НЕТ — только логика, данные, разбор полей.
 * Вёрстка делается в index.php
 */

require_once "function.php";

// Подключение БД (как в старом init)
$mysqlConnection = mysql_connect("localhost", "host1409556", "0f7cd928");
mysql_query("SET NAMES 'cp1251'");

// Универсальный помощник
function fetchAll($query) {
    $res = mysql_query($query);
    $out = [];
    while ($row = mysql_fetch_assoc($res)) {
        $out[] = $row;
    }
    return $out;
}

/* ================================================================
   1. НОВОСТЬ ДНЯ
================================================================ */
function getNewsDay() {

    $res = mysql_query("SELECT * FROM host1409556_barysh.news_day");
    $item = mysql_fetch_assoc($res);

    if (!$item) return null;

    // дата
    $item['parsed_date'] = parseDate($item['data']);

    // текст (очистка от старой разметки)
    $patterns = [
        '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
        '/\n/', '/(?:\/{3})/','/(?:\|{3})/','/@[^@]+@/',
        '/(?:\{{3})/','/(?:\}{3})/','/\[/', '/\]/'
    ];
    $replace = ['${2}', '</p><p>', '', '', '', '', '', '', ''];

    $item['text_clean'] = preg_replace($patterns, $replace, $item['text']);

    return $item;
}


/* ================================================================
   2. КАЛЕНДАРЬ ЕПАРХИИ
================================================================ */
function getCalendar() {

    $day   = Date("d");
    $month = Date("m");
    $year  = Date("Y");

    $out = [
        "date" => parseDate("$year.$month.$day 00:00"),
        "arhierei" => [],
        "duhovenstvo" => [],
        "prestolnie" => []
    ];

    // --- Архиерей
    $arhi = [];

    $events = [
        "12.06" => ["type" => "birthday",  "year" => 1963],
        "10.28" => ["type" => "hirotonia", "year" => 2012],
        "11.30" => ["type" => "postrig",   "year" => 1996],
        "12.02" => ["type" => "angel"]
    ];

    $key = "$month.$day";
    if (isset($events[$key])) {
        $e = $events[$key];
        if ($e["type"] == "angel") {
            $arhi[] = ["title" => "День ангела"];
        } else {
            $age = $year - $e["year"];
            $titles = [
                "birthday"  => "День рождения",
                "hirotonia" => "Архиерейская хиротония",
                "postrig"   => "Монашеский постриг",
            ];
            $arhi[] = [
                "title" => $titles[$e["type"]],
                "years" => $age,
                "years_text" => yearRus($age, "год","года","лет")
            ];
        }
    }

    $out["arhierei"] = $arhi;

    // --- духовенство
    $calendarKey = "$month.$day";
    $angelKey    = "$day.$month";

    $klirik = fetchAll("
        SELECT id, name, san, rozd, diak, presv, monah, angel
        FROM host1409556_barysh.klir
        WHERE status LIKE 'штатный'
          AND (
                rozd LIKE '%$calendarKey'
             OR diak LIKE '%$calendarKey'
             OR presv LIKE '%$calendarKey'
             OR monah LIKE '%$calendarKey'
             OR angel LIKE '%$angelKey%'
          )
        ORDER BY name ASC
    ");

    foreach ($klirik as $k) {
        $item = [
            "id" => $k["id"],
            "name" => $k["name"],
            "san" => $k["san"],
            "type" => "",
            "years" => ""
        ];

        if (substr($k["rozd"],5,5) === $calendarKey) {
            $yy = substr($k["rozd"],0,4);
            $age = $year - $yy;
            $item["type"] = "birthday";
            $item["years"] = $age;
        }
        if (substr($k["diak"],5,5) === $calendarKey) {
            $yy = substr($k["diak"],0,4);
            $age = $year - $yy;
            $item["type"] = "diak";
            $item["years"] = $age;
        }
        if (substr($k["presv"],5,5) === $calendarKey) {
            $yy = substr($k["presv"],0,4);
            $age = $year - $yy;
            $item["type"] = "ierey";
            $item["years"] = $age;
        }
        if (substr($k["monah"],5,5) === $calendarKey) {
            $yy = substr($k["monah"],0,4);
            $age = $year - $yy;
            $item["type"] = "monah";
            $item["years"] = $age;
        }
        if (strpos($k["angel"], $angelKey) !== false) {
            $item["type"] = "angel";
        }

        $out["duhovenstvo"][] = $item;
    }

    // --- престольные праздники
    $prestol = fetchAll("
        SELECT id, name
        FROM host1409556_barysh.prihods
        WHERE angel LIKE '%$day.$month%'
        ORDER BY name ASC
    ");

    $out["prestolnie"] = $prestol;

    return $out;
}


/* ================================================================
   3. КРЕСТНЫЙ ХОД (на сегодня)
================================================================ */
function getHod() {

    $today = Date("Y.m.d");
    $year  = Date("Y");

    $res = fetchAll("
        SELECT *
        FROM host1409556_barysh.krest_hod_$year
        WHERE data = '$today'
        ORDER BY pribyv ASC
    ");

    return $res;
}


/* ================================================================
   4. РАСПИСАНИЕ (архипастырское)
================================================================ */
function getRaspisanie() {
    $today = Date("Y.m.d");

    return fetchAll("
        SELECT *
        FROM host1409556_barysh.raspisanie
        WHERE data >= '$today'
        ORDER BY data ASC, (text+0) ASC
        LIMIT 3
    ");
}


/* ================================================================
   5. АНОНСЫ
================================================================ */
function getAnons() {
    global $new_day;

    $dtn_day = $new_day["data"];

    return fetchAll("
        SELECT *
        FROM host1409556_barysh.anons
        WHERE data != '$dtn_day'
        ORDER BY data DESC
        LIMIT 2
    ");
}


/* ================================================================
   6. ТРИ НОВОСТИ
================================================================ */
function getNews3() {
    return fetchAll("
        SELECT *
        FROM host1409556_barysh.news_eparhia
        ORDER BY data DESC
        LIMIT 3
    ");
}


/* ================================================================
   7. ПУБЛИКАЦИИ
================================================================ */
function getPublikacii() {
    return fetchAll("
        SELECT *
        FROM host1409556_barysh.publikacii
        ORDER BY data DESC
        LIMIT 3
    ");
}


/* ================================================================
   8. СЛОВО АРХИПАСТЫРЯ
================================================================ */
function getSlovoPadre() {
    return fetchAll("
        SELECT tema, kratko, data, oblozka, link
        FROM host1409556_barysh.news_mitropolia
        WHERE section = 'slovo'
          AND data >= '2025-11-01 00:00:00'
        ORDER BY data DESC
        LIMIT 2
    ");
}


/* ================================================================
   9. ВИДЕО (как в старом коде)
================================================================ */
function getVideo() {
    return []; // позже добавлю, если нужно
}


/* ================================================================
   ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
================================================================ */
function parseDate($dt) {
    $y = substr($dt,0,4);
    $m = substr($dt,5,2);
    $d = substr($dt,8,2);
    $t = substr($dt,11,5);

    $months = [
        "01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"мая",
        "06"=>"июня","07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября",
        "11"=>"ноября","12"=>"декабря"
    ];

    if ($d[0] == "0") $d = substr($d,1);

    return [
        "raw" => $dt,
        "day" => $d,
        "month" => $m,
        "month_text" => $months[$m],
        "year" => $y,
        "time" => $t
    ];
}

?>