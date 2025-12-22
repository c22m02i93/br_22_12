<?php
// ==== Календарь епархии (обновлённый стиль) ====

$day_today   = date("d");
$month_today = date("m");
$year_today  = date("Y");

// Месяцы
$months = [
    '01'=>'января','02'=>'февраля','03'=>'марта','04'=>'апреля',
    '05'=>'мая','06'=>'июня','07'=>'июля','08'=>'августа',
    '09'=>'сентября','10'=>'октября','11'=>'ноября','12'=>'декабря'
];

$day_clean = ltrim($day_today, '0');
$month_text = $months[$month_today];


// ======== АРХИЕРЕЙ ========
$events_arhi = [];

function add_event(&$arr, $name) {
    $arr[] = "<div class='cal-item'>$name</div>";
}

if ($month_today.'.'.$day_today == '12.06') {
    $years = $year_today - 1963;
    add_event($events_arhi, "День рождения — <b>$years ".yearRus($years,'год','года','лет')."</b>");
}

if ($month_today.'.'.$day_today == '10.28') {
    $years = $year_today - 2012;
    add_event($events_arhi, "Архиерейская хиротония — <b>$years ".yearRus($years,'год','года','лет')."</b>");
}

if ($month_today.'.'.$day_today == '11.30') {
    $years = $year_today - 1996;
    add_event($events_arhi, "Монашеский постриг — <b>$years ".yearRus($years,'год','года','лет')."</b>");
}

if ($month_today.'.'.$day_today == '12.02') {
    add_event($events_arhi, "День ангела");
}


// ======== ДУХОВЕНСТВО ========
$calDateKey = "$month_today.$day_today";
$angelKey   = "$day_today.$month_today";

$events_priest = [];

$q = mysql_query("
    SELECT id, name, san, rozd, diak, presv, monah, angel
    FROM host1409556_barysh.klir
    WHERE status='штатный'
");

while ($r = mysql_fetch_assoc($q)) {

    $fio = $r['san']." ".$r['name'];
    $link = "<a href='/klirik.php?id={$r['id']}' class='cal-link' target='_blank'>$fio</a>";

    if (!empty($r['rozd']) && substr($r['rozd'],5,5) === $calDateKey) {
        $years = $year_today - substr($r['rozd'],0,4);
        add_event($events_priest, "$link — день рождения (<b>$years</b>)");
    }
    if (!empty($r['diak']) && substr($r['diak'],5,5) === $calDateKey) {
        $years = $year_today - substr($r['diak'],0,4);
        add_event($events_priest, "$link — диаконская хиротония (<b>$years</b>)");
    }
    if (!empty($r['presv']) && substr($r['presv'],5,5) === $calDateKey) {
        $years = $year_today - substr($r['presv'],0,4);
        add_event($events_priest, "$link — иерейская хиротония (<b>$years</b>)");
    }
    if (!empty($r['monah']) && substr($r['monah'],5,5) === $calDateKey) {
        $years = $year_today - substr($r['monah'],0,4);
        add_event($events_priest, "$link — монашеский постриг (<b>$years</b>)");
    }
    if (!empty($r['angel']) && strpos($r['angel'], $angelKey) !== false) {
        add_event($events_priest, "$link — день ангела");
    }
}


// ======== ПРЕСТОЛЬНЫЕ ПРАЗДНИКИ ========
$events_prest = [];

$q2 = mysql_query("
    SELECT id, name
    FROM host1409556_barysh.prihods
    WHERE angel LIKE '%$day_today.$month_today%'
");

while ($p = mysql_fetch_assoc($q2)) {
    add_event(
        $events_prest,
        "<a class='cal-link' href='/prihod.php?id={$p['id']}' target='_blank'>{$p['name']}</a>"
    );
}


// ======== ВЫВОД ========
?>

<div class="calendar-card">

    <div class="cal-title">Календарь епархии</div>

    <div class="cal-date"><?= $day_clean ?> <?= $month_text ?></div>

    <?php if ($events_arhi): ?>
        <div class="cal-block">
            <div class="cal-block-title">Архиерей</div>
            <?= implode("", $events_arhi) ?>
        </div>
    <?php endif; ?>

    <?php if ($events_priest): ?>
        <div class="cal-block">
            <div class="cal-block-title">Духовенство</div>
            <?= implode("", $events_priest) ?>
        </div>
    <?php endif; ?>

    <?php if ($events_prest): ?>
        <div class="cal-block">
            <div class="cal-block-title">Престольный праздник</div>
            <?= implode("", $events_prest) ?>
        </div>
    <?php endif; ?>

    <?php if (!$events_arhi && !$events_priest && !$events_prest): ?>
        <div class="cal-empty">Сегодня событий нет</div>
    <?php endif; ?>

    <div class="cal-footer">
        <a href="/kalendar.php?month=<?= $month_today ?>">Весь календарь</a>
    </div>
</div>


<style>
.calendar-card {
    background: #fff;
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.12);
    border: 1px solid #e5e5e5;
    margin-bottom: 25px;
}

.cal-title {
    font-size: 20px;
    text-align: center;
    margin-bottom: 10px;
    font-weight: bold;
}

.cal-date {
    color: #d40000;
    font-size: 22px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 15px;
}

.cal-block {
    margin-bottom: 15px;
}

.cal-block-title {
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 5px;
}

.cal-item {
    padding-left: 5px;
    margin-bottom: 4px;
    color: #444;
}

.cal-link {
    color: #0044cc;
    text-decoration: none;
}
.cal-link:hover {
    text-decoration: underline;
}

.cal-empty {
    text-align:center;
    color:#777;
    margin:10px 0;
}

.cal-footer {
    margin-top: 15px;
    text-align: center;
}

.cal-footer a {
    color: #666;
    text-decoration: none;
}
.cal-footer a:hover {
    text-decoration: underline;
}
</style>