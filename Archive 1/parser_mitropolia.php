<?php
/**
 * Парсер mitropolia-simbirsk.ru
 * Оптимизированная версия с минимальной нагрузкой:
 *  - не скачивает полные страницы статей
 *  - не грузит удалённый сайт
 *  - изображение берётся из RSS или пропускается
 *  - ссылка ведёт напрямую на оригинал
 */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 0);

header('Content-Type: text/plain; charset=windows-1251');

/* ==== Ограничение общего времени выполнения скрипта ==== */
$script_start = microtime(true);
$max_runtime  = 15; // максимум 15 секунд

function check_runtime_limit($start, $max) {
    if (microtime(true) - $start > $max) {
        echo "[STOP] Max runtime {$max}s reached, aborting.\n";
        return true;
    }
    return false;
}

/* ==== БД ==== */
$db_host = "localhost";
$db_user = "host1409556";
$db_pass = "0f7cd928";
$db_name = "host1409556_barysh";
$table   = "news_mitropolia";

/* ==== Файлы ==== */
$upload_dir = __DIR__ . "/uploads/mitropolia";
$upload_url = "/uploads/mitropolia";

/* ==== RSS-источники ==== */
$feeds = array(
    "barysh_tag" => "https://mitropolia-simbirsk.ru/tag/baryshskaya-eparhiya/feed/",
    "arhipastry" => "https://mitropolia-simbirsk.ru/category/mitropoliya/arhipastyrskoe-sluzhenie/feed/",
    "slovo"      => "https://mitropolia-simbirsk.ru/category/mitropolit/slovo-arhipastyrya/feed/",
);

/* ==== Функции ==== */

function http_get($url){
    if(function_exists('curl_init')){
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => "BaryshParser/1.2",
            CURLOPT_FAILONERROR    => true,
        ));
        $data = curl_exec($ch);
        curl_close($ch);
        return $data ? $data : '';
    }
    return @file_get_contents($url);
}

/**
 * Извлечь og:image из XML элемента RSS
 */
function extract_image_from_rss_item($item){
    // Ищем media:content
    $namespaces = $item->getNamespaces(true);
    if(isset($namespaces['media'])){
        $media = $item->children($namespaces['media']);
        if($media->content){
            foreach($media->content as $content){
                if((string)$content['url']) return (string)$content['url'];
            }
        }
    }
    
    // Ищем description с тегом img
    $desc = (string)$item->description;
    if($desc && preg_match('/<img[^>]+src=["\']([^"\']+)/i', $desc, $m)){
        return $m[1];
    }
    
    return '';
}

function ensure_dir($p){
    if(!is_dir($p)) @mkdir($p, 0755, true);
}

function absolute_url($src){
    $src = trim(html_entity_decode($src, ENT_QUOTES, 'UTF-8'));
    if(strpos($src, '//') === 0) return 'https:' . $src;
    if(strpos($src, 'http') === 0) return $src;
    if($src[0] == '/') return 'https://mitropolia-simbirsk.ru' . $src;
    return 'https://mitropolia-simbirsk.ru/' . $src;
}

/**
 * Сохранить изображение локально
 */
function save_image_local($img_url, $upload_dir, $prefix='img'){
    $img_url = absolute_url($img_url);
    if(!$img_url) return '';

    $path = parse_url($img_url, PHP_URL_PATH);
    $ext  = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
    $ext  = strtolower(preg_replace('/[^a-z0-9]/i', '', $ext));
    if(!$ext) $ext = 'jpg';

    $name = $prefix . '-' . date('Ymd-His') . '-' . substr(md5($img_url), 0, 8) . '.' . $ext;
    ensure_dir($upload_dir);

    $bin = http_get($img_url);
    if(!$bin || strlen($bin) < 512) return '';

    @file_put_contents(rtrim($upload_dir, '/') . '/' . $name, $bin);
    return $name;
}

function str_truncate($text, $limit){
    $text = trim($text);
    if(function_exists('mb_strlen')){
        if(mb_strlen($text, 'UTF-8') <= $limit) return $text;
        return mb_substr($text, 0, $limit, 'UTF-8') . '…';
    }
    if(strlen($text) <= $limit) return $text;
    return substr($text, 0, $limit) . '…';
}

/* ==== БД подключение ==== */
mysql_connect($db_host, $db_user, $db_pass) or die("DB connect error");
mysql_select_db($db_name) or die("DB select error");
mysql_query("SET NAMES 'utf8'");

