<?php
// Opsional 2
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Opsional 2
function set_remember_me_cookie(string $token): void
{
    $secure = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';

    setcookie('remember_me', $token, [
        'expires' => time() + (30 * 24 * 60 * 60), // 30 hari
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    $_COOKIE['remember_me'] = $token;
}

// Opsional 2
function clear_remember_me_cookie(): void
{
    $secure = !empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off';

    setcookie('remember_me', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    unset($_COOKIE['remember_me']);
}

// Opsional 2
// Jangan mengakses database bila tidak ada cookie; ini penting saat PostgreSQL mati.
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $rememberToken = (string) $_COOKIE['remember_me'];

    if (!preg_match('/\A[a-f0-9]{64}\z/i', $rememberToken)) {
        clear_remember_me_cookie();
    } else {
        try {
            require_once __DIR__ . '/koneksi.php';

            $stmt = $pdo->prepare(
                'SELECT id, nama, role FROM users WHERE remember_token_hash = :token_hash LIMIT 1'
            );
            $stmt->execute(['token_hash' => hash('sha256', $rememberToken)]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
            } else {
                clear_remember_me_cookie();
            }
        } catch (Throwable $e) {
            // Opsional 4
            // Jika database sedang mati, biarkan cookie tetap tersimpan untuk dicoba lagi nanti.
            $_SESSION['flash'] = [
                'type' => 'error',
                'pesan' => 'Login otomatis belum dapat diverifikasi. Pastikan PostgreSQL aktif, lalu coba lagi.',
            ];
        }
    }
}
