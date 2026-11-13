<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

$id = (int) get('id');
if ($id <= 0) {
    abort_page(404, 'Laporan tidak ditemukan', 'Alamat laporan tidak valid.');
}

$stmt = $pdo->prepare(
    'SELECT l.*, p.name AS owner_name, p.email AS owner_email, p.teams AS owner_teams
     FROM laporan l
     JOIN pengguna p ON p.id = l.pengguna_id
     WHERE l.id = :id
     LIMIT 1'
);

$stmt->execute([':id' => $id]);
$report = $stmt->fetch();

if (!$report) {
    abort_page(404, 'Laporan tidak ditemukan', 'Laporan yang kamu cari sudah dihapus atau tidak pernah ada.');
}

$user = current_user();
$isOwner = $user !== null && (int) $report['pengguna_id'] === (int) $user['id'];
$statuses = statuses_for_type((string) $report['type']);
$teams = teams_url((string) $report['owner_teams']);
$hasPhoto = !empty($report['image']) && is_file(UPLOAD_DIR . '/' . basename((string) $report['image']));
$imageSrc = image_url($report['image'] ?? null, (string) $report['category']);
$tone = status_tone((string) $report['status']);

$page_title = $report['title'];
$active = 'home';
require __DIR__ . '/includes/header.php';
?>

<div class="wrap detail-top">
    <a class="btn btn--ghost btn--sm back-link" href="index.php">Kembali ke papan temuan</a>
</div>

