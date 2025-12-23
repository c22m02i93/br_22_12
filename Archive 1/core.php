<?php
declare(strict_types=1);

require_once __DIR__ . '/function.php';
require_once __DIR__ . '/inc/db.php';

function fetchAll(string $sql, array $params = []): array
{
    $pdo = getPdo();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetchOne(string $sql, array $params = []): ?array
{
    $pdo = getPdo();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/* ================================================================
   1. ������� ���
================================================================ */
function getNewsDay(): ?array
{
    $item = fetchOne("SELECT * FROM host1409556_barysh.news_day LIMIT 1");

    if (!$item) {
        return null;
    }

    // ����
    $item['parsed_date'] = parseDate($item['data']);

    // ����� (������� �� ������ ��������)
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
   2. ��������� �������
================================================================ */
function getCalendar(): array
{
    $day   = date('d');
    $month = date('m');
    $year  = date('Y');

    $out = [
        'date' => parseDate("$year.$month.$day 00:00"),
        'arhierei' => [],
        'duhovenstvo' => [],
        'prestolnie' => [],
    ];

    $arhi = [];

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
            $arhi[] = ['title' => '���� ������'];
        } else {
            $age = $year - $e['year'];
            $titles = [
                'birthday'  => '���� ��������',
                'hirotonia' => '������������ ���������',
                'postrig'   => '���������� �������',
            ];
            $arhi[] = [
                'title' => $titles[$e['type']],
                'years' => $age,
                'years_text' => yearRus($age, '���','����','���'),
            ];
        }
    }

    $out['arhierei'] = $arhi;

    $calendarKey = "$month.$day";
    $angelKey    = "$day.$month";

    $klirik = fetchAll(
        "SELECT id, name, san, rozd, diak, presv, monah, angel
         FROM host1409556_barysh.klir
         WHERE status LIKE '�������'
           AND (
                 rozd LIKE :calendar
              OR diak LIKE :calendar
              OR presv LIKE :calendar
              OR monah LIKE :calendar
              OR angel LIKE :angel
           )
         ORDER BY name ASC",
        [
            ':calendar' => '%' . $calendarKey,
            ':angel' => '%' . $angelKey . '%',
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
            $yy = substr($k['rozd'], 0, 4);
            $age = $year - $yy;
            $item['type'] = 'birthday';
            $item['years'] = $age;
        }
        if (substr($k['diak'], 5, 5) === $calendarKey) {
            $yy = substr($k['diak'], 0, 4);
            $age = $year - $yy;
            $item['type'] = 'diak';
            $item['years'] = $age;
        }
        if (substr($k['presv'], 5, 5) === $calendarKey) {
            $yy = substr($k['presv'], 0, 4);
            $age = $year - $yy;
            $item['type'] = 'ierey';
            $item['years'] = $age;
        }
        if (substr($k['monah'], 5, 5) === $calendarKey) {
            $yy = substr($k['monah'], 0, 4);
            $age = $year - $yy;
            $item['type'] = 'monah';
            $item['years'] = $age;
        }
        if (strpos($k['angel'], $angelKey) !== false) {
            $item['type'] = 'angel';
        }

        $out['duhovenstvo'][] = $item;
    }

    $prestol = fetchAll(
        "SELECT id, name
         FROM host1409556_barysh.prihods
         WHERE angel LIKE :angel
         ORDER BY name ASC",
        [':angel' => '%' . $day . '.' . $month . '%']
    );

    $out['prestolnie'] = $prestol;

    return $out;
}


/* ================================================================
   3. �������� ��� (�� �������)
================================================================ */
function getHod(): array
{
    $today = date('Y.m.d');
    $year  = date('Y');

    if (!ctype_digit($year)) {
        return [];
    }

    $table = 'host1409556_barysh.krest_hod_' . $year;
    $sql = "SELECT * FROM {$table} WHERE data = :today ORDER BY pribyv ASC";

    return fetchAll($sql, [':today' => $today]);
}


/* ================================================================
   4. ���������� (��������������)
================================================================ */
function getRaspisanie(): array
{
    $today = date('Y.m.d');

    return fetchAll(
        "SELECT *
         FROM host1409556_barysh.raspisanie
         WHERE data >= :today
         ORDER BY data ASC, (text+0) ASC
         LIMIT 3",
        [':today' => $today]
    );
}


/* ================================================================
   5. ������
================================================================ */
function getAnons(): array
{
    global $new_day;

    $dtn_day = $new_day['data'] ?? null;

    if (!$dtn_day) {
        return [];
    }

    return fetchAll(
        "SELECT *
         FROM host1409556_barysh.anons
         WHERE data != :data
         ORDER BY data DESC
         LIMIT 2",
        [':data' => $dtn_day]
    );
}


/* ================================================================
   6. ��� �������
================================================================ */
function getNews3(): array
{
    return fetchAll(
        "SELECT *
         FROM host1409556_barysh.news_eparhia
         ORDER BY data DESC
         LIMIT 3"
    );
}


/* ================================================================
   7. ����������
================================================================ */
function getPublikacii(): array
{
    return fetchAll(
        "SELECT *
         FROM host1409556_barysh.publikacii
         ORDER BY data DESC
         LIMIT 3"
    );
}


/* ================================================================
   8. ����� �����������
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
   9. ����� (��� � ������ ����)
================================================================ */
function getVideo(): array
{
    return []; // ����� �������, ���� �����
}


/* ================================================================
   ��������������� �������
================================================================ */
function parseDate(string $dt): array
{
    $y = substr($dt, 0, 4);
    $m = substr($dt, 5, 2);
    $d = substr($dt, 8, 2);
    $t = substr($dt, 11, 5);

    $months = [
        '01' => '������', '02' => '�������', '03' => '�����', '04' => '������', '05' => '���',
        '06' => '����', '07' => '����', '08' => '�������', '09' => '��������', '10' => '�������',
        '11' => '������', '12' => '�������',
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
