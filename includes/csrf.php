<?php
declare(strict_types=1);

/** ------------------------------------------------------------------
 *  CSRF token per sesi
 *  ------------------------------------------------------------------ */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '" />';
}

function csrf_protect(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || $sent === '' || !hash_equals(csrf_token(), $sent)) {
        abort_page(
            400,
            'Permintaan ditolak',
            'Token keamanan tidak valid atau sesi sudah berakhir. Muat ulang halaman lalu coba lagi.'
        );
    }
}
