<?php
declare(strict_types=1);

/** Partial kartu laporan. Variabel: $r (baris laporan), $show_actions (opsional). */
if (!isset($r) || !is_array($r)) {
    return;
}

$showActions = isset($show_actions) ? (bool) $show_actions : false;
$detailUrl   = 'laporan_detail.php?id=' . (int) $r['id'];
$imageSrc    = image_url($r['image'] ?? null, (string) $r['category']);
$hasPhoto    = !empty($r['image']) && is_file(UPLOAD_DIR . '/' . basename((string) $r['image']));
$altText     = $hasPhoto
    ? 'Foto ' . $r['title']
    : 'Belum ada foto untuk ' . $r['title'];
?>
<article class="tag-card">
    <a class="tag-photo" href="<?= e($detailUrl) ?>" aria-label="Buka detail laporan <?= e($r['title']) ?>">
        <img src="<?= e($imageSrc) ?>" alt="<?= e($altText) ?>" loading="lazy" width="800" height="600" />
        <span class="tag-code"><?= e(report_code((string) $r['type'], (int) $r['id'])) ?></span>
    </a>
    <div class="tag-body">
        <div class="tag-badges">
            <span class="badge badge--<?= e($r['type']) ?>"><?= e(type_label_short((string) $r['type'])) ?></span>
            <span class="badge badge--<?= e(status_tone((string) $r['status'])) ?>"><?= e(status_label((string) $r['status'])) ?></span>
        </div>
        <h3 class="tag-title"><a href="<?= e($detailUrl) ?>"><?= e($r['title']) ?></a></h3>
        <div class="tag-meta">
            <span><span class="meta-kicker"><?= e(CATEGORIES[$r['category']] ?? $r['category']) ?></span></span>
            <span>Lokasi: <?= e($r['location']) ?></span>
            <?php if (($r['type'] ?? '') === 'ditemukan' && !empty($r['storage_location'])) : ?>
                <span class="tag-storage">Disimpan: <?= e($r['storage_location']) ?></span>
            <?php endif; ?>
            <span class="tag-date"><?= e(format_tanggal($r['date'])) ?></span>
        </div>
        <?php if ($showActions) : ?>
            <div class="tag-owner-actions">
                <a class="btn btn--ghost btn--sm" href="<?= e($detailUrl) ?>">Kelola</a>
                <a class="btn btn--ghost btn--sm" href="laporan_edit.php?id=<?= (int) $r['id'] ?>">Edit</a>
            </div>
        <?php endif; ?>
    </div>
</article>
