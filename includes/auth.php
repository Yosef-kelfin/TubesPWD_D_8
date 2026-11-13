<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/data.php';
require_once __DIR__ . '/functions.php';

/** ------------------------------------------------------------------
 *  Sesi & autentikasi
 *  ------------------------------------------------------------------ */
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => $secure,
    ]);
    session_start();
}

require_once __DIR__ . '/csrf.php';

function current_user(): ?array
{
    static $loaded = false;
    static $user   = null;

    if ($loaded) {
        return $user;
    }
    $loaded = true;

    $id = (int) ($_SESSION['user_id'] ?? 0);
    if ($id <= 0) {
        return null;
    }

    $row = store_user($id);

    if ($row === null) {
        unset($_SESSION['user_id']);
        return null;
    }

    unset($row['password']);
    $user = $row;
    return $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id']   = (int) $user['id'];
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => (bool) $params['secure'],
                'httponly' => (bool) $params['httponly'],
                'samesite' => 'Lax',
            ]
        );
    }

    session_destroy();
}

function require_login(): void
{
    if (is_logged_in()) {
        return;
    }

    flash('info', 'Masuk dulu untuk membuka halaman itu.');
    $next = $_SERVER['REQUEST_URI'] ?? 'index.php';
    redirect('login.php?next=' . rawurlencode($next));
}

function require_owner(array $report): void
{
    $user = current_user();
    if ($user === null || (int) $report['pengguna_id'] !== (int) $user['id']) {
        abort_page(
            403,
            'Tidak boleh mengakses',
            'Hanya pembuat laporan yang dapat mengubah atau menghapus laporan ini.'
        );
    }
}

/** Redirect tujuan setelah login, hanya untuk path lokal. */
function safe_next_url(string $next): string
{
    $next = trim($next);
    if ($next === '' || $next[0] !== '/' || str_starts_with($next, '//')) {
        return 'index.php';
    }
    if (str_contains($next, "\n") || str_contains($next, "\r")) {
        return 'index.php';
    }
    return $next;
}
