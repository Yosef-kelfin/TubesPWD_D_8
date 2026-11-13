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

$stmt = $pdo->prepare('SELECT * FROM laporan WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$report = $stmt->fetch();

if (!$report) {
    abort_page(404, 'Laporan tidak ditemukan', 'Laporan yang kamu cari sudah dihapus.');
}

require_owner($report);

$delete = $pdo->prepare('DELETE FROM laporan WHERE id = :id AND pengguna_id = :pengguna_id');
$delete->execute([
    ':id'          => (int) $report['id'],
    ':pengguna_id' => (int) current_user()['id'],
]);

if ($delete->rowCount() > 0) {
    delete_photo((string) ($report['image'] ?? ''));
    flash('success', 'Laporan dihapus dari papan temuan.');
} else {
    flash('error', 'Laporan gagal dihapus. Coba lagi.');
}

redirect('laporan_saya.php');