/* ==== Таблица ==== */
mysql_query("CREATE TABLE IF NOT EXISTS `$table` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tema` VARCHAR(255) NOT NULL,
  `kratko` TEXT,
  `data` DATETIME NOT NULL,
  `oblozka` VARCHAR(500) DEFAULT NULL,
  `link` VARCHAR(500) NOT NULL,
  `section` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_link` (`link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

/* ==== Кэш ==== */
$cache_dir = __DIR__ . '/cache_mitropolia';
$cache_ttl = 600;
ensure_dir($cache_dir);

$total_new = 0;
$total_upd = 0;

$cutoff_ts = strtotime("2025-11-01 00:00:00");

/* ======= Основной цикл ======= */
foreach($feeds as $section => $rss_url){

    if(check_runtime_limit($script_start, $max_runtime)){
        break;
    }

    // Для слова удаляем кэш каждый раз
    if($section === 'slovo'){
        $cf = $cache_dir . '/' . md5($rss_url) . '.xml';
        if(file_exists($cf)) @unlink($cf);
    }

    $cache_file = $cache_dir . '/' . md5($rss_url) . '.xml';
    if(file_exists($cache_file) && time() - filemtime($cache_file) < $cache_ttl){
        $xml_str = @file_get_contents($cache_file);
    } else {
        $xml_str = http_get($rss_url);
        if($xml_str) @file_put_contents($cache_file, $xml_str);
    }
    
    if(!$xml_str){
        echo "[WARN] no RSS: $rss_url\n";
        continue;
    }

    $rss = @simplexml_load_string($xml_str);
    if(!$rss){
        echo "[WARN] bad XML: $rss_url\n";
        continue;
    }

    $items = array();

    if($rss->channel && $rss->channel->item){
        foreach($rss->channel->item as $it) $items[] = $it;
    } elseif($rss->entry){
        foreach($rss->entry as $it) $items[] = $it;
    }

    foreach($items as $item){

        if(check_runtime_limit($script_start, $max_runtime)){
            break 2;
        }

        $title = trim((string)$item->title);

        $link = trim((string)$item->link);
        if(!$link && $item->link && $item->link['href'])
            $link = trim((string)$item->link['href']);

        if(!$link || !$title){
            continue;
        }

        $pub = (string)$item->pubDate;
        if(!$pub && $item->updated) $pub = (string)$item->updated;
        if(!$pub && $item->date)    $pub = (string)$item->date;

        $ts = $pub ? strtotime($pub) : time();
        if($ts < $cutoff_ts){
            break;
        }

        $date = date("Y-m-d H:i:s", $ts);

        $e_link = mysql_real_escape_string($link);
        $q = mysql_query("SELECT id, oblozka FROM `$table` WHERE link='$e_link' LIMIT 1");
        $row = $q ? mysql_fetch_assoc($q) : null;

        // Извлечение описания только из RSS, без скачивания страницы
        $plain = trim(strip_tags($item->description ? $item->description : $item->content));
        $plain = preg_replace('/\s+/u', ' ', $plain);
        $plain = str_truncate($plain, 600);

        $e_date = mysql_real_escape_string($date);
        $e_tema = mysql_real_escape_string($title);
        $e_krat = mysql_real_escape_string($plain);
        $e_sec  = mysql_real_escape_string($section);

        $oblozka_local = '';

        // Пытаемся получить изображение только из RSS, БЕЗ скачивания страницы
        if(!$row || empty($row['oblozka'])){
            $img = extract_image_from_rss_item($item);
            if($img){
                $fname = save_image_local($img, $upload_dir, $section);
                if($fname) $oblozka_local = $upload_url . '/' . $fname;
            }
        }

        if($row){
            // Обновление только если есть новые данные
            $set = array(
                "`data`='$e_date'",
                "`tema`='$e_tema'",
                "`section`='$e_sec'"
            );

            if($oblozka_local && empty($row['oblozka']))
                $set[] = "`oblozka`='" . mysql_real_escape_string($oblozka_local) . "'";

            if(count($set) > 0){
                $sql = "UPDATE `$table` SET " . implode(',', $set) . " WHERE id=" . (int)$row['id'] . " LIMIT 1";
                mysql_query($sql);
                if(mysql_affected_rows() > 0){
                    $total_upd++;
                    echo "[UPD] $title ($section)\n";
                }
            }

        } else {
            $e_img = mysql_real_escape_string($oblozka_local);
            $sql = "INSERT INTO `$table`
                    (`tema`,`kratko`,`data`,`oblozka`,`link`,`section`)
                    VALUES ('$e_tema','$e_krat','$e_date','$e_img','$e_link','$e_sec')";
            if(mysql_query($sql)){
                $total_new++;
                echo "[NEW] $title ($section)\n";
            } else {
                echo "[ERR] " . mysql_error() . "\n";
            }
        }
    }
}

echo "Done. New: $total_new, Updated: $total_upd\n";
?>