<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('index.php');
}

csrf_protect();
logout_user();

/* Sesi baru hanya untuk membawa flash message. */
session_start();
flash('info', 'Kamu sudah keluar. Sampai jumpa lagi.');
redirect('login.php');
