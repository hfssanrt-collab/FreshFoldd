<?php
// Pengaturan sesi + helper login. Di-include oleh index.php, api.php, login.php, logout.php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

// Untuk halaman biasa: arahkan ke login jika belum masuk
function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// Untuk API: balas 401 (JSON) jika belum masuk
function require_login_api(): void
{
    if (!is_logged_in()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'auth'    => false,
            'message' => 'Sesi berakhir, silakan login kembali',
        ]);
        exit;
    }
}
