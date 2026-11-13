<?php
declare(strict_types=1);

$login_next  = $login_next ?? 'index.php';
$login_title = $login_title ?? 'Masuk untuk menghubungi pelapor';
$login_body  = $login_body ?? 'Kontak Microsoft Teams hanya bisa dibuka setelah kamu masuk.';
?>
<dialog class="login-modal" id="login-modal" aria-labelledby="login-modal-title">
    <div class="login-modal-inner">
        <p class="plate">Akses terkunci</p>
        <h2 id="login-modal-title"><?= e($login_title) ?></h2>
        <p class="login-modal-text"><?= e($login_body) ?></p>
        <div class="login-modal-actions">
            <a class="btn btn--signal" href="login.php?next=<?= e(rawurlencode((string) $login_next)) ?>">Masuk ke akun</a>
            <a class="btn btn--ghost" href="register.php">Daftar akun</a>
            <form method="dialog">
                <button class="btn btn--ghost" type="submit">Nanti saja</button>
            </form>
        </div>
    </div>
</dialog>