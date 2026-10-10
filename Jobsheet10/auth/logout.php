<?php
// Opsional 2
require_once __DIR__ . '/../includes/session.php';

// Opsional 2
if (isset($_SESSION['user_id'])) {
    try {
        require __DIR__ . '/../includes/koneksi.php';
        $stmt = $pdo->prepare('UPDATE users SET remember_token_hash = NULL WHERE id = :id');
        $stmt->execute(['id' => $_SESSION['user_id']]);
    } catch (Throwable $e) {
        // Logout lokal tetap dilakukan jika PostgreSQL sedang tidak bisa diakses.
    }
}

clear_remember_me_cookie();
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?? 'Lax',
    ]);
}

session_destroy();
header('Location: login.php');
exit;
