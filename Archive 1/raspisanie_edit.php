<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

/* --------- АВТОРИЗАЦИЯ --------- */
$auth = isset($_SESSION['auth']) ? $_SESSION['auth'] : 0;
$name_user = isset($_SESSION['name_user']) ? $_SESSION['name_user'] : '';
if ($auth != 1) { header("Location: my_auth.php"); exit; }

/* --------- БАЗА ДАННЫХ --------- */
include 'db.php';
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    die("Ошибка: соединение \$mysqli не создано в db.php");
}

/* --------- ВХОДНЫЕ ДАННЫЕ --------- */
$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

/* --------- СОХРАНЕНИЕ --------- */
if (!empty($_POST['submit'])) {

    $sluzba = isset($_POST['sluzba']) ? trim($_POST['sluzba']) : '';
    $hram   = isset($_POST['hram'])   ? trim($_POST['hram'])   : '';
    $data_d = isset($_POST['data'])   ? trim($_POST['data'])   : '';
    $month  = isset($_POST['month'])  ? trim($_POST['month'])  : '';
    $year   = isset($_POST['year'])   ? trim($_POST['year'])   : '';

    $map_months = array(
        'января'=>'01','февраля'=>'02','марта'=>'03','апреля'=>'04','мая'=>'05',
        'июня'=>'06','июля'=>'07','августа'=>'08','сентября'=>'09','октября'=>'10',
        'ноября'=>'11','декабря'=>'12'
    );
    $mon2 = isset($map_months[$month]) ? $map_months[$month] : '01';

    $data_s   = preg_replace('/^(\d)$/', '0${1}', $data_d);
    $data_sql = $year.'.'.$mon2.'.'.$data_s;
    $data_view = $data_d.' '.$month;
    $time2    = $data_s.'.'.$mon2.'.'.$year;

    $days = array('ВС','ПН','ВТ','СР','ЧТ','ПТ','СБ');
    $ts = strtotime($time2);
    if ($ts === false) {
        $dt = DateTime::createFromFormat('d.m.Y', $time2);
        $ts = $dt ? $dt->getTimestamp() : time();
    }
    $num_day = date('w', $ts);
    $nedel   = $days[$num_day];

    // Ищем приход
    $pr = false;
    if ($stmt = $mysqli->prepare("SELECT id, name FROM host1409556_barysh.prihods WHERE name = ?")) {
        $stmt->bind_param("s", $hram);
        if ($stmt->execute()) {
            $stmt->bind_result($pr_id, $pr_name);
            if ($stmt->fetch()) $pr = array('id'=>$pr_id,'name'=>$pr_name);
        }
        $stmt->close();
    }

    if ($pr && !empty($pr['name'])) {
        $text = $sluzba.'. <a href="prihod.php?id='.$pr['id'].'">'.htmlspecialchars($hram, ENT_QUOTES, 'CP1251').'</a>';
    } elseif ($hram === 'Жадовский монастырь') {
        $text = $sluzba.'. <a href="mon.php">Жадовский монастырь</a>';
    } else {
        $text = $sluzba.'. '.htmlspecialchars($hram, ENT_QUOTES, 'CP1251');
    }

    $patterns = array(
        '/великомученика/i','/святителя/i','/мучениц/i','/святого/i','/святых/i',
        '/священномученика/i','/равноапостольных/i','/апостола/i','/преподобного/i'
    );
    $replace = array('вмч.','свт.','мц.','св.','свв.','сщмч.','равноап.','ап.','прп.');
    $text = preg_replace($patterns, $replace, $text);

    if ($stmt = $mysqli->prepare("
        UPDATE host1409556_barysh.raspisanie
        SET data = ?, data_text = ?, nedel = ?, text = ?
        WHERE id = ?
    ")) {
        $stmt->bind_param("ssssi", $data_sql, $data_view, $nedel, $text, $id);
        $stmt->execute();
        $stmt->close();
    }

    echo '<p style="color:#135B00; text-align:center"><b>Изменения сохранены</b></p>';
}

/* --------- ЗАГРУЗКА ДАННЫХ --------- */
$row = false;
$res = $mysqli->query("SELECT * FROM host1409556_barysh.raspisanie WHERE id = ".intval($id));
if ($res) { $row = $res->fetch_assoc(); $res->free(); }
if (!$row) die("<p style='color:red; text-align:center'>Ошибка: запись не найдена</p>");

$data_sql  = $row['data'];
$data_text = $row['data_text'];
$nedel     = $row['nedel'];
$text      = $row['text'];

list($y, $m2, $d2) = explode('.', $data_sql);
$day_val = (int)$d2;

$months = array(
    '01'=>'января','02'=>'февраля','03'=>'марта','04'=>'апреля','05'=>'мая',
    '06'=>'июня','07'=>'июля','08'=>'августа','09'=>'сентября','10'=>'октября',
    '11'=>'ноября','12'=>'декабря'
);
$month_val = isset($months[$m2]) ? $months[$m2] : 'января';

$sluzba_val = ''; $hram_val = '';
if (preg_match('/^(\d{1,2}:\d{2})\s+(.*)$/', $text, $m)) {
    $time_part = $m[1]; $rest = $m[2];
} else { $time_part=''; $rest=$text; }
if (preg_match('/^(.*?\.)\s*(.*)$/', $rest, $m2)) {
    $sluzba_clean=$m2[1]; $hram_part=$m2[2];
} else { $sluzba_clean=$rest; $hram_part=''; }
$sluzba_val = trim(($time_part ? $time_part.' ' : '').$sluzba_clean);
$hram_val   = trim(strip_tags($hram_part));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include 'head.php'; ?>
    <title>Редактирование архиерейской службы</title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
    <style>
    .date-block {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
    }
    .date-day, .date-year {
        border: 1px solid #ccc;
        border-radius: 4px;
        text-align: center;
        padding: 4px;
        font-size: 14px;
    }
    .date-day { width: 45px; }
    .date-year { width: 60px; }

    .custom-select-wrap {
        position: relative;
        width: 130px;
    }

    .date-month {
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 4px 24px 4px 6px;
        font-size: 14px;
        background-color: #fff;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
    }

    .custom-select-wrap::after {
        content: "?";
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: #555;
        pointer-events: none;
    }

    .date-block input:focus, .date-month:focus {
        outline: none;
        border-color: #4a7bf7;
        box-shadow: 0 0 3px rgba(74,123,247,0.5);
    }
    </style>
</head>
<body>

<div style="box-shadow: 0 0 20px rgba(0,0,0,0.5);">
<?php include 'golova.php'; include 'menu.php'; include 'content.php'; ?>

<div id="osnovnoe">
<h1>Редактирование архиерейской службы</h1>

<table cellspacing="3" cellpadding="2" width="400" align="center" border="0">
<form action="raspisanie_edit.php" method="post">
<input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'CP1251') ?>" />

