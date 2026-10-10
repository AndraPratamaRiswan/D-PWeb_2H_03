<?php
// Opsional 2
require_once __DIR__ . '/session.php';

// Opsional 4
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Opsional 1
function require_role(string $requiredRole, string $redirect, string $message = 'Akses ditolak.'): void
{
    if (($_SESSION['role'] ?? '') !== $requiredRole) {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => $message];
        header('Location: ' . $redirect);
        exit;
    }
}
