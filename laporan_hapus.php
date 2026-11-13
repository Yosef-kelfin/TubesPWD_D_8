<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('laporan_saya.php');
}

csrf_protect();

$id = (int) post('id');
if ($id <= 0) {
    abort_page(400, 'Permintaan tidak valid', 'Laporan yang dimaksud tidak ditemukan.');
}

$report = store_report($id);

if (!$report) {
    abort_page(404, 'Laporan tidak ditemukan', 'Laporan yang kamu cari sudah dihapus.');
}

require_owner($report);

$deleted = store_delete_report((int) $report['id']);

if ($deleted) {
    delete_photo((string) ($report['image'] ?? ''));
    flash('success', 'Laporan dihapus dari papan temuan.');
} else {
    flash('error', 'Laporan gagal dihapus. Coba lagi.');
}

redirect('laporan_saya.php');
