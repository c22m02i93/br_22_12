<?php

declare(strict_types=1);

function ensureSessionStarted(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function generateCsrfToken(): string
{
    ensureSessionStarted();
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function validateCsrfToken(?string $token): bool
{
    ensureSessionStarted();
    $sessionToken = $_SESSION['csrf_token'] ?? null;
    return is_string($token) && is_string($sessionToken) && hash_equals($sessionToken, $token);
}
