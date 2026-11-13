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

$status = post('status');
$allowed = statuses_for_type((string) $report['type']);

if (!isset($allowed[$status])) {
    abort_page(
        400,
        'Status tidak valid',
        'Status itu tidak berlaku untuk jenis laporan ' . (REPORT_TYPES[$report['type']] ?? $report['type']) . '.'
    );
}

$update = $pdo->prepare('UPDATE laporan SET status = :status WHERE id = :id AND pengguna_id = :pengguna_id');
$update->execute([
    ':status' => $status,
    ':id' => (int) $report['id'],
    ':pengguna_id' => (int) current_user()['id'],
]);

flash('success', 'Status laporan diubah menjadi ' . status_label($status) . '.');

if (post('redirect') === 'saya') {
    redirect('laporan_saya.php');
}
redirect('laporan_detail.php?id=' . (int) $report['id']);
