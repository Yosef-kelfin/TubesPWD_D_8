<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

/* ---------- Baca & bersihkan parameter filter (GET) ---------- */
$q = mb_substr(get('q'), 0, 100);
$kategori = get('kategori');
$jenis = get('jenis');
$status = get('status');
$lokasi = mb_substr(get('lokasi'), 0, 100);
$page = max(1, (int) get('page', '1'));

if (!isset(CATEGORIES[$kategori])) {
    $kategori = '';
}
if (!isset(REPORT_TYPES[$jenis])) {
    $jenis = '';
}
if (!isset(ALL_STATUSES[$status])) {
    $status = '';
}

/* ---------- Filter laporan ---------- */
$filters = [
    'q'        => $q,
    'kategori' => $kategori,
    'jenis'    => $jenis,
    'status'   => $status,
    'lokasi'   => $lokasi,
];

$total      = store_count_reports($filters);
$totalPages = max(1, (int) ceil($total / PAGE_SIZE));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * PAGE_SIZE;
$reports    = store_reports_for($filters, null, PAGE_SIZE, $offset);

/* ---------- Statistik ringkas ---------- */
$stats        = store_stats();
$totalAll     = (int) ($stats['total'] ?? 0);
$pendingFound = (int) ($stats['pending'] ?? 0);

/* ---------- Data untuk tautan ---------- */
$baseParams = [
    'q' => $q,
    'kategori' => $kategori,
    'jenis' => $jenis,
    'lokasi' => $lokasi,
    'status' => $status,
];
$hasFilter = $q !== '' || $kategori !== '' || $jenis !== '' || $lokasi !== '' || $status !== '';

$pageUrl = static function (int $number) use ($baseParams): string {
    return 'index.php' . query_string($baseParams + ['page' => $number > 1 ? (string) $number : '']);
};

$activeFilters = [];
if ($q !== '') {
    $activeFilters[] = 'Kata kunci: "' . $q . '"';
}
if ($kategori !== '') {
    $activeFilters[] = 'Kategori: ' . CATEGORIES[$kategori];
}
if ($jenis !== '') {
    $activeFilters[] = 'Jenis: ' . REPORT_TYPES[$jenis];
}
if ($lokasi !== '') {
    $activeFilters[] = 'Lokasi: ' . $lokasi;
}
if ($status !== '') {
    $activeFilters[] = 'Status: ' . ALL_STATUSES[$status];
}

$page_title = 'Beranda';
$active = 'home';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="wrap hero-inner">
        <div class="hero-copy">
            <p class="plate">Papan temuan · Kampus UAJY</p>
            <h1>Hilang di kampus?<br />Cek papan temuan.</h1>
            <p class="hero-sub">Laporan barang hilang dan temuan dari gedung, kantin, hingga parkiran. Detail kontak
                pelapor terbuka setelah kamu masuk.</p>
        </div>

        <form class="hero-search" method="get" action="index.php" role="search">
            <label class="search-label" for="q">Cari barang</label>
            <div class="search-row">
                <input type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="Tumbler, jaket, kunci…"
                    autocomplete="off" />
                <button class="btn btn--signal" type="submit">Cari</button>
            </div>
            <?php foreach (['kategori' => $kategori, 'jenis' => $jenis, 'lokasi' => $lokasi, 'status' => $status] as $name => $value): ?>
                <?php if ($value !== ''): ?>
                    <input type="hidden" name="<?= e($name) ?>" value="<?= e($value) ?>" />
                <?php endif; ?>
            <?php endforeach; ?>
            <p class="search-foot">
                <span><strong class="tnum"><?= (int) $totalAll ?></strong> laporan</span>
                <span class="search-sep" aria-hidden="true">&#183;</span>
                <span><strong class="tnum"><?= (int) $pendingFound ?></strong> belum dikembalikan</span>
                <?php if ($hasFilter): ?>
                    <a class="btn btn--ghost btn--sm search-all" href="index.php">Reset filter</a>
                <?php elseif (is_logged_in()): ?>
                    <a class="btn btn--ghost btn--sm search-all" href="laporan_buat.php?type=ditemukan">Laporkan temuan</a>
                <?php else: ?>
                    <a class="btn btn--ghost btn--sm search-all" href="register.php">Daftar akun</a>
                <?php endif; ?>
            </p>
        </form>
    </div>
</section>

