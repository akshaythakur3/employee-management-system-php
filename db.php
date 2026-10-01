<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function getDbConnection(): mysqli
{
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: '';
    $dbname = getenv('DB_NAME') ?: 'project_db';

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        throw new RuntimeException('Database connection failed: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');

    return $conn;
}

function csrfToken(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function isCsrfTokenValid(mixed $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}
