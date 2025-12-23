<?php

declare(strict_types=1);

// ������ ������, ���� �������� ��� ������
if (session_status() === PHP_SESSION_NONE && isset($_REQUEST[session_name()])) {
    session_start();
}

// �������� ������ ������
$auth = $_SESSION['auth'] ?? null;
$name_user = $_SESSION['name_user'] ?? null;

// ���������� �������
require_once __DIR__ . '/function.php';
require_once __DIR__ . '/inc/env.php';
require_once __DIR__ . '/inc/db.php';

// ���������� ����
$pdo = getPdoConnection();