<div class="wrap detail-grid">
    <figure class="detail-figure">
        <img src="<?= e($imageSrc) ?>"
            alt="<?= $hasPhoto ? 'Foto ' . e($report['title']) : 'Belum ada foto untuk ' . e($report['title']) ?>"
            width="800" height="600" />
        <figcaption>
            <span><?= e(report_code((string) $report['type'], (int) $report['id'])) ?></span>
            <span class="figure-type"><?= e(REPORT_TYPES[$report['type']] ?? $report['type']) ?></span>
        </figcaption>
    </figure>

    <div class="claim">
        <div class="claim-badges">
            <span
                class="badge badge--<?= e($report['type']) ?>"><?= e(type_label_short((string) $report['type'])) ?></span>
            <span class="stamp stamp--<?= e($tone) ?>"><?= e(status_label((string) $report['status'])) ?></span>
        </div>

        <h1><?= e($report['title']) ?></h1>
        <p class="claim-byline">
            Dilaporkan oleh <?= e($report['owner_name']) ?> &#183;
            <?= e(format_tanggal((string) $report['created_at'])) ?>
        </p>

        <dl class="claim-rows">
            <dt>Kode laporan</dt>
            <dd><?= e(report_code((string) $report['type'], (int) $report['id'])) ?></dd>

            <dt>Kategori</dt>
            <dd><?= e(CATEGORIES[$report['category']] ?? $report['category']) ?></dd>

            <dt>Lokasi <?= $report['type'] === 'ditemukan' ? 'ditemukan' : 'hilang' ?></dt>
            <dd><?= e($report['location']) ?></dd>

            <dt>Tanggal</dt>
            <dd><?= e(format_tanggal((string) $report['date'])) ?></dd>

            <dt>Status</dt>
            <dd><?= e(status_label((string) $report['status'])) ?></dd>

            <?php if ($report['type'] === 'ditemukan'): ?>
                <dt class="is-storage">Penyimpanan</dt>
                <dd class="is-storage">
                    <?= e(!empty($report['storage_location']) ? $report['storage_location'] : 'Belum dicantumkan') ?>
                </dd>
            <?php endif; ?>

            <dt>Pelapor</dt>
            <dd><?= e($report['owner_name']) ?><?php if (!$isOwner && $user !== null): ?> <span
                        class="muted small">(<?= e($report['owner_teams']) ?>)</span><?php elseif ($user === null): ?> <span
                        class="muted small">(kontak tersedia setelah masuk)</span><?php endif; ?></dd>
        </dl>

        <div class="claim-desc">
            <?php if (trim((string) $report['description']) !== ''): ?>
                <?= e($report['description']) ?>
            <?php else: ?>
                <span class="muted">Tidak ada deskripsi tambahan.</span>
            <?php endif; ?>
        </div>

        <?php if ($report['type'] === 'ditemukan'): ?>
            <div class="callout">
                <strong>Lokasi penyimpanan</strong>
                Barang berada di
                <?= e(!empty($report['storage_location']) ? $report['storage_location'] : 'lokasi yang belum dicantumkan') ?>.
                Hubungi pelapor lewat Teams sebelum mengambil.
            </div>
        <?php endif; ?>

        <div class="claim-actions">
            <?php if (!$isOwner): ?>
                <?php if ($teams !== ''): ?>
                    <?php if ($user === null): ?>
                        <a class="btn btn--signal" href="login.php?next=<?= e(rawurlencode($_SERVER['REQUEST_URI'] ?? ('/findit/laporan_detail.php?id=' . $id))) ?>"
                            data-login-gate="login-modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                aria-hidden="true" focusable="false">
                                <path fill="currentColor"
                                    d="M4 4h16a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H9l-5 4V5a1 1 0 0 1 1-1Zm3 4v2h10V8H7Zm0 4v2h7v-2H7Z" />
                            </svg>
                            Hubungi via Teams
                        </a>
                    <?php else: ?>
                        <a class="btn btn--signal" href="<?= e($teams) ?>" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                aria-hidden="true" focusable="false">
                                <path fill="currentColor"
                                    d="M4 4h16a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H9l-5 4V5a1 1 0 0 1 1-1Zm3 4v2h10V8H7Zm0 4v2h7v-2H7Z" />
                            </svg>
                            Hubungi via Teams
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="muted small">Pelapor belum mengisi kontak Teams.</span>
                <?php endif; ?>
            <?php else: ?>
                <span class="muted small">Ini laporanmu. Kelola status, ubah data, atau hapus lewat panel di bawah.</span>
            <?php endif; ?>
        </div>

        <div class="callout">
            <strong>Pengambilan barang</strong>
            FindIt! hanya menyediakan informasi. Pengambilan dan verifikasi kepemilikan dilakukan langsung sesuai
            prosedur kampus.
        </div>

        <?php if ($isOwner): ?>
            <section class="owner-panel" aria-labelledby="owner-title">
                <h2 id="owner-title">Kelola laporan</h2>

                <form class="status-form" method="post" action="laporan_status.php">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $report['id'] ?>" />
                    <input type="hidden" name="redirect" value="detail" />
                    <div class="field-inline">
                        <label for="status">Ubah status</label>
                        <select id="status" name="status">
                            <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?= e($key) ?>" <?= $report['status'] === $key ? ' selected="selected"' : '' ?>>
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn btn--ink btn--sm" type="submit">Simpan status</button>
                </form>

                <div class="owner-links">
                    <a class="btn btn--ghost btn--sm" href="laporan_edit.php?id=<?= (int) $report['id'] ?>">Edit laporan</a>
                    <form method="post" action="laporan_hapus.php"
                        data-confirm="<?= e('Hapus laporan "' . $report['title'] . '"? Foto yang terlampir juga akan dihapus.') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $report['id'] ?>" />
                        <button class="btn btn--danger btn--sm" type="submit">Hapus laporan</button>
                    </form>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php
if ($user === null) {
    $login_next = $_SERVER['REQUEST_URI'] ?? ('/findit/laporan_detail.php?id=' . $id);
    $login_title = 'Masuk untuk menghubungi pelapor';
    $login_body = 'Kontak Microsoft Teams hanya bisa dibuka setelah kamu masuk. Dengan akun FindIt!, kamu bisa menghubungi pelapor, melihat kontak, dan mengelola laporanmu sendiri.';
    require __DIR__ . '/includes/login_modal.php';
}
require __DIR__ . '/includes/footer.php';
?>