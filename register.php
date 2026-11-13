<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$form   = [
    'name'  => '',
    'email' => '',
    'teams' => '',
];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_protect();

    $form['name']  = post('name');
    $form['email'] = post('email');
    $form['teams'] = post('teams');

    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    $confirm  = isset($_POST['password_confirm']) && is_string($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    $nameLength = mb_strlen($form['name']);
    if ($nameLength < 2 || $nameLength > 100) {
        $errors['name'] = 'Nama harus 2–100 karakter.';
    }

    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    } elseif (mb_strlen($form['email']) > 100) {
        $errors['email'] = 'Email maksimal 100 karakter.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM pengguna WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $form['email']]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email sudah terdaftar.';
        }
    }

    $teamsLength = mb_strlen($form['teams']);
    if ($teamsLength < 3 || $teamsLength > 150) {
        $errors['teams'] = 'Identitas Teams harus 3–150 karakter.';
    } elseif (!preg_match('~^https?://~i', $form['teams']) && !filter_var($form['teams'], FILTER_VALIDATE_EMAIL)) {
        $errors['teams'] = 'Isi email Teams (mis. nama@uajy.ac.id) atau tautan Teams yang valid.';
    }

    if (mb_strlen($password) < 8) {
        $errors['password'] = 'Password minimal 8 karakter.';
    } elseif (mb_strlen($password) > 100) {
        $errors['password'] = 'Password maksimal 100 karakter.';
    }

    if ($confirm !== $password) {
        $errors['password_confirm'] = 'Konfirmasi password tidak sama.';
    }

    if ($errors === []) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO pengguna (email, name, password, teams)
                 VALUES (:email, :name, :password, :teams)'
            );
            $stmt->execute([
                ':email'    => $form['email'],
                ':name'     => $form['name'],
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':teams'    => $form['teams'],
            ]);

            flash('success', 'Akun dibuat. Masuk dengan email dan password barumu.');
            redirect('login.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors['email'] = 'Email sudah terdaftar.';
            } else {
                throw $e;
            }
        }
    }
}

$page_title = 'Daftar';
$active     = '';
require __DIR__ . '/includes/header.php';
?>

<div class="wrap auth-wrap">
    <div class="auth-grid">
        <aside class="auth-aside">
            <p class="plate">Bergabung · FindIt!</p>
            <h1>Barang ketemu? Catat di papan temuan.</h1>
            <p>Satu akun untuk melaporkan barang hilang maupun temuan. Tidak ada admin, semua laporan dibuat dan ditutup oleh pembuatnya.</p>
            <ul class="auth-points">
                <li>Laporan ditemukan mencantumkan lokasi penyimpanan.</li>
                <li>Kontak pelapor lewat Microsoft Teams.</li>
                <li>Laporan bisa diedit atau dihapus kapan saja.</li>
            </ul>
        </aside>

        <div class="auth-box">
            <h2>Buat akun</h2>
            <p class="auth-sub">Sudah punya akun? <a href="login.php">Masuk di sini</a>.</p>

            <?php if ($errors !== []) : ?>
                <div class="form-errors" role="alert">
                    <strong>Periksa <?= count($errors) ?> kolom yang ditandai.</strong>
                    Akun belum dibuat.
                </div>
            <?php endif; ?>

            <form method="post" action="register.php" novalidate="novalidate">
                <?= csrf_field() ?>

                <div class="form-grid">
                    <div class="field">
                        <label for="name">Nama lengkap</label>
                        <input type="text" id="name" name="name" value="<?= e($form['name']) ?>" maxlength="100" autocomplete="name" required="required"<?= isset($errors['name']) ? ' aria-invalid="true"' : '' ?> />
                        <?php if (isset($errors['name'])) : ?>
                            <p class="field-error"><?= e($errors['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= e($form['email']) ?>" maxlength="100" autocomplete="email" required="required"<?= isset($errors['email']) ? ' aria-invalid="true"' : '' ?> />
                        <?php if (isset($errors['email'])) : ?>
                            <p class="field-error"><?= e($errors['email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field field--full">
                        <label for="teams">Email atau tautan Microsoft Teams</label>
                        <input type="text" id="teams" name="teams" value="<?= e($form['teams']) ?>" maxlength="150" placeholder="nama@uajy.ac.id" required="required"<?= isset($errors['teams']) ? ' aria-invalid="true"' : '' ?> />
                        <p class="field-help">Dipakai untuk tombol "Hubungi via Teams" pada laporanmu. Bisa berupa email atau tautan chat Teams.</p>
                        <?php if (isset($errors['teams'])) : ?>
                            <p class="field-error"><?= e($errors['teams']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" required="required"<?= isset($errors['password']) ? ' aria-invalid="true"' : '' ?> />
                        <p class="field-help">Minimal 8 karakter.</p>
                        <?php if (isset($errors['password'])) : ?>
                            <p class="field-error"><?= e($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="password_confirm">Ulangi password</label>
                        <input type="password" id="password_confirm" name="password_confirm" autocomplete="new-password" required="required"<?= isset($errors['password_confirm']) ? ' aria-invalid="true"' : '' ?> />
                        <?php if (isset($errors['password_confirm'])) : ?>
                            <p class="field-error"><?= e($errors['password_confirm']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <button class="btn btn--signal btn--block" type="submit">Daftar</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
