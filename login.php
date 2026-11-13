<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$email  = '';
$next   = get('next');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_protect();

    $email    = post('email');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    $next     = post('next');

    if ($email === '' || $password === '') {
        $errors['login'] = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['login'] = 'Email atau password salah.';
    } else {
        $user = store_user_by_email($email);

        if ($user !== null && password_verify($password, (string) $user['password'])) {
            login_user($user);
            flash('success', 'Selamat datang kembali, ' . $user['name'] . '.');
            redirect(safe_next_url($next));
        }

        $errors['login'] = 'Email atau password salah.';
    }
}

$page_title = 'Masuk';
$active     = '';
require __DIR__ . '/includes/header.php';
?>

<div class="wrap auth-wrap">
    <div class="auth-grid">
        <aside class="auth-aside">
            <p class="plate">Papan temuan kampus</p>
            <h1>Satu akun untuk mencari dan melaporkan barang.</h1>
            <p>Masuk untuk menghubungi pelapor lewat Microsoft Teams, mengelola laporanmu, dan memantau status barang.</p>
            <ul class="auth-points">
                <li>Hubungi pelapor lewat Microsoft Teams.</li>
                <li>Kelola laporan milikmu sendiri.</li>
                <li>Pantau status barang yang kamu laporkan.</li>
            </ul>
        </aside>

        <div class="auth-box">
            <h2>Masuk</h2>
            <p class="auth-sub">Belum punya akun? <a href="register.php">Daftar sekarang</a>.</p>

            <?php if (isset($errors['login'])) : ?>
                <div class="form-errors" role="alert">
                    <strong>Gagal masuk.</strong>
                    <?= e($errors['login']) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php" novalidate="novalidate">
                <?= csrf_field() ?>
                <?php if ($next !== '') : ?>
                    <input type="hidden" name="next" value="<?= e($next) ?>" />
                <?php endif; ?>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required="required" />
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required="required" />
                </div>

                <button class="btn btn--signal btn--block" type="submit">Masuk</button>
            </form>

            <p class="demo-note">Akun demo: <code>kelvin@uajy.ac.id</code> atau <code>cs@uajy.ac.id</code>, password <code>password</code>.</p>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
