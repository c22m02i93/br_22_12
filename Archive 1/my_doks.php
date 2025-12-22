<?php
// --- Авторизация ---
session_start();
$auth      = isset($_SESSION['auth']) ? $_SESSION['auth'] : 0;
$name_user = isset($_SESSION['name_user']) ? $_SESSION['name_user'] : '';

if ($auth != 1) {
    Header("Location: my_auth.php");
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include 'head.php'; ?>
<script src="jquery.js" type="text/javascript"></script>
<title>Добавление документов</title>

<script src="jquery.js" type="text/javascript"></script>

<script type="text/javascript">
$(function () {
    var $links = $('div.tabs ul.tabNavigation a');
    var $tabContainers = $('div.tabs > div').hide();

    function openTab(hash) {
        $tabContainers.hide();
        $tabContainers.filter(hash).show();
        $links.removeClass('selected');
        $links.filter('[href="'+hash+'"]').addClass('selected');
    }

    $links.on('click', function () {
        openTab(this.hash);
        window.location.hash = this.hash; // сохраняем вкладку
        return false;
    });

    var startHash = window.location.hash;
    if (startHash && $links.filter('[href="'+startHash+'"]').length) {
        openTab(startHash);
    } else {
        $links.first().trigger('click');
    }
});
</script>

</head>
<body>

<div style="box-shadow: 0 0 20px rgba(0,0,0,0.5);">
<?php
include 'golova.php';
include 'menu.php';
include 'db.php';
include 'content.php';
?>

<div id="osnovnoe">
<h1>Добавление документов</h1>

<?php
// Сообщение об успехе (после редиректа)
if (isset($_GET['ok'])) {
    echo '<p style="color:#135B00; text-align: center"><b>Документ успешно добавлен!</b></p><br />';
}

if (isset($_POST['submit'])) {

    $tematika = isset($_POST['tematika']) ? $_POST['tematika'] : '';
    $name     = isset($_POST['name']) ? $_POST['name'] : '';
    $san      = isset($_POST['san']) ? $_POST['san'] : '';
    $nomer    = isset($_POST['nomer']) ? trim($_POST['nomer']) : '';
    $text     = isset($_POST['text']) ? $_POST['text'] : '';

    $date = Date("Y.m.d H:i:s");
    $year = Date("Y");

    // Разрешённые типы (чтобы не подсунули мусор)
    $allowed = array('ukaz','raspor','cirk','udostoverenie');
    if (!in_array($tematika, $allowed, true)) {
        die('Ошибка: неизвестная тематика');
    }

    // Подключение к БД (оставил как у тебя, чтобы точно работало)
    mysql_connect("localhost", "host1409556", "0f7cd928");
    @mysql_query("SET NAMES utf8");

    // Твой фильтр для текста (оставлен)
    $p_msg = array ('/\\\/', '/\"/', '/\'/', '/\`/', '/\%/', '/\$/', '/\</', '/\>/');
    $r_msg = array ('&#092;', '&quot;', '&#039;', '&#096;', '&#037;', '&#036;', '&#060;', '&#062;');
    $text  = preg_replace($p_msg, $r_msg, $text);

    // Номер документа:
    // - для "Указ": если ввели вручную — берём его, иначе авто (COUNT + 1)
    // - для остальных: берём то, что ввели (там required стоит)
    if ($tematika == 'ukaz') {
        if ($nomer !== '') {
            $numer = (int)$nomer; // ручной номер имеет приоритет
        } else {
            $xx  = mysql_query("SELECT COUNT(*) AS cnt
                                FROM host1409556_barysh.doks
                                WHERE year='$year' AND tematika='ukaz'");
            $row = mysql_fetch_assoc($xx);
            $numer = ((int)$row['cnt']) + 1;
        }
    } else {
        $numer = (int)$nomer;
    }

    // Для "Удостоверение" — как было: name = san
    if ($tematika == 'udostoverenie') {
        $name = $san;
    }

    // Минимальное экранирование (чтобы не ломались кавычки)
    $name     = mysql_real_escape_string($name);
    $text     = mysql_real_escape_string($text);
    $tematika = mysql_real_escape_string($tematika);

    mysql_query("INSERT INTO host1409556_barysh.doks VALUES ('$date', '$year', '$numer', '$name', '$text', '$tematika')");

    // Редирект на нужную вкладку + ok=1 (чтобы не было повторной отправки при обновлении)
    $tabHash = '#first';
    if ($tematika == 'raspor') $tabHash = '#second';
    if ($tematika == 'cirk')  $tabHash = '#cirk';
    if ($tematika == 'udostoverenie') $tabHash = '#fift';

    Header("Location: my_doks.php?ok=1".$tabHash);
    exit;
}
?>

<br />

<div class="tabs">
    <!-- Вкладки -->
    <ul class="tabNavigation">
        <li><a class="" href="#first">Указ</a></li>
        <li><a class="" href="#second">Распоряжение</a></li>
        <li><a class="" href="#cirk">Циркуляр</a></li>
        <li><a class="" href="#fift">Удостоверение</a></li>
    </ul>

    <!-- Контейнеры вкладок -->

    <div id="first">
        <span class="table">
        <TABLE CELLSPACING=3 CELLPADDING=2 width='400' align='center' border=0>
            <FORM ACTION='my_doks.php' method='post'>
                <TR>
                    <TD VALIGN=top>
                        <INPUT TYPE="HIDDEN" NAME="tematika" VALUE="ukaz">
                    </TD>
                    <TD></TD>
                </TR>

                <!-- Номер (необязательный) -->
                <TR><TD VALIGN=top><b>Номер документа (если пусто — авто):</b></TD><TD></TD></TR>
                <TR><TD colspan=2><INPUT TYPE="TEXT" NAME='nomer' SIZE=75 /></TD></TR>

                <TR><TD VALIGN=top><b>Кому выдан документ:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><INPUT TYPE="TEXT" NAME='name' SIZE=75 required/></TD></TR>

                <TR><TD VALIGN=top><b>Текст:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><TEXTAREA NAME='text' COLS=55 ROWS=10 required></TEXTAREA></TD></TR>

                <TR><TD VALIGN=top colspan=2>
                    <INPUT TYPE='submit' name='submit' value='Добавить' />
                    <INPUT TYPE='reset' value='Очистить'>
                </TD></TR>
            </FORM>
        </TABLE>
        </span>
    </div>

    <div id="second">
        <span class="table">
        <TABLE CELLSPACING=3 CELLPADDING=2 width='400' align='center' border=0>
            <FORM ACTION='my_doks.php' method='post'>
                <TR>
                    <TD VALIGN=top>
                        <INPUT TYPE="HIDDEN" NAME="tematika" VALUE="raspor">
                    </TD>
                    <TD></TD>
                </TR>

                <TR><TD VALIGN=top><b>Номер документа:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><INPUT TYPE="TEXT" NAME='nomer' SIZE=75 required/></TD></TR>

                <TR><TD VALIGN=top><b>Кому выдан документ:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><INPUT TYPE="TEXT" NAME='name' SIZE=75/></TD></TR>

                <TR><TD VALIGN=top><b>Текст:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><TEXTAREA NAME='text' COLS=55 ROWS=10 required></TEXTAREA></TD></TR>

                <TR><TD VALIGN=top colspan=2>
                    <INPUT TYPE='submit' name='submit' value='Добавить' />
                    <INPUT TYPE='reset' value='Очистить'>
                </TD></TR>
            </FORM>
        </TABLE>
        </span>
    </div>

    <div id="cirk">
        <span class="table">
        <TABLE CELLSPACING=3 CELLPADDING=2 width='400' align='center' border=0>
            <FORM ACTION='my_doks.php' method='post'>
                <TR>
                    <TD VALIGN=top>
                        <INPUT TYPE="HIDDEN" NAME="tematika" VALUE="cirk">
                    </TD>
                    <TD></TD>
                </TR>

                <TR><TD VALIGN=top><b>Номер документа:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><INPUT TYPE="TEXT" NAME='nomer' SIZE=75 required/></TD></TR>

                <TR><TD VALIGN=top><b>Кому выдан документ:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><INPUT TYPE="TEXT" NAME='name' SIZE=75/></TD></TR>

                <TR><TD VALIGN=top><b>Текст:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><TEXTAREA NAME='text' COLS=55 ROWS=10 required></TEXTAREA></TD></TR>

                <TR><TD VALIGN=top colspan=2>
                    <INPUT TYPE='submit' name='submit' value='Добавить' />
                    <INPUT TYPE='reset' value='Очистить'>
                </TD></TR>
            </FORM>
        </TABLE>
        </span>
    </div>

    <div id="fift">
        <span class="table">
        <TABLE CELLSPACING=3 CELLPADDING=2 width='400' align='center' border=0>
            <FORM ACTION='my_doks.php' method='post'>

                <TR><TD VALIGN=top><b>Сан:</b></TD><TD></TD></TR>
                <TR>
                    <TD VALIGN=top>
                        <INPUT TYPE="HIDDEN" NAME="tematika" VALUE="udostoverenie">
                        <input type='radio' name='san' value='диакона' checked id="diak"><label for="diak">диакона</label><br />
                        <input type='radio' name='san' value='пресвитера' id="ierei"><label for="ierei">пресвитера</label><br />
                    </TD>
                    <TD></TD>
                </TR>

                <TR><TD VALIGN=top><b>Номер документа:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><INPUT TYPE="TEXT" NAME='nomer' SIZE=75 required/></TD></TR>

                <TR><TD VALIGN=top><b>Текст:</b></TD><TD></TD></TR>
                <TR><TD colspan=2><TEXTAREA NAME='text' COLS=55 ROWS=10 required></TEXTAREA></TD></TR>

                <TR><TD VALIGN=top colspan=2>
                    <INPUT TYPE='submit' name='submit' value='Добавить' />
                    <INPUT TYPE='reset' value='Очистить'>
                </TD></TR>
            </FORM>
        </TABLE>
        </span>

        <hr />
        <p><b>Правила оформления:</b></p>
        <p><b>{{{http://ссылка}}}-{{{текст, который будет отображаться}}}</b> — активная ссылка. Ввод <b>http://</b> перед ссылкой обязателен.</p>
    </div>

</div> <!-- /.tabs -->

</div> <!-- /#osnovnoe -->

<?php include 'footer.php'; ?>


</div>

<style>
  /* по умолчанию показываем только первую вкладку */
  .tabs > div { display: none; }
  .tabs > div:first-of-type { display: block; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var tabs = document.querySelectorAll('div.tabs > div');
  var links = document.querySelectorAll('div.tabs ul.tabNavigation a');

  function openTab(hash) {
    // скрыть все панели
    for (var i = 0; i < tabs.length; i++) tabs[i].style.display = 'none';

    // снять выделение со всех ссылок
    for (var j = 0; j < links.length; j++) links[j].classList.remove('selected');

    // показать нужную
    var panel = document.querySelector(hash);
    if (panel) panel.style.display = 'block';

    // выделить ссылку
    var activeLink = document.querySelector('div.tabs ul.tabNavigation a[href="' + hash + '"]');
    if (activeLink) activeLink.classList.add('selected');
  }

  // клики по вкладкам
  for (var k = 0; k < links.length; k++) {
    links[k].addEventListener('click', function (e) {
      e.preventDefault();
      openTab(this.getAttribute('href'));
      location.hash = this.getAttribute('href');
    });
  }

  // стартовая вкладка
  var startHash = location.hash;
  var startLink = document.querySelector('div.tabs ul.tabNavigation a[href="' + startHash + '"]');

  if (startHash && startLink) {
    openTab(startHash);
  } else if (links.length) {
    openTab(links[0].getAttribute('href'));
  }
});
</script>
</body>
</html>