<?php

declare(strict_types=1);

/**
 * Единый логический модуль сайта
 * ВАЖНО: НИКАКОГО HTML НЕТ — только логика, данные, разбор полей.
 * Вёрстка делается в index.php
 */

require_once __DIR__ . '/function.php';
require_once __DIR__ . '/inc/env.php';
require_once __DIR__ . '/inc/db.php';

$pdo = getPdoConnection();

// Универсальный помощник
function fetchAll(string $query, array $params = []): array
{
    global $pdo;

    $statement = $pdo->prepare($query);
    $statement->execute($params);

    return $statement->fetchAll();
}

/* ================================================================
   1. НОВОСТЬ ДНЯ
================================================================ */
function getNewsDay(): ?array
{
    $rows = fetchAll('SELECT * FROM host1409556_barysh.news_day LIMIT 1');
    $item = $rows[0] ?? null;

    if (!$item) {
        return null;
    }

    // дата
    $item['parsed_date'] = parseDate($item['data']);

    // текст (очистка от старой разметки)
    $patterns = [
        '/(?:\{{3})(http:\/\/[^\s\[<\(\)\|]+)(?:\}{3})-(?:\{{3})([^}]+)(?:\}{3})/i',
        '/\n/', '/(?:\/{3})/','/(?:\|{3})/','/@[^@]+@/',
        '/(?:\{{3})/','/(?:\}{3})/','/\[/','/\]/'
    ];
    $replace = ['${2}', '</p><p>', '', '', '', '', '', '', ''];

    $item['text_clean'] = preg_replace($patterns, $replace, $item['text']);

    return $item;
}


/* ================================================================
   2. КАЛЕНДАРЬ ЕПАРХИИ
================================================================ */
function getCalendar(): array
{
    $day   = Date('d');
    $month = Date('m');
    $year  = Date('Y');

    $out = [
        'date' => parseDate("$year.$month.$day 00:00"),
        'arhierei' => [],
        'duhovenstvo' => [],
        'prestolnie' => [],
    ];

    // --- Архиерей
    $events = [
        '12.06' => ['type' => 'birthday',  'year' => 1963],
        '10.28' => ['type' => 'hirotonia', 'year' => 2012],
        '11.30' => ['type' => 'postrig',   'year' => 1996],
        '12.02' => ['type' => 'angel'],
    ];

    $key = "$month.$day";
    if (isset($events[$key])) {
        $e = $events[$key];
        if ($e['type'] === 'angel') {
            $out['arhierei'][] = ['title' => 'День ангела'];
        } else {
            $age = $year - $e['year'];
            $titles = [
                'birthday'  => 'День рождения',
                'hirotonia' => 'Архиерейская хиротония',
                'postrig'   => 'Монашеский постриг',
            ];
            $out['arhierei'][] = [
                'title' => $titles[$e['type']],
                'years' => $age,
                'years_text' => yearRus($age, 'год', 'года', 'лет'),
            ];
        }
    }

    // --- духовенство
    $calendarKey = "$month.$day";
    $angelKey    = "$day.$month";

    $klirik = fetchAll(
        'SELECT id, name, san, rozd, diak, presv, monah, angel
         FROM host1409556_barysh.klir
         WHERE status LIKE :status
           AND (
                 rozd LIKE :calendarKey1
              OR diak LIKE :calendarKey2
              OR presv LIKE :calendarKey3
              OR monah LIKE :calendarKey4
              OR angel LIKE :angelKey
           )
         ORDER BY name ASC',
        [
            'status' => 'штатный',
            'calendarKey1' => "%$calendarKey",
            'calendarKey2' => "%$calendarKey",
            'calendarKey3' => "%$calendarKey",
            'calendarKey4' => "%$calendarKey",
            'angelKey' => "%$angelKey%",
        ]
    );

    foreach ($klirik as $k) {
        $item = [
            'id' => $k['id'],
            'name' => $k['name'],
            'san' => $k['san'],
            'type' => '',
            'years' => '',
        ];

        if (substr($k['rozd'], 5, 5) === $calendarKey) {
            $yy = (int) substr($k['rozd'], 0, 4);
            $age = $year - $yy;
            $item['type'] = 'birthday';
            $item['years'] = $age;
        }
        if (substr($k['diak'], 5, 5) === $calendarKey) {
            $yy = (int) substr($k['diak'], 0, 4);
            $age = $year - $yy;
            $item['type'] = 'diak';
            $item['years'] = $age;
        }
        if (substr($k['presv'], 5, 5) === $calendarKey) {
            $yy = (int) substr($k['presv'], 0, 4);
            $age = $year - $yy;
            $item['type'] = 'ierey';
            $item['years'] = $age;
        }
        if (substr($k['monah'], 5, 5) === $calendarKey) {
            $yy = (int) substr($k['monah'], 0, 4);
            $age = $year - $yy;
            $item['type'] = 'monah';
            $item['years'] = $age;
        }
        if (strpos($k['angel'], $angelKey) !== false) {
            $item['type'] = 'angel';
        }

        $out['duhovenstvo'][] = $item;
    }

    // --- престольные праздники
    $prestol = fetchAll(
        'SELECT id, name
         FROM host1409556_barysh.prihods
         WHERE angel LIKE :angel
         ORDER BY name ASC',
        ['angel' => "%$day.$month%"]
    );

    $out['prestolnie'] = $prestol;

    return $out;
}