<tr><td valign="top"><b>Дата:</b></td><td></td></tr>
<tr><td colspan="2">
  <div class="date-block">
    <input type="text" name="data" class="date-day" maxlength="2"
           value="<?= htmlspecialchars($day_val, ENT_QUOTES, 'CP1251') ?>" />
    <div class="custom-select-wrap">
      <select name="month" class="date-month">
        <?php foreach ($months as $mm => $mname): ?>
          <option value="<?= htmlspecialchars($mname, ENT_QUOTES, 'CP1251') ?>"
            <?php if ($mname == $month_val) echo 'selected'; ?>>
            <?= htmlspecialchars($mname, ENT_QUOTES, 'CP1251') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <input type="text" name="year" class="date-year" maxlength="4"
           value="<?= htmlspecialchars($y, ENT_QUOTES, 'CP1251') ?>" />
  </div>
</td></tr>

<tr><td valign="top"><b>Время и служба:</b></td><td></td></tr>
<tr><td colspan="2">
    <input type="text" name="sluzba" size="40"
           value="<?= htmlspecialchars($sluzba_val, ENT_QUOTES, 'CP1251') ?>" />
</td></tr>

<tr><td valign="top"><b>Храм:</b></td><td></td></tr>
<tr><td colspan="2">
    <input type="text" name="hram" size="40" list="hrams"
           value="<?= htmlspecialchars($hram_val, ENT_QUOTES, 'CP1251') ?>" />
</td></tr>

<datalist id="hrams">
<?php
$res2 = $mysqli->query("SELECT name FROM host1409556_barysh.prihods ORDER BY name");
if ($res2) {
    while ($pr = $res2->fetch_assoc()) {
        echo '<option value="'.htmlspecialchars($pr['name'], ENT_QUOTES, 'CP1251').'">';
    }
    $res2->free();
}
?>
<option value="Жадовский монастырь">
</datalist>

<tr><td colspan="2" style="text-align:center">
    <input type="submit" name="submit" value="Сохранить" />
    <input type="button" value="Отмена" onclick="document.location='index.php'" />
</td></tr>
</form>
</table>

</div>

<?php include 'footer.php'; ?>

</div>
</body>
</html>