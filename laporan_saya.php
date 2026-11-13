<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();
$userId = (int) $user['id'];

$jenis = get('jenis');
if (!isset(REPORT_TYPES[$jenis])) {
    $jenis = '';
}
$page = max(1, (int) get('page', '1'));

$stats = store_stats($userId);

$filters = $jenis !== '' ? ['jenis' => $jenis] : [];
$total   = store_count_reports($filters, $userId);

$totalPages = max(1, (int) ceil($total / PAGE_SIZE));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * PAGE_SIZE;

$reports = store_reports_for($filters, $userId, PAGE_SIZE, $offset);

$pageUrl = static function (int $number) use ($jenis): string {
    return 'laporan_saya.php' . query_string([
        'jenis' => $jenis,
        'page'  => $number > 1 ? (string) $number : '',
    ]);
};

$page_title = 'Laporan saya';
$active     = 'saya';
require __DIR__ . '/includes/header.php';
?>

<div class="wrap">
    <div class="page-head">
        <div class="page-head-row">
            <div>
                <p class="plate">Akun saya</p>
                <h1>Laporan saya</h1>
                <p class="lede">Semua laporan yang kamu buat. Buka detail untuk mengubah status, mengedit, atau menghapus laporan.</p>
            </div>
            <div class="page-head-actions">
                <a class="btn btn--signal" href="laporan_buat.php">Buat laporan</a>
            </div>
        </div>

        <div class="my-summary">
            <span class="summary-chip"><strong class="tnum"><?= (int) ($stats['total'] ?? 0) ?></strong> total</span>
            <span class="summary-chip"><strong class="tnum"><?= (int) ($stats['hilang'] ?? 0) ?></strong> hilang</span>
            <span class="summary-chip"><strong class="tnum"><?= (int) ($stats['ditemukan'] ?? 0) ?></strong> ditemukan</span>
            <span class="summary-chip"><strong class="tnum"><?= (int) ($stats['selesai'] ?? 0) ?></strong> selesai</span>
        </div>

        <nav class="segmented" aria-label="Saring laporan saya">
            <a href="laporan_saya.php"<?= $jenis === '' ? ' class="is-active" aria-current="page"' : '' ?>>Semua</a>
            <a href="laporan_saya.php?jenis=hilang"<?= $jenis === 'hilang' ? ' class="is-active" aria-current="page"' : '' ?>>Hilang</a>
            <a href="laporan_saya.php?jenis=ditemukan"<?= $jenis === 'ditemukan' ? ' class="is-active" aria-current="page"' : '' ?>>Ditemukan</a>
        </nav>
    </div>

    <div class="board-head">
        <div>
            <h2><?= $jenis === '' ? 'Semua laporanmu' : e(REPORT_TYPES[$jenis]) ?></h2>
            <p class="board-sub"><?= (int) $total ?> laporan</p>
        </div>
        <?php if ($totalPages > 1) : ?>
            <p class="board-page tnum">Halaman <?= (int) $page ?> dari <?= (int) $totalPages ?></p>
        <?php endif; ?>
    </div>

    <div class="board-grid">
        <?php if ($reports === []) : ?>
            <div class="empty-board">
                <img src="assets/img/empty-board.svg" alt="" width="190" height="143" />
                <h3>Belum ada laporan di sini</h3>
                <p>Kalau kamu kehilangan atau menemukan barang, catat sekarang supaya bisa ditemukan orang lain.</p>
                <div class="empty-actions">
                    <a class="btn btn--signal" href="laporan_buat.php">Buat laporan pertama</a>
                </div>
            </div>
        <?php else : ?>
            <?php $show_actions = true; ?>
            <?php foreach ($reports as $r) : ?>
                <?php require __DIR__ . '/includes/report_card.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1) : ?>
        <?php $numbers = pager_numbers($page, $totalPages); ?>
        <nav class="pager" aria-label="Navigasi halaman laporan saya">
            <?php if ($page > 1) : ?>
                <a href="<?= e($pageUrl($page - 1)) ?>" rel="prev">Sebelumnya</a>
            <?php else : ?>
                <span class="is-disabled" aria-disabled="true">Sebelumnya</span>
            <?php endif; ?>

            <?php foreach ($numbers as $number) : ?>
                <?php if ($number === $page) : ?>
                    <span class="is-current" aria-current="page"><?= (int) $number ?></span>
                <?php else : ?>
                    <a href="<?= e($pageUrl($number)) ?>"><?= (int) $number ?></a>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($page < $totalPages) : ?>
                <a href="<?= e($pageUrl($page + 1)) ?>" rel="next">Berikutnya</a>
            <?php else : ?>
                <span class="is-disabled" aria-disabled="true">Berikutnya</span>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
