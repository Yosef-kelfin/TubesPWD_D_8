<?php
declare(strict_types=1);

/** ------------------------------------------------------------------
 *  Identitas & pengaturan aplikasi
 *  ------------------------------------------------------------------ */
const APP_NAME        = 'FindIt!';
const APP_TAGLINE     = 'Papan Temuan Kampus UAJY';
/** Ganti ke 'application/xhtml+xml' bila ingin mode XHTML ketat. */
const APP_CONTENT_TYPE = 'text/html';
const UPLOAD_DIR      = __DIR__ . '/../uploads';
const UPLOAD_URL      = 'uploads';
const MAX_UPLOAD_BYTES = 2 * 1024 * 1024; // 2 MB
const PAGE_SIZE       = 12;

/** ------------------------------------------------------------------
 *  Data master
 *  ------------------------------------------------------------------ */
const CATEGORIES = [
    'tumbler'       => 'Tumbler',
    'pakaian_jaket' => 'Pakaian/Jaket',
    'elektronik'    => 'Barang Elektronik',
    'dompet_kartu'  => 'Dompet/Kartu',
    'kunci'         => 'Kunci',
    'lainnya'       => 'Lainnya',
];

const REPORT_TYPES = [
    'hilang'    => 'Barang Hilang',
    'ditemukan' => 'Barang Ditemukan',
];

const STATUS_BY_TYPE = [
    'hilang' => [
        'masih_dicari'    => 'Masih Dicari',
        'sudah_ditemukan' => 'Sudah Ditemukan',
    ],
    'ditemukan' => [
        'belum_dikembalikan' => 'Belum Dikembalikan',
        'sudah_dikembalikan' => 'Sudah Dikembalikan',
    ],
];

const ALL_STATUSES = [
    'masih_dicari'       => 'Masih Dicari',
    'sudah_ditemukan'    => 'Sudah Ditemukan',
    'belum_dikembalikan' => 'Belum Dikembalikan',
    'sudah_dikembalikan' => 'Sudah Dikembalikan',
];

const CAMPUS_LOCATIONS = [
    'Kantin Gedung Thomas Aquinas',
    'Kantin Kampus 3',
    'Perpustakaan Lantai 2',
    'Parkiran Basement Kampus 2',
    'Parkiran Gedung E',
    'Lobi Gedung Bonaventura',
    'Ruang 3203 Gedung Bonaventura',
    'Gedung Agustinus Lantai 3',
    'Lab Komputer 2',
    'Halte Kampus 1',
];

const BULAN_ID = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];

/** ------------------------------------------------------------------
 *  Helper dasar
 *  ------------------------------------------------------------------ */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function get(string $key, string $default = ''): string
{
    $value = $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function query_string(array $params): string
{
    $clean = [];
    foreach ($params as $key => $value) {
        if ($value !== '' && $value !== null) {
            $clean[$key] = $value;
        }
    }
    return $clean ? '?' . http_build_query($clean) : '';
}

function format_tanggal(?string $date): string
{
    if ($date === null || $date === '') {
        return '—';
    }
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '—';
    }
    return (int) date('j', $timestamp) . ' '
        . BULAN_ID[(int) date('n', $timestamp)] . ' '
        . date('Y', $timestamp);
}

function report_code(string $type, int $id): string
{
    $prefix = $type === 'ditemukan' ? 'TMU' : 'HIL';
    return sprintf('%s-%04d', $prefix, $id);
}

function statuses_for_type(string $type): array
{
    return STATUS_BY_TYPE[$type] ?? [];
}

function default_status(string $type): string
{
    return $type === 'ditemukan' ? 'belum_dikembalikan' : 'masih_dicari';
}

function status_label(string $status): string
{
    return ALL_STATUSES[$status] ?? $status;
}

function type_label_short(string $type): string
{
    return $type === 'ditemukan' ? 'Ditemukan' : 'Hilang';
}

function status_tone(string $status): string
{
    return match ($status) {
        'masih_dicari'       => 'lost',
        'belum_dikembalikan' => 'pending',
        'sudah_ditemukan',
        'sudah_dikembalikan' => 'done',
        default              => 'pending',
    };
}

function image_url(?string $image, string $category): string
{
    if ($image !== null && $image !== '' && is_file(UPLOAD_DIR . '/' . basename($image))) {
        return UPLOAD_URL . '/' . rawurlencode(basename($image));
    }
    return placeholder_for($category);
}

function placeholder_for(string $category): string
{
    $slug = isset(CATEGORIES[$category]) ? $category : 'lainnya';
    return 'assets/img/ph-' . $slug . '.svg';
}

/** ------------------------------------------------------------------
 *  Flash message
 *  ------------------------------------------------------------------ */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return is_array($items) ? $items : [];
}

/** ------------------------------------------------------------------
 *  Halaman error (403 / 404 / 400)
 *  ------------------------------------------------------------------ */
