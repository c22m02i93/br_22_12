<?php
// Блок: Архиерейское служение (расписание)

$data_today = Date("Y.m.d");

// Находим ближайшее служение (чтобы решить — показывать заголовок или нет)
$news_all_r = mysql_query("
    SELECT *
    FROM host1409556_barysh.raspisanie
    WHERE data BETWEEN '$data_today' AND '9999.12.31'
    ORDER BY data ASC
    LIMIT 1
");

$news_r = mysql_fetch_array($news_all_r);

// Если текст есть — выводим заголовок
if (!empty($news_r['text'])) {
    echo '<h2 style="border-bottom: 5px solid #E6E0C6;">
            <a style="color:#7A6D42; border-bottom:5px solid #E6E0C6;" href="raspisanie.php">
                Архиерейское служение
            </a>
          </h2><br />';
}

// Теперь берём 3 ближайших служения
$news_all = mysql_query("
    SELECT *
    FROM host1409556_barysh.raspisanie
    WHERE data BETWEEN '$data_today' AND '9999.12.31'
    ORDER BY data ASC, (text+0) ASC
    LIMIT 3
");

for ($t=0; $t < mysql_num_rows($news_all); $t++)
{
    $news = mysql_fetch_array($news_all);

    // Формирование текста, жирное выделение времени
    $patterns = [
        '/\n/',
        '/(\d{1,2}:\d{2})/'
    ];
    $replace = [
        '</p><p>',
        '<b>${1}</b>'
    ];
    $text = preg_replace($patterns, $replace, $news['text']);

    // Вывод блока
    echo '<div class="box_arhi">
            <h3>'.$news['data_text'].' - '.$news['nedel'].'</h3>
            <p>'.$text.'</p><br />
          </div><br />';
}

// Разрывы как в оригинале
if (!empty($news_r['text'])) echo '<br />';
?>