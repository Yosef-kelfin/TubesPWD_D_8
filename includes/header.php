<?php
declare(strict_types=1);

if (!headers_sent()) {
    header('Content-Type: ' . APP_CONTENT_TYPE . '; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
}

$user        = current_user();
$page_title  = isset($page_title) ? (string) $page_title : '';
$active      = isset($active) ? (string) $active : '';
$body_class  = isset($body_class) ? (string) $body_class : '';
$flash_items = flashes();

$initials = '';
if ($user !== null) {
    foreach (preg_split('/\s+/', trim((string) $user['name'])) ?: [] as $part) {
        if ($part !== '') {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }
        if (mb_strlen($initials) >= 2) {
            break;
        }
    }
}

$nav_items = [
    ['href' => 'index.php',        'label' => 'Beranda',       'key' => 'home'],
    ['href' => 'laporan_buat.php', 'label' => 'Buat Laporan',  'key' => 'buat'],
    ['href' => 'laporan_saya.php', 'label' => 'Laporan Saya',  'key' => 'saya'],
    ['href' => 'profil.php',       'label' => 'Profil',        'key' => 'profil'],
];
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="id" xml:lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $page_title !== '' ? e($page_title) . ' · ' . e(APP_NAME) : e(APP_NAME) . ' — ' . e(APP_TAGLINE) ?></title>
    <meta name="description" content="Papan temuan kampus FindIt!: laporan barang hilang dan ditemukan di lingkungan kampus." />
    <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>" />
</head>
<body class="<?= e($body_class) ?>">
<a class="skip-link" href="#main">Lewati ke konten</a>

<header class="site-head">
    <div class="wrap head-inner">
        <a class="brand" href="index.php" aria-label="FindIt! beranda">
            <span class="brand-mark" aria-hidden="true">F!</span>
            <span class="brand-text">
                <span class="brand-name">FindIt!</span>
                <span class="brand-sub">Lost &amp; Found UAJY</span>
            </span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-menu">
            <span class="nav-toggle-bars" aria-hidden="true"><span></span><span></span></span>
            <span class="nav-toggle-text">Menu</span>
        </button>

        <div class="head-menu" id="site-menu">
            <nav class="nav" aria-label="Navigasi utama">
                <?php foreach ($nav_items as $item) : ?>
                    <?php if ($user === null && in_array($item['key'], ['buat', 'saya', 'profil'], true)) : ?>
                        <?php continue; ?>
                    <?php endif; ?>
                    <a href="<?= e($item['href']) ?>"<?= $active === $item['key'] ? ' class="is-active" aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
                <?php endforeach; ?>
            </nav>

            <div class="head-actions">
                <?php if ($user !== null) : ?>
                    <a class="user-chip" href="profil.php" title="Buka profil <?= e($user['name']) ?>">
                        <span class="user-initials" aria-hidden="true"><?= e($initials !== '' ? $initials : '?') ?></span>
                        <span class="user-name"><?= e($user['name']) ?></span>
                    </a>
                    <form class="logout-form" method="post" action="logout.php">
                        <?= csrf_field() ?>
                        <button class="btn btn--ghost btn--sm" type="submit">Keluar</button>
                    </form>
                <?php else : ?>
                    <a class="btn btn--ghost btn--sm" href="login.php">Masuk</a>
                    <a class="btn btn--signal btn--sm" href="register.php">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main id="main" class="site-main" tabindex="-1">
    <?php if ($flash_items !== []) : ?>
        <div class="wrap flash-stack">
            <?php foreach ($flash_items as $flash) : ?>
                <?php
                $flashType = in_array($flash['type'], ['success', 'error', 'info'], true) ? $flash['type'] : 'info';
                $flashLabel = $flashType === 'success' ? 'Berhasil.' : ($flashType === 'error' ? 'Gagal.' : 'Info.');
                ?>
                <div class="flash flash--<?= e($flashType) ?>" role="<?= $flashType === 'error' ? 'alert' : 'status' ?>">
                    <strong class="flash-label"><?= e($flashLabel) ?></strong>
                    <p class="flash-text"><?= e($flash['message']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
