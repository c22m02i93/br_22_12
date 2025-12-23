<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/db.php';

try {
    $pdo = getPdo();
    $GLOBALS['db'] = $pdo;
} catch (PDOException $e) {
    die('������ ����������� � MySQL: ' . htmlspecialchars($e->getMessage()));
}
