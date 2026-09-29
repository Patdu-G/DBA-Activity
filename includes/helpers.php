<?php
function config(): array {
    static $c = null;
    return $c ??= require __DIR__ . '/../config/config.php';
}
function url(string $path = ''): string {
    return rtrim(config()['base_url'], '/') . '/' . ltrim($path, '/');
}
function e($v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
function redirect(string $path) {
    header('Location: ' . url($path));
    exit;
}
function db_error(PDOException $ex): string {
    $msg = $ex->getMessage();
    if ($ex->getCode() === '23505' || stripos($msg, 'UNIQUE constraint') !== false)
        return 'Duplicate value: that unique value already exists.';
    if ($ex->getCode() === '23503' || stripos($msg, 'FOREIGN KEY') !== false)
        return 'Relationship error: this record is linked to other records, or points to one that does not exist.';
    if ($ex->getCode() === '23514' || stripos($msg, 'CHECK constraint') !== false)
        return 'A value failed a CHECK rule (for example, quantity cannot be negative).';
    if ($ex->getCode() === '23502' || stripos($msg, 'NOT NULL') !== false)
        return 'A required field is missing.';
    return 'Database error: ' . $msg;
}
