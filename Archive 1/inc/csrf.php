<?php

declare(strict_types=1);

function ensureSessionStarted(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function getCsrfToken(): string
{
    ensureSessionStarted();

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validateCsrfToken(?string $token): bool
{
    ensureSessionStarted();

    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    if ($token === null) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}
