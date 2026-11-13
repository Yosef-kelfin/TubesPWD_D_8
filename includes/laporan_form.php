<?php
declare(strict_types=1);

/**
 * Partial form laporan (dipakai laporan_buat.php & laporan_edit.php).
 * Variabel: $form, $errors, $is_edit, $form_action, $current_image, $cancel_url.
 */
$isEdit      = !empty($is_edit);
$currentImage = isset($current_image) && $current_image !== null && $current_image !== ''
    ? (string) $current_image
    : '';
$cancelUrl   = isset($cancel_url) ? (string) $cancel_url : 'index.php';
$errorCount  = count($errors);
?>
<?php if ($errorCount > 0) : ?>
    <div class="form-errors" role="alert">
        <strong>Periksa <?= (int) $errorCount ?> kolom yang ditandai.</strong>
        Perubahan belum disimpan.
    </div>
<?php endif; ?>

<form method="post" action="<?= e($form_action) ?>" enctype="multipart/form-data" novalidate="novalidate">
    <?= csrf_field() ?>

    <div class="form-grid">
        <fieldset class="field field--full">
            <legend>Jenis laporan</legend>
            <div class="type-choice">
                <label class="type-card">
                    <input type="radio" name="type" value="hilang"<?= $form['type'] === 'hilang' ? ' checked="checked"' : '' ?> />
                    <span class="type-card-body">
                        <span class="type-card-title">Barang hilang</span>
                        <span class="type-card-desc">Saya kehilangan barang dan mencari informasinya.</span>
                    </span>
                </label>
                <label class="type-card">
                    <input type="radio" name="type" value="ditemukan"<?= $form['type'] === 'ditemukan' ? ' checked="checked"' : '' ?> />
                    <span class="type-card-body">
                        <span class="type-card-title">Barang ditemukan</span>
                        <span class="type-card-desc">Saya menemukan barang dan menyimpannya.</span>
                    </span>
                </label>
            </div>
            <?php if (isset($errors['type'])) : ?>
                <p class="field-error"><?= e($errors['type']) ?></p>
            <?php endif; ?>
        </fieldset>

        <div class="field field--full">
            <label for="title">Nama barang</label>
            <input type="text" id="title" name="title" value="<?= e($form['title']) ?>" maxlength="120" required="required"
                placeholder="Contoh: Tumbler Tupperware hitam"<?= isset($errors['title']) ? ' aria-invalid="true"' : '' ?> />
            <?php if (isset($errors['title'])) : ?>
                <p class="field-error"><?= e($errors['title']) ?></p>
            <?php else : ?>
                <p class="field-help">Sebutkan merek, warna, atau ciri yang mudah dikenali.</p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="category">Kategori</label>
            <select id="category" name="category" required="required"<?= isset($errors['category']) ? ' aria-invalid="true"' : '' ?>>
                <option value="">Pilih kategori</option>
                <?php foreach (CATEGORIES as $key => $label) : ?>
                    <option value="<?= e($key) ?>"<?= $form['category'] === $key ? ' selected="selected"' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category'])) : ?>
                <p class="field-error"><?= e($errors['category']) ?></p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="date">Tanggal hilang / ditemukan</label>
            <input type="date" id="date" name="date" value="<?= e($form['date']) ?>" required="required"<?= isset($errors['date']) ? ' aria-invalid="true"' : '' ?> />
            <?php if (isset($errors['date'])) : ?>
                <p class="field-error"><?= e($errors['date']) ?></p>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="location">Lokasi hilang / ditemukan</label>
            <input type="text" id="location" name="location" value="<?= e($form['location']) ?>" maxlength="150" required="required"
                list="lokasi-kampus" placeholder="Contoh: Kantin Gedung Thomas Aquinas"<?= isset($errors['location']) ? ' aria-invalid="true"' : '' ?> />
            <datalist id="lokasi-kampus">
                <?php foreach (CAMPUS_LOCATIONS as $place) : ?>
                    <option value="<?= e($place) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <?php if (isset($errors['location'])) : ?>
                <p class="field-error"><?= e($errors['location']) ?></p>
            <?php endif; ?>
        </div>

        <div class="field field--full" data-storage-wrap="1"<?= $form['type'] === 'ditemukan' ? '' : ' hidden="hidden"' ?>>
            <label for="storage_location">Lokasi penyimpanan barang</label>
            <input type="text" id="storage_location" name="storage_location" value="<?= e($form['storage_location']) ?>" maxlength="150"
                list="lokasi-penyimpanan" placeholder="Contoh: Diserahkan ke CS Gedung Bonaventura"
                <?= $form['type'] === 'ditemukan' ? 'required="required"' : '' ?><?= isset($errors['storage_location']) ? ' aria-invalid="true"' : '' ?> />
            <datalist id="lokasi-penyimpanan">
                <option value="Dibawa penemu"></option>
                <option value="Diserahkan ke CS"></option>
                <option value="Pos Satpam Kampus 2"></option>
                <option value="Resepsionis Gedung Bonaventura"></option>
            </datalist>
            <p class="field-help">Wajib untuk barang ditemukan. Jelaskan apakah barang dibawa penemu atau diserahkan ke CS beserta lokasinya.</p>
            <?php if (isset($errors['storage_location'])) : ?>
                <p class="field-error"><?= e($errors['storage_location']) ?></p>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" maxlength="2000" rows="5"
                placeholder="Ciri-ciri barang, isi tas, atau informasi lain yang membantu."<?= isset($errors['description']) ? ' aria-invalid="true"' : '' ?>><?= e($form['description']) ?></textarea>
            <p class="field-help">Maksimal 2000 karakter. Hindari menulis data sensitif seperti nomor KTP atau PIN.</p>
            <?php if (isset($errors['description'])) : ?>
                <p class="field-error"><?= e($errors['description']) ?></p>
            <?php endif; ?>
        </div>

        <div class="field field--full">
            <label for="image">Foto barang (opsional)</label>
            <div class="upload-box">
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" data-preview="photo-preview" />
                <p class="field-help">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
                <?php if ($isEdit && $currentImage !== '') : ?>
                    <div class="upload-current">
                        <img src="<?= e(UPLOAD_URL . '/' . rawurlencode(basename($currentImage))) ?>" alt="Foto laporan saat ini" width="74" height="56" />
                        <span>Foto saat ini</span>
                        <label class="check-line">
                            <input type="checkbox" name="hapus_foto" value="1" />
                            Hapus foto ini
                        </label>
                    </div>
                <?php endif; ?>
            </div>
            <p class="field-error" data-file-error="1" hidden="hidden"><span data-file-error-text="1"></span></p>
            <?php if (isset($errors['image'])) : ?>
                <p class="field-error"><?= e($errors['image']) ?></p>
            <?php endif; ?>
            <figure class="photo-preview" id="photo-preview" data-preview-box="1" hidden="hidden">
                <img src="<?= e(asset('assets/img/placeholder.svg')) ?>" alt="Pratinjau foto barang yang akan diunggah" />
                <figcaption>Pratinjau foto baru</figcaption>
            </figure>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn--signal" type="submit"><?= $isEdit ? 'Simpan perubahan' : 'Simpan laporan' ?></button>
        <a class="btn btn--ghost form-cancel" href="<?= e($cancelUrl) ?>">Batal</a>
        <?php if ($isEdit) : ?>
            <span class="field-help">Mengubah jenis laporan akan mengatur ulang statusnya.</span>
        <?php endif; ?>
    </div>
</form>
