<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();

$pickType = get('type');
$form = [
    'type' => isset(REPORT_TYPES[$pickType]) ? $pickType : 'hilang',
    'title' => '',
    'category' => '',
    'location' => '',
    'date' => date('Y-m-d'),
    'description' => '',
    'storage_location' => '',
];
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_protect();

    $form = read_report_input();
    $errors = validate_report($form);
    $newImage = null;

    if ($errors === []) {
        $newImage = upload_photo($_FILES['image'] ?? [], $errors);
    }

    if ($errors === []) {
        $status = default_status($form['type']);

        $newId = store_create_report([
            'pengguna_id' => (int) $user['id'],
            'type' => $form['type'],
            'title' => $form['title'],
            'category' => $form['category'],
            'location' => $form['location'],
            'date' => $form['date'],
            'description' => $form['description'] !== '' ? $form['description'] : null,
            'image' => $newImage,
            'storage_location' => $form['type'] === 'ditemukan' ? $form['storage_location'] : null,
            'status' => $status,
        ]);

        flash('success', 'Laporan tersimpan di papan temuan.');
        redirect('laporan_detail.php?id=' . $newId);
    }
}

$form_action = 'laporan_buat.php';
$is_edit = false;
$current_image = null;
$cancel_url = 'index.php';

$page_title = 'Buat laporan';
$active = 'buat';
require __DIR__ . '/includes/header.php';
?>

<div class="wrap">
    <div class="page-head">
        <p class="plate">Laporan baru</p>
        <h1>Catat barang hilang atau temuan</h1>
        <p class="lede">Isi informasi sejelas mungkin. Laporan barang ditemukan wajib mencantumkan lokasi penyimpanan
            supaya pemilik bisa mengambilnya.</p>
    </div>

    <div class="form-shell">
        <?php if (isset($errors['form'])): ?>
            <div class="form-errors" role="alert">
                <strong>Gagal menyimpan.</strong>
                <?= e($errors['form']) ?>
            </div>
        <?php endif; ?>

        <?php require __DIR__ . '/includes/laporan_form.php'; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>