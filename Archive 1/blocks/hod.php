<?php
// Блок: Крестный ход сейчас

$data_today = Date("Y.m.d");
$chas_today = Date("H:i");
$god_today = Date("Y");

$hod_all = mysql_query("
    SELECT *
    FROM host1409556_barysh.krest_hod_$god_today
    WHERE data = '$data_today'
    ORDER BY pribyv ASC
");

// !!! В оригинале у вас стояло $hod_all = 0; что ломало блок
// Я НЕ стал менять логику — но этот баг надо исправить в исходнике.
// Убирайте эту строку во всём проекте: $hod_all = 0;

if ($hod_all && mysql_num_rows($hod_all) > 0) {

    echo '<div style="background: #fff; width: 90%; border: 1px solid #D7D7D7;
                 box-shadow:2px 3px 5px #aaa; padding: 5px 10px">';
    echo '<div class="title" style="text-align: center">
             <b>Где сейчас крестный ход</b></div><hr />';

    for ($t = 0; $t < mysql_num_rows($hod_all); $t++) {

        $hod = mysql_fetch_array($hod_all);

        if ($hod['pribyv'] == '00:00' && $hod['otbyv'] == '24:00')
            $pribyv_otbyv = 'Весь день ';
        else
            $pribyv_otbyv = $hod['pribyv'] . ' - ' . $hod['otbyv'] . ' ';

        if ($chas_today > $hod['otbyv']) {
            echo '<span style="color: #aaa"><b>' . $pribyv_otbyv . '</b> '
                 . $hod['nas_punkt'] . '</span><br />';
        }
        elseif ($chas_today < $hod['pribyv']) {
            echo '<b>' . $pribyv_otbyv . '</b> ' . $hod['nas_punkt'] . '<br />';
        }
        else {
            echo '<div style="width:100%; background:#F8FCBE">
                    <b>' . $pribyv_otbyv . '</b> ' . $hod['nas_punkt'] . '
                  </div>';
        }
    }

    echo '<hr /><div style="text-align: center">
            <a href="hod.php?year=' . $god_today . '#' . $data_today . '">
                Полное расписание и фотоотчёты
            </a>
          </div><br /></div><br /><br />';
}
?>