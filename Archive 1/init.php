<?php
declare(strict_types=1);

session_start();

// �������� ������ ������
$auth = $_SESSION['auth'] ?? null;
$name_user = $_SESSION['name_user'] ?? null;

// ���������� �������
require_once __DIR__ . '/function.php';
require_once __DIR__ . '/inc/db.php';

try {
    // Initialize PDO connection early
    getPdo();
} catch (PDOException $e) {
    die('������ ����������� � ���� ������: ' . htmlspecialchars($e->getMessage()));
}
