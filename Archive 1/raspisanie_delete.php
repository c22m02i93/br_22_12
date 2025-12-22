<?php
session_start();

if ($_SESSION['auth'] != 1) {
    die("Доступ запрещён");
}

require_once 'db.php';

$id = intval($_GET['id']);

mysql_query("DELETE FROM host1409556_barysh.raspisanie WHERE id = $id");

header("Location: index.php"); // возвращаемся на главную
exit;
?>