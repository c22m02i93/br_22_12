<?php
/**
 * Вспомогательные функции для новостей (mysqli, cp1251 и старая разметка).
 */

function legacy_mysqli()
{
    static $link = null;

    if ($link instanceof mysqli) {
        return $link;
    }

    $link = @mysqli_connect('localhost', 'host1409556', '0f7cd928', 'host1409556_barysh');
    if (!$link) {
        trigger_error('Ошибка подключения к MySQL: ' . mysqli_connect_error(), E_USER_WARNING);
        return null;
    }

    // ВАЖНО: оставляем кодировку cp1251, как у тебя в проекте
    mysqli_set_charset($link, 'cp1251');
    return $link;
}

/**
 * Общее количество новостей (епархия + митрополия).
 */
function count_combined_news()
{
    $link = legacy_mysqli();
    if (!$link) {
        return 0;
    }

    $sql = "SELECT
                (SELECT COUNT(*) FROM news_eparhia) +
                (SELECT COUNT(*) FROM news_mitropolia) AS total";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        trigger_error('Ошибка запроса count_combined_news: ' . mysqli_error($link), E_USER_WARNING);
        return 0;
    }

    $row = mysqli_fetch_assoc($result);
    return isset($row['total']) ? (int)$row['total'] : 0;
}

/**
 * Получить ленту новостей (епархия + митрополия вперемешку по дате).
 */
function fetch_combined_news($page, $perPage)
{
    $link = legacy_mysqli();
    if (!$link) {
        return array();
    }

    $page = (int)$page;
    $perPage = (int)$perPage;

    if ($page < 1) {
        $page = 1;
    }
    if ($perPage < 1) {
        $perPage = 10;
    }

    $offset = ($page - 1) * $perPage;

    $sql = "
        (SELECT
            STR_TO_DATE(data, '%Y.%m.%d %H:%i') AS published_at,
            data AS legacy_key,
            tema,
            kratko,
            oblozka,
            video,
            views,
            '' AS external_link,
            'local' AS source,
            '' AS section_label
        FROM news_eparhia)
        UNION ALL
        (SELECT
            data AS published_at,
            DATE_FORMAT(data, '%Y.%m.%d %H:%i') AS legacy_key,
            tema,
            kratko,
            oblozka,
            NULL AS video,
            NULL AS views,
            link AS external_link,
            'mitropolia' AS source,
            section AS section_label
        FROM news_mitropolia)
        ORDER BY published_at DESC
        LIMIT ?, ?
    ";

    $stmt = mysqli_prepare($link, $sql);
    if (!$stmt) {
        trigger_error('Ошибка подготовки запроса fetch_combined_news: ' . mysqli_error($link), E_USER_WARNING);
        return array();
    }

    mysqli_stmt_bind_param($stmt, 'ii', $offset, $perPage);

    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($link);
        mysqli_stmt_close($stmt);
        trigger_error('Ошибка выполнения запроса fetch_combined_news: ' . $error, E_USER_WARNING);
        return array();
    }

    if (!mysqli_stmt_bind_result(
        $stmt,
        $publishedAt,
        $legacyKey,
        $tema,
        $kratko,
        $oblozka,
        $video,
        $views,
        $externalLink,
        $source,
        $sectionLabel
    )) {
        mysqli_stmt_close($stmt);
        trigger_error('Ошибка bind_result в fetch_combined_news: ' . mysqli_error($link), E_USER_WARNING);
        return array();
    }

    $items = array();
    while (mysqli_stmt_fetch($stmt)) {
        $items[] = array(
            'published_at'   => $publishedAt ? $publishedAt : '1970-01-01 00:00:00',
            'legacy_key'     => $legacyKey,
            'tema'           => $tema,
            'kratko'         => $kratko,
            'oblozka'        => $oblozka,
            'video'          => $video,
            'views'          => $views,
            'external_link'  => $externalLink,
            'source'         => $source,
            'section_label'  => $sectionLabel
        );
    }

    mysqli_stmt_close($stmt);
    return $items;
}

/**
 * Форматирование даты/времени по-русски (если нужно).
 * Месяца здесь специально оставлены пустыми строками — ты уже сам решишь,
 * какие подписи использовать, чтобы не трогать твои русские надписи.
 */
function format_russian_datetime($datetime)
{
    if (!$datetime) {
        return '';
    }

    $ts = strtotime($datetime);
    if (!$ts) {
        return $datetime;
    }

    $months = array(
        1  => '',
        2  => '',
        3  => '',
        4  => '',
        5  => '',
        6  => '',
        7  => '',
        8  => '',
        9  => '',
        10 => '',
        11 => '',
        12 => ''
    );

    $day   = (int)date('j', $ts);
    $month = (int)date('n', $ts);
    $year  = date('Y', $ts);
    $time  = date('H:i', $ts);

    $month_label = isset($months[$month]) ? $months[$month] : '';

    return '<span class="date">' . $day . ' ' . $month_label . ' ' . $year . ' . ' . $time . '</span>';
}

/**
 * Преобразование старой «разметки» (///курсив///, |||жирный|||, @R15-@ и т.д.)
 * в HTML.
 *
 * ВАЖНО: если текст уже содержит HTML (CKEditor), МЫ ЕГО НЕ ТРОГАЕМ,
 * чтобы редакторские новости не ломались.
 */
function transform_legacy_markup($text)
{
    $text = trim($text);
    if ($text === '') {
        return '';
    }

    // Если текст уже содержит HTML-теги (писали через редактор),
    // просто возвращаем как есть и НИЧЕГО не переписываем.
    if (preg_match('/<\s*(p|div|br|h[1-6]|ul|ol|li|img|a|span|strong|em|table|tr|td)[^>]*>/i', $text)) {
        return $text;
    }

    // Старый формат (без HTML) продолжаем обрабатывать как раньше
    $patterns = array(
        // ///курсив///
        '/(?:\/{3})(.+)(?:\/{3})/U',
        // |||жирный|||
        '/(?:\|{3})(.+)(?:\|{3})/U',
        // {{{http://...}}}-{{{текст}}}
        '/(?:\{{3})(http[s]*:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
        // {{{http://...}}}
        '/(?:\{{3})(http[s]*:\/\/[^\s\[<\(\)\|]+)(?:\}{3})/i',
        // {{{email@site.ru}}}
        '/(?:\{{3})([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3}))(?:\}{3})/',
        // перенос строки → новый параграф
        '/\n/',
        // @R15-Комментарий@
        '/@R(\d+)[-]?([^@]*)@/',
        // @L15-Комментарий@
        '/@L(\d+)[-]?([^@]*)@/',
        // [[[текст по центру]]]
        '/(?:\[{3})(([0-9]*[^\]{3}]*)*)(?:\]{3})/'
    );

    $replace = array(
        '<i>${1}</i>',
        '<b>${1}</b>',
        '<a href="${1}" target="_blank">${2}</a>',
        '<a href="${1}" target="_blank">${1}</a>',
        '<a href="mailto:${1}">${1}</a>',
        '</p><p>',
        '<span class="photos"><a href="FOTO/${1}.jpg" alt="${2}" title="${2}"><img class="news-photo float-end" src="FOTO_MINI/${1}.jpg" /></a></span>',
        '<span class="photos"><a href="FOTO/${1}.jpg" alt="${2}" title="${2}"><img class="news-photo float-start" src="FOTO_MINI/${1}.jpg" /></a></span>',
        '<div class="text-center fw-bold" style="width:100%; color:#743C00">${1}</div>'
    );

    $html = preg_replace($patterns, $replace, $text);
    return '<p>' . $html . '</p>';
}