function abort_page(int $code, string $title, string $message): never
{
    http_response_code($code);
    $page_title = $code . ' · ' . $title;
    $active     = '';
    require __DIR__ . '/header.php';
    ?>
    <div class="wrap error-wrap">
        <div class="error-sheet">
            <p class="plate">Kode <?= (int) $code ?></p>
            <h1><?= e($title) ?></h1>
            <p class="error-message"><?= e($message) ?></p>
            <div class="error-actions">
                <a class="btn btn--signal" href="index.php">Kembali ke beranda</a>
                <?php if (is_logged_in()) : ?>
                    <a class="btn btn--ghost" href="laporan_saya.php">Laporan saya</a>
                <?php else : ?>
                    <a class="btn btn--ghost" href="login.php">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    require __DIR__ . '/footer.php';
    exit;
}

/** ------------------------------------------------------------------
 *  Validasi laporan
 *  ------------------------------------------------------------------ */
function read_report_input(): array
{
    return [
        'type'             => post('type'),
        'title'            => post('title'),
        'category'         => post('category'),
        'location'         => post('location'),
        'date'             => post('date'),
        'description'      => post('description'),
        'storage_location' => post('storage_location'),
    ];
}

function validate_report(array $input): array
{
    $errors = [];

    if (!isset(REPORT_TYPES[$input['type']])) {
        $errors['type'] = 'Pilih jenis laporan.';
    }

    $titleLength = mb_strlen($input['title']);
    if ($titleLength < 3 || $titleLength > 120) {
        $errors['title'] = 'Nama barang harus 3–120 karakter.';
    }

    if (!isset(CATEGORIES[$input['category']])) {
        $errors['category'] = 'Pilih kategori barang.';
    }

    $locationLength = mb_strlen($input['location']);
    if ($locationLength < 3 || $locationLength > 150) {
        $errors['location'] = 'Lokasi harus 3–150 karakter.';
    }

    $date = DateTime::createFromFormat('!Y-m-d', $input['date']);
    if (!$date || $date->format('Y-m-d') !== $input['date']) {
        $errors['date'] = 'Tanggal tidak valid.';
    }

    if (mb_strlen($input['description']) > 2000) {
        $errors['description'] = 'Deskripsi maksimal 2000 karakter.';
    }

    if ($input['type'] === 'ditemukan') {
        $storageLength = mb_strlen($input['storage_location']);
        if ($storageLength < 3 || $storageLength > 150) {
            $errors['storage_location'] = 'Lokasi penyimpanan wajib diisi (3–150 karakter).';
        }
    }

    return $errors;
}

/** ------------------------------------------------------------------
 *  Upload foto
 *  ------------------------------------------------------------------ */
function upload_photo(array $file, array &$errors): ?string
{
    $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($errorCode === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($errorCode !== UPLOAD_ERR_OK) {
        $errors['image'] = 'Upload foto gagal. Coba lagi dengan file lain.';
        return null;
    }
    if (($file['size'] ?? 0) > MAX_UPLOAD_BYTES) {
        $errors['image'] = 'Ukuran foto maksimal 2 MB.';
        return null;
    }

    $tmp = (string) ($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        $errors['image'] = 'Berkas foto tidak valid.';
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = (string) $finfo->file($tmp);
    $map   = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($map[$mime])) {
        $errors['image'] = 'Format foto harus JPG, PNG, atau WEBP.';
        return null;
    }

    $info = @getimagesize($tmp);
    if ($info === false || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
        $errors['image'] = 'Berkas yang diunggah bukan gambar yang valid.';
        return null;
    }

    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0775, true) && !is_dir(UPLOAD_DIR)) {
        $errors['image'] = 'Folder upload tidak tersedia.';
        return null;
    }

    $filename = bin2hex(random_bytes(10)) . '.' . $map[$mime];
    if (!move_uploaded_file($tmp, UPLOAD_DIR . '/' . $filename)) {
        $errors['image'] = 'Foto gagal disimpan. Coba lagi.';
        return null;
    }

    return $filename;
}

function delete_photo(?string $filename): void
{
    if ($filename === null || $filename === '') {
        return;
    }
    $path = UPLOAD_DIR . '/' . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}

/** ------------------------------------------------------------------
 *  Kontak Teams
 *  ------------------------------------------------------------------ */
function teams_url(string $teams): string
{
    $value = trim($teams);
    if ($value === '') {
        return '';
    }
    if (preg_match('~^https?://~i', $value) === 1) {
        return $value;
    }
    return 'https://teams.microsoft.com/l/chat/0/0?users=' . rawurlencode($value);
}

/** ------------------------------------------------------------------
 *  Paginasi
 *  ------------------------------------------------------------------ */
function pager_numbers(int $current, int $total, int $radius = 2): array
{
    $start = max(1, $current - $radius);
    $end   = min($total, $current + $radius);
    return range($start, $end);
}

/** ------------------------------------------------------------------
 *  Cache-busting aset
 *  ------------------------------------------------------------------ */
function asset(string $path): string
{
    $full = __DIR__ . '/../' . $path;
    $version = is_file($full) ? (string) filemtime($full) : '1';
    return $path . '?v=' . $version;
}