<div class="wrap board-layout">
    <aside class="filter-panel">
        <h2 class="filter-title">Saring laporan</h2>
        <p class="filter-note">Filter bisa dikombinasikan. URL hasil pencarian dapat dibagikan.</p>

        <form method="get" action="index.php">
            <input type="hidden" name="q" value="<?= e($q) ?>" />
            <div class="filters-grid">
                <fieldset class="filter-group">
                    <legend>Jenis laporan</legend>
                    <div class="chip-row">
                        <label class="chip">
                            <input type="radio" name="jenis" value="" <?= $jenis === '' ? ' checked="checked"' : '' ?> />
                            <span>Semua</span>
                        </label>
                        <?php foreach (REPORT_TYPES as $key => $label): ?>
                            <label class="chip">
                                <input type="radio" name="jenis" value="<?= e($key) ?>" <?= $jenis === $key ? ' checked="checked"' : '' ?> />
                                <span><?= e($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>

                <div class="filter-group">
                    <label for="filter-kategori">Kategori</label>
                    <select id="filter-kategori" name="kategori">
                        <option value="">Semua kategori</option>
                        <?php foreach (CATEGORIES as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $kategori === $key ? ' selected="selected"' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filter-lokasi">Lokasi</label>
                    <input type="text" id="filter-lokasi" name="lokasi" value="<?= e($lokasi) ?>"
                        list="lokasi-kampus-filter" placeholder="Kantin, parkiran, gedung…" />
                    <datalist id="lokasi-kampus-filter">
                        <?php foreach (CAMPUS_LOCATIONS as $place): ?>
                            <option value="<?= e($place) ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="filter-group">
                    <label for="filter-status">Status</label>
                    <select id="filter-status" name="status">
                        <option value="">Semua status</option>
                        <?php foreach (ALL_STATUSES as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $status === $key ? ' selected="selected"' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-actions">
                    <button class="btn btn--signal btn--block" type="submit">Terapkan filter</button>
                    <a class="btn btn--ghost btn--block" href="index.php">Bersihkan filter</a>
                </div>
            </div>
        </form>
    </aside>

    <section class="board-main" aria-label="Daftar laporan">
        <div class="board-head">
            <div>
                <h2>Papan laporan</h2>
                <p class="board-sub">
                    <?php if ($total === 0): ?>
                        Tidak ada laporan yang cocok
                    <?php else: ?>
                        <?= (int) $total ?> laporan<?= $hasFilter ? ' cocok dengan filter' : ' terbaru' ?>
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($totalPages > 1): ?>
                <p class="board-page tnum">Halaman <?= (int) $page ?> dari <?= (int) $totalPages ?></p>
            <?php endif; ?>
        </div>

        <?php if ($activeFilters !== []): ?>
            <div class="active-filters">
                <?php foreach ($activeFilters as $filterLabel): ?>
                    <span class="chip-static"><?= e($filterLabel) ?></span>
                <?php endforeach; ?>
                <a class="btn btn--ghost btn--sm" href="index.php">Hapus semua</a>
            </div>
        <?php endif; ?>

        <div class="board-grid">
            <?php if ($reports === []): ?>
                <div class="empty-board">
                    <img src="assets/img/empty-board.svg" alt="" width="190" height="143" />
                    <h3>Belum ada yang cocok</h3>
                    <p>Ubah kata kunci atau kosongkan sebagian filter. Kalau barangmu belum ada, buat laporan supaya orang
                        lain bisa menemukannya.</p>
                    <div class="empty-actions">
                        <a class="btn btn--ghost" href="index.php">Bersihkan filter</a>
                        <?php if (is_logged_in()): ?>
                            <a class="btn btn--signal" href="laporan_buat.php">Buat laporan</a>
                        <?php else: ?>
                            <a class="btn btn--signal" href="login.php">Masuk</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($reports as $r): ?>
                    <?php require __DIR__ . '/includes/report_card.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <?php $numbers = pager_numbers($page, $totalPages); ?>
            <nav class="pager" aria-label="Navigasi halaman laporan">
                <?php if ($page > 1): ?>
                    <a href="<?= e($pageUrl($page - 1)) ?>" rel="prev">Sebelumnya</a>
                <?php else: ?>
                    <span class="is-disabled" aria-disabled="true">Sebelumnya</span>
                <?php endif; ?>

                <?php if (!in_array(1, $numbers, true)): ?>
                    <a href="<?= e($pageUrl(1)) ?>">1</a>
                    <?php if ($numbers[0] > 2): ?>
                        <span class="pager-gap" aria-hidden="true">…</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php foreach ($numbers as $number): ?>
                    <?php if ($number === $page): ?>
                        <span class="is-current" aria-current="page"><?= (int) $number ?></span>
                    <?php else: ?>
                        <a href="<?= e($pageUrl($number)) ?>"><?= (int) $number ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if (!in_array($totalPages, $numbers, true)): ?>
                    <?php if (end($numbers) < $totalPages - 1): ?>
                        <span class="pager-gap" aria-hidden="true">…</span>
                    <?php endif; ?>
                    <a href="<?= e($pageUrl($totalPages)) ?>"><?= (int) $totalPages ?></a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= e($pageUrl($page + 1)) ?>" rel="next">Berikutnya</a>
                <?php else: ?>
                    <span class="is-disabled" aria-disabled="true">Berikutnya</span>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>