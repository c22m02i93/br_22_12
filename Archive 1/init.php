<?php
// Запуск сессии, если передано имя сессии
if (isset($_REQUEST[session_name()])) {
    session_start();
}

// Получаем данные сессии
$auth = $_SESSION['auth'] ?? null;
$name_user = $_SESSION['name_user'] ?? null;

// Подключаем функции
require_once 'function.php';

// Подключаем базу
$mysqlConnection = mysql_connect("localhost", "host1409556", "0f7cd928");
if ($mysqlConnection) {
    mysql_query("SET NAMES 'cp1251'", $mysqlConnection);
} else {
    die('Ошибка подключения к базе данных');
}