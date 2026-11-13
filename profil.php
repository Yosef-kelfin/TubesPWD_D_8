<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();
$userId = (int) $user['id'];

$identity = [
    'name'  => (string) $user['name'],
    'teams' => (string) $user['teams'],
];
$identityErrors = [];
$passwordErrors = [];

$reportCount = store_count_reports([], $userId);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    csrf_protect();

    $formType = post('form');

    if ($formType === 'identity') {
        $identity['name']  = post('name');
        $identity['teams'] = post('teams');

        $nameLength = mb_strlen($identity['name']);
        if ($nameLength < 2 || $nameLength > 100) {
            $identityErrors['name'] = 'Nama harus 2–100 karakter.';
        }

        $teamsLength = mb_strlen($identity['teams']);
        if ($teamsLength < 3 || $teamsLength > 150) {
            $identityErrors['teams'] = 'Identitas Teams harus 3–150 karakter.';
        } elseif (!preg_match('~^https?://~i', $identity['teams']) && !filter_var($identity['teams'], FILTER_VALIDATE_EMAIL)) {
            $identityErrors['teams'] = 'Isi email Teams atau tautan Teams yang valid.';
        }

        if ($identityErrors === []) {
            store_update_user($userId, [
                'name'  => $identity['name'],
                'teams' => $identity['teams'],
            ]);

            flash('success', 'Profil diperbarui.');
            redirect('profil.php');
        }
    } elseif ($formType === 'password') {
        $currentPassword = isset($_POST['current_password']) && is_string($_POST['current_password'])
            ? $_POST['current_password']
            : '';
        $newPassword     = isset($_POST['new_password']) && is_string($_POST['new_password'])
            ? $_POST['new_password']
            : '';
        $confirmPassword = isset($_POST['confirm_password']) && is_string($_POST['confirm_password'])
            ? $_POST['confirm_password']
            : '';

        $currentUser  = store_user($userId);
        $currentHash  = (string) ($currentUser['password'] ?? '');

        if ($currentPassword === '' || !password_verify($currentPassword, $currentHash)) {
            $passwordErrors['current_password'] = 'Password saat ini tidak cocok.';
        }
        if (mb_strlen($newPassword) < 8) {
            $passwordErrors['new_password'] = 'Password baru minimal 8 karakter.';
        } elseif (mb_strlen($newPassword) > 100) {
            $passwordErrors['new_password'] = 'Password baru maksimal 100 karakter.';
        }
        if ($confirmPassword !== $newPassword) {
            $passwordErrors['confirm_password'] = 'Konfirmasi password tidak sama.';
        }

        if ($passwordErrors === []) {
            store_update_user($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ]);

            flash('success', 'Password diganti. Gunakan password baru saat masuk berikutnya.');
            redirect('profil.php');
        }
    }
}

$page_title = 'Profil';
$active     = 'profil';
require __DIR__ . '/includes/header.php';
?>

<div class="wrap">
    <div class="page-head">
        <p class="plate">Akun saya</p>
        <h1>Profil</h1>
        <p class="lede">Nama dan kontak Teams dipakai pada laporan yang kamu buat. Email akun tidak dapat diubah.</p>
    </div>

    <div class="profile-grid">
        <section class="profile-card" aria-labelledby="identity-title">
            <h2 id="identity-title">Identitas</h2>
            <p class="card-sub">Kontak Teams dipakai untuk tombol "Hubungi via Teams" pada laporanmu.</p>

            <?php if ($identityErrors !== []) : ?>
                <div class="form-errors" role="alert">
                    <strong>Periksa <?= count($identityErrors) ?> kolom.</strong>
                    Perubahan belum disimpan.
                </div>
            <?php endif; ?>

            <form method="post" action="profil.php" novalidate="novalidate">
                <?= csrf_field() ?>
                <input type="hidden" name="form" value="identity" />

                <div class="field">
                    <label for="email-readonly">Email akun</label>
                    <input type="email" id="email-readonly" value="<?= e($user['email']) ?>" readonly="readonly" disabled="disabled" />
                    <p class="field-help">Email ini dipakai untuk masuk.</p>
                </div>

                <div class="field">
                    <label for="name">Nama lengkap</label>
                    <input type="text" id="name" name="name" value="<?= e($identity['name']) ?>" maxlength="100" autocomplete="name" required="required"<?= isset($identityErrors['name']) ? ' aria-invalid="true"' : '' ?> />
                    <?php if (isset($identityErrors['name'])) : ?>
                        <p class="field-error"><?= e($identityErrors['name']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="teams">Email atau tautan Microsoft Teams</label>
                    <input type="text" id="teams" name="teams" value="<?= e($identity['teams']) ?>" maxlength="150" required="required"<?= isset($identityErrors['teams']) ? ' aria-invalid="true"' : '' ?> />
                    <?php if (isset($identityErrors['teams'])) : ?>
                        <p class="field-error"><?= e($identityErrors['teams']) ?></p>
                    <?php endif; ?>
                </div>

                <button class="btn btn--signal" type="submit">Simpan profil</button>
            </form>

            <div class="profile-meta">
                <span><strong class="tnum"><?= (int) $reportCount ?></strong> laporan dibuat</span>
                <span><strong><?= e(format_tanggal((string) $user['created_at'])) ?></strong> bergabung sejak</span>
            </div>
        </section>

        <section class="profile-card" aria-labelledby="password-title">
            <h2 id="password-title">Kata sandi</h2>
            <p class="card-sub">Ganti password secara berkala. Minimal 8 karakter.</p>

            <?php if ($passwordErrors !== []) : ?>
                <div class="form-errors" role="alert">
                    <strong>Password belum diganti.</strong>
                    Periksa kolom yang ditandai.
                </div>
            <?php endif; ?>

            <form method="post" action="profil.php" novalidate="novalidate">
                <?= csrf_field() ?>
                <input type="hidden" name="form" value="password" />

                <div class="field">
                    <label for="current_password">Password saat ini</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password" required="required"<?= isset($passwordErrors['current_password']) ? ' aria-invalid="true"' : '' ?> />
                    <?php if (isset($passwordErrors['current_password'])) : ?>
                        <p class="field-error"><?= e($passwordErrors['current_password']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="new_password">Password baru</label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password" required="required"<?= isset($passwordErrors['new_password']) ? ' aria-invalid="true"' : '' ?> />
                    <?php if (isset($passwordErrors['new_password'])) : ?>
                        <p class="field-error"><?= e($passwordErrors['new_password']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="confirm_password">Ulangi password baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required="required"<?= isset($passwordErrors['confirm_password']) ? ' aria-invalid="true"' : '' ?> />
                    <?php if (isset($passwordErrors['confirm_password'])) : ?>
                        <p class="field-error"><?= e($passwordErrors['confirm_password']) ?></p>
                    <?php endif; ?>
                </div>

                <button class="btn btn--ink" type="submit">Ganti password</button>
            </form>
        </section>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