/* ================================================================
   3. КРЕСТНЫЙ ХОД (на сегодня)
================================================================ */
function getHod(): array
{
    $today = Date('Y.m.d');
    $year  = Date('Y');

    if (!preg_match('/^\d{4}$/', $year)) {
        return [];
    }

    $table = sprintf('host1409556_barysh.krest_hod_%s', $year);

    return fetchAll(
        "SELECT * FROM {$table} WHERE data = :today ORDER BY pribyv ASC",
        ['today' => $today]
    );
}


/* ================================================================
   4. РАСПИСАНИЕ (архипастырское)
================================================================ */
function getRaspisanie(): array
{
    $today = Date('Y.m.d');

    return fetchAll(
        'SELECT *
         FROM host1409556_barysh.raspisanie
         WHERE data >= :today
         ORDER BY data ASC, (text+0) ASC
         LIMIT 3',
        ['today' => $today]
    );
}


/* ================================================================
   5. АНОНСЫ
================================================================ */
function getAnons(): array
{
    global $new_day;

    $dtn_day = $new_day['data'] ?? '';

    return fetchAll(
        'SELECT *
         FROM host1409556_barysh.anons
         WHERE data != :data
         ORDER BY data DESC
         LIMIT 2',
        ['data' => $dtn_day]
    );
}


/* ================================================================
   6. ТРИ НОВОСТИ
================================================================ */
function getNews3(): array
{
    return fetchAll(
        'SELECT *
         FROM host1409556_barysh.news_eparhia
         ORDER BY data DESC
         LIMIT 3'
    );
}


/* ================================================================
   7. ПУБЛИКАЦИИ
================================================================ */
function getPublikacii(): array
{
    return fetchAll(
        'SELECT *
         FROM host1409556_barysh.publikacii
         ORDER BY data DESC
         LIMIT 3'
    );
}


/* ================================================================
   8. СЛОВО АРХИПАСТЫРЯ
================================================================ */
function getSlovoPadre(): array
{
    return fetchAll(
        "SELECT tema, kratko, data, oblozka, link
         FROM host1409556_barysh.news_mitropolia
         WHERE section = 'slovo'
           AND data >= '2025-11-01 00:00:00'
         ORDER BY data DESC
         LIMIT 2"
    );
}


/* ================================================================
   9. ВИДЕО (как в старом коде)
================================================================ */
function getVideo(): array
{
    return []; // позже добавлю, если нужно
}


/* ================================================================
   ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
================================================================ */
function parseDate(string $dt): array
{
    $y = substr($dt, 0, 4);
    $m = substr($dt, 5, 2);
    $d = substr($dt, 8, 2);
    $t = substr($dt, 11, 5);

    $months = [
        '01' => 'января','02' => 'февраля','03' => 'марта','04' => 'апреля','05' => 'мая',
        '06' => 'июня','07' => 'июля','08' => 'августа','09' => 'сентября','10' => 'октября',
        '11' => 'ноября','12' => 'декабря'
    ];

    if ($d[0] === '0') {
        $d = substr($d, 1);
    }

    return [
        'raw' => $dt,
        'day' => $d,
        'month' => $m,
        'month_text' => $months[$m] ?? $m,
        'year' => $y,
        'time' => $t,
    ];
}
