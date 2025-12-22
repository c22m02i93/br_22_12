<?php

// Подключаемся к MySQL (старый способ для PHP 5.4)
$mysqlConnection = mysql_connect("localhost", "host1409556", "0f7cd928");

if (!$mysqlConnection) {
    die("Ошибка подключения к MySQL");
}

// Выбираем базу
mysql_select_db("host1409556_barysh", $mysqlConnection);

// Устанавливаем кодировку cp1251 (как в дампе)
mysql_query("SET NAMES 'cp1251'", $mysqlConnection);

// Делаем соединение глобальным, чтобы скрипты могли его использовать
$GLOBALS['db'] = $mysqlConnection;