<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int) get('id');
$stmt = $pdo->prepare('SELECT * FROM laporan WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$report = $stmt->fetch();

if (!$report) {
    abort_page(404, 'Laporan tidak ditemukan', 'Laporan yang kamu cari sudah dihapus atau tidak pernah ada.');
}

require_owner($report);

$errors = [];
$form = [
    'type'             => (string) $report['type'],
    'title'            => (string) $report['title'],
    'category'         => (string) $report['category'],
    'location'         => (string) $report['location'],
    'date'             => (string) $report['date'],
    'description'      => (string) ($report['description'] ?? ''),
    'storage_location' => (string) ($report['storage_location'] ?? ''),
];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_protect();

    $form      = read_report_input();
    $errors    = validate_report($form);
    $newImage  = null;
    $removeOld = post('hapus_foto') === '1';

    if ($errors === []) {
        $newImage = upload_photo($_FILES['image'] ?? [], $errors);
    }

    if ($errors === []) {
        $oldImage = (string) ($report['image'] ?? '');

        if ($newImage !== null) {
            $finalImage = $newImage;
        } elseif ($removeOld) {
            $finalImage = null;
        } else {
            $finalImage = $oldImage !== '' ? $oldImage : null;
        }

        $newStatus = $form['type'] === $report['type']
            ? (string) $report['status']
            : default_status($form['type']);

        try {
            $update = $pdo->prepare(
                'UPDATE laporan SET
                    type = :type,
                    title = :title,
                    category = :category,
                    location = :location,
                    date = :date,
                    description = :description,
                    image = :image,
                    storage_location = :storage_location,
                    status = :status
                 WHERE id = :id AND pengguna_id = :pengguna_id'
            );
            $update->execute([
                ':type'             => $form['type'],
                ':title'            => $form['title'],
                ':category'         => $form['category'],
                ':location'         => $form['location'],
                ':date'             => $form['date'],
                ':description'      => $form['description'] !== '' ? $form['description'] : null,
                ':image'            => $finalImage,
                ':storage_location' => $form['type'] === 'ditemukan' ? $form['storage_location'] : null,
                ':status'           => $newStatus,
                ':id'               => (int) $report['id'],
                ':pengguna_id'      => (int) current_user()['id'],
            ]);

            if ($oldImage !== '' && ($newImage !== null || $removeOld)) {
                delete_photo($oldImage);
            }

            flash('success', 'Perubahan laporan disimpan.');
            redirect('laporan_detail.php?id=' . (int) $report['id']);
        } catch (PDOException $e) {
            if ($newImage !== null) {
                delete_photo($newImage);
            }
            $errors['form'] = 'Perubahan gagal disimpan. Coba lagi sebentar lagi.';
        }
    }
}

$current_image = (string) ($report['image'] ?? '');
$form_action   = 'laporan_edit.php?id=' . (int) $report['id'];
$is_edit       = true;
$cancel_url    = 'laporan_detail.php?id=' . (int) $report['id'];

$page_title = 'Edit laporan';
$active     = 'saya';
require __DIR__ . '/includes/header.php';
?>

<div class="wrap">
    <div class="page-head">
        <p class="plate">Edit laporan</p>
        <h1><?= e($report['title']) ?></h1>
        <p class="lede">Perbarui data laporan. Status diubah terpisah dari halaman detail.</p>
    </div>

    <div class="form-shell">
        <?php if (isset($errors['form'])) : ?>
            <div class="form-errors" role="alert">
                <strong>Gagal menyimpan.</strong>
                <?= e($errors['form']) ?>
            </div>
        <?php endif; ?>

        <?php require __DIR__ . '/includes/laporan_form.php'; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
