<?php
// Opsional 3
require_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';

if ($username === '' || $password === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username dan password wajib diisi.'];
    header('Location: login.php');
    exit;
}

// Opsional 3
$maxAttempts = 5;
$lockSeconds = 15 * 60;
$attemptKey = strtolower($username);
$attemptState = $_SESSION['login_attempts'][$attemptKey] ?? ['count' => 0, 'locked_until' => 0];
$now = time();

if (($attemptState['locked_until'] ?? 0) > $now) {
    $remainingMinutes = max(1, (int) ceil(($attemptState['locked_until'] - $now) / 60));
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => "Terlalu banyak percobaan login untuk username ini. Coba lagi sekitar {$remainingMinutes} menit.",
    ];
    header('Location: login.php');
    exit;
}

// Jika masa penguncian telah berakhir, hitungan dimulai kembali dari nol.
if (($attemptState['locked_until'] ?? 0) !== 0) {
    $attemptState = ['count' => 0, 'locked_until' => 0];
}

try {
    require __DIR__ . '/../includes/koneksi.php';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Opsional 2
        if ($rememberMe) {
            $rememberToken = bin2hex(random_bytes(32));
            $updateToken = $pdo->prepare(
                'UPDATE users SET remember_token_hash = :token_hash WHERE id = :id'
            );
            $updateToken->execute([
                'token_hash' => hash('sha256', $rememberToken),
                'id' => $user['id'],
            ]);
            set_remember_me_cookie($rememberToken);
        } else {
            // Opsional 2
            $updateToken = $pdo->prepare(
                'UPDATE users SET remember_token_hash = NULL WHERE id = :id'
            );
            $updateToken->execute(['id' => $user['id']]);
            clear_remember_me_cookie();
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        unset($_SESSION['login_attempts'][$attemptKey]);

        header('Location: ../index.php');
        exit;
    }
} catch (Throwable $e) {
    // Opsional 4
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Login belum dapat diproses karena database tidak dapat diakses. Pastikan PostgreSQL aktif.',
    ];
    header('Location: login.php');
    exit;
}

// Opsional 3
$failedCount = (int) ($attemptState['count'] ?? 0) + 1;
$lockedUntil = 0;
if ($failedCount >= $maxAttempts) {
    $lockedUntil = time() + $lockSeconds;
}
$_SESSION['login_attempts'][$attemptKey] = [
    'count' => $failedCount,
    'locked_until' => $lockedUntil,
];

if ($lockedUntil > 0) {
    $message = 'Username atau password salah. Batas percobaan tercapai; login dikunci sementara selama 15 menit.';
} else {
    $remaining = $maxAttempts - $failedCount;
    $message = "Username atau password salah. Sisa percobaan: {$remaining} kali.";
}

$_SESSION['flash'] = ['type' => 'error', 'pesan' => $message];
header('Location: login.php');
exit;
