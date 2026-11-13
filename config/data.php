<?php
declare(strict_types=1);

/**
 * Lapisan data demo FindIt! (tanpa MySQL).
 *
 * Data disimpan sebagai JSON di data/findit.json dan di-seed otomatis
 * dari data demo bawaan. Hapus file JSON tersebut untuk mengembalikan
 * data demo ke kondisi awal.
 */

date_default_timezone_set('Asia/Jakarta');

const DATA_DIR     = __DIR__ . '/../data';
const DATA_FILE    = DATA_DIR . '/findit.json';
const DATA_VERSION = 1;

/** ------------------------------------------------------------------
 *  Data demo awal
 *  ------------------------------------------------------------------ */
function demo_users(): array
{
    // Hash bcrypt untuk password "password".
    $hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    return [
        [
            'id'         => 1,
            'email'      => 'kelvin@uajy.ac.id',
            'name'       => 'Kelvin Utama',
            'password'   => $hash,
            'teams'      => 'https://teams.microsoft.com/l/chat/0/0?users=kelvin@uajy.ac.id',
            'created_at' => '2026-10-02 09:14:00',
        ],
        [
            'id'         => 2,
            'email'      => 'cs@uajy.ac.id',
            'name'       => 'Petugas Cleaning Service',
            'password'   => $hash,
            'teams'      => 'https://teams.microsoft.com/l/chat/0/0?users=cs@uajy.ac.id',
            'created_at' => '2026-10-02 09:20:00',
        ],
        [
            'id'         => 3,
            'email'      => 'nadia.putri@students.uajy.ac.id',
            'name'       => 'Nadia Putri',
            'password'   => $hash,
            'teams'      => 'nadia.putri@students.uajy.ac.id',
            'created_at' => '2026-10-08 13:42:00',
        ],
    ];
}

function demo_reports(): array
{
    return [
        [
            'id'               => 1,
            'pengguna_id'     => 1,
            'type'            => 'hilang',
            'title'           => 'Tumbler Tupperware Hitam',
            'category'        => 'tumbler',
            'location'        => 'Kantin Gedung Thomas Aquinas',
            'date'            => '2026-11-10',
            'description'     => 'Tumbler Tupperware 1 liter warna hitam doff. Ada stiker UAJY di bagian depan. Terakhir terlihat setelah makan siang.',
            'image'           => null,
            'storage_location' => null,
            'status'          => 'masih_dicari',
            'created_at'      => '2026-11-10 13:05:00',
            'updated_at'      => '2026-11-10 13:05:00',
        ],
        [
            'id'               => 2,
            'pengguna_id'     => 2,
            'type'            => 'ditemukan',
            'title'           => 'Jaket Hoodie Grey',
            'category'        => 'pakaian_jaket',
            'location'        => 'Parkiran Basement Kampus 2',
            'date'            => '2026-11-09',
            'description'     => 'Hoodie abu-abu polos ukuran L tanpa sablon. Ditemukan tergantung di pagar basement.',
            'image'           => null,
            'storage_location' => 'Pos Satpam Kampus 2',
            'status'          => 'belum_dikembalikan',
            'created_at'      => '2026-11-09 17:22:00',
            'updated_at'      => '2026-11-09 17:22:00',
        ],
        [
            'id'               => 3,
            'pengguna_id'     => 2,
            'type'            => 'ditemukan',
            'title'           => 'AirPods Case Putih',
            'category'        => 'elektronik',
            'location'        => 'Ruang 3203 Gedung Bonaventura',
            'date'            => '2026-11-08',
            'description'     => 'Case AirPods putih tanpa earbuds. Ada goresan kecil di sudut kanan.',
            'image'           => null,
            'storage_location' => 'Resepsionis Gedung Bonaventura',
            'status'          => 'belum_dikembalikan',
            'created_at'      => '2026-11-08 11:40:00',
            'updated_at'      => '2026-11-08 11:40:00',
        ],
        [
            'id'               => 4,
            'pengguna_id'     => 3,
            'type'            => 'hilang',
            'title'           => 'Dompet Coklat Berisi KTM',
            'category'        => 'dompet_kartu',
            'location'        => 'Perpustakaan Lantai 2',
            'date'            => '2026-11-07',
            'description'     => 'Dompet kulit coklat tua. Berisi KTM dan kartu perpustakaan. Mohon hubungi kalau menemukan.',
            'image'           => null,
            'storage_location' => null,
            'status'          => 'masih_dicari',
            'created_at'      => '2026-11-07 15:10:00',
            'updated_at'      => '2026-11-07 15:10:00',
        ],
        [
            'id'               => 5,
            'pengguna_id'     => 3,
            'type'            => 'ditemukan',
            'title'           => 'Kunci Motor Honda',
            'category'        => 'kunci',
            'location'        => 'Parkiran Gedung E',
            'date'            => '2026-11-06',
            'description'     => 'Kunci motor Honda dengan gantungan kain biru. Ditemukan di dekat pintu parkiran.',
            'image'           => null,
            'storage_location' => 'Dibawa penemu (hubungi via Teams)',
            'status'          => 'belum_dikembalikan',
            'created_at'      => '2026-11-06 18:05:00',
            'updated_at'      => '2026-11-06 18:05:00',
        ],
        [
            'id'               => 6,
            'pengguna_id'     => 3,
            'type'            => 'hilang',
            'title'           => 'Kalkulator Casio FX-991',
            'category'        => 'elektronik',
            'location'        => 'Gedung Agustinus Lantai 3',
            'date'            => '2026-11-05',
            'description'     => 'Kalkulator scientific Casio. Ada nama pemilik di bagian belakang.',
            'image'           => null,
            'storage_location' => null,
            'status'          => 'sudah_ditemukan',
            'created_at'      => '2026-11-05 09:30:00',
            'updated_at'      => '2026-11-06 10:12:00',
        ],
        [
            'id'               => 7,
            'pengguna_id'     => 2,
            'type'            => 'ditemukan',
            'title'           => 'Botol Minum Stainless',
            'category'        => 'tumbler',
            'location'        => 'Kantin Kampus 3',
            'date'            => '2026-11-04',
            'description'     => 'Botol stainless 600 ml warna silver dengan tutup hitam.',
            'image'           => null,
            'storage_location' => 'Dibawa penemu',
            'status'          => 'sudah_dikembalikan',
            'created_at'      => '2026-11-04 12:15:00',
            'updated_at'      => '2026-11-05 08:45:00',
        ],
        [
            'id'               => 8,
            'pengguna_id'     => 2,
            'type'            => 'ditemukan',
            'title'           => 'Payung Lipat Biru',
            'category'        => 'lainnya',
            'location'        => 'Lobi Gedung Thomas Aquinas',
            'date'            => '2026-11-03',
            'description'     => 'Payung lipat biru tua dengan gagang kayu.',
            'image'           => null,
            'storage_location' => 'Pos CS Gedung Thomas Aquinas',
            'status'          => 'belum_dikembalikan',
            'created_at'      => '2026-11-03 08:50:00',
            'updated_at'      => '2026-11-03 08:50:00',
        ],
        [
            'id'               => 9,
            'pengguna_id'     => 1,
            'type'            => 'hilang',
            'title'           => 'Tote Bag Kanvas',
            'category'        => 'lainnya',
            'location'        => 'Halte Kampus 1',
            'date'            => '2026-11-02',
            'description'     => 'Tote bag kanvas krem bertuliskan nama depan. Berisi buku catatan dan alat tulis.',
            'image'           => null,
            'storage_location' => null,
            'status'          => 'masih_dicari',
            'created_at'      => '2026-11-02 16:40:00',
            'updated_at'      => '2026-11-02 16:40:00',
        ],
        [
            'id'               => 10,
            'pengguna_id'     => 3,
            'type'            => 'ditemukan',
            'title'           => 'Flashdisk 32GB',
            'category'        => 'elektronik',
            'location'        => 'Lab Komputer 2',
            'date'            => '2026-11-01',
            'description'     => 'Flashdisk hitam 32GB tanpa merek. Ditemukan menempel di depan CPU.',
            'image'           => null,
            'storage_location' => 'Laboran Lab Komputer 2',
            'status'          => 'sudah_dikembalikan',
            'created_at'      => '2026-11-01 10:20:00',
            'updated_at'      => '2026-11-02 09:05:00',
        ],
    ];
}

/** ------------------------------------------------------------------
 *  Baca / tulis penyimpanan JSON
 *  ------------------------------------------------------------------ */
function store_seed(): array
{
    $users   = demo_users();
    $reports = demo_reports();

    return [
        'version'        => DATA_VERSION,
        'next_user_id'   => ((int) max(array_column($users, 'id'))) + 1,
        'next_report_id' => ((int) max(array_column($reports, 'id'))) + 1,
        'users'          => $users,
        'reports'        => $reports,
    ];
}

function store_read(): array
{
    if (is_file(DATA_FILE)) {
        $raw     = @file_get_contents(DATA_FILE);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;

        if (
            is_array($decoded)
            && (int) ($decoded['version'] ?? 0) === DATA_VERSION
            && isset($decoded['users'], $decoded['reports'])
            && is_array($decoded['users'])
            && is_array($decoded['reports'])
        ) {
            return [
                'version'        => DATA_VERSION,
                'next_user_id'   => max(1, (int) ($decoded['next_user_id'] ?? 1)),
                'next_report_id' => max(1, (int) ($decoded['next_report_id'] ?? 1)),
                'users'          => array_values($decoded['users']),
                'reports'        => array_values($decoded['reports']),
            ];
        }
    }

    $seed = store_seed();
    store_write($seed);
    return $seed;
}

function store_write(array $data): void
{
    if (!is_dir(DATA_DIR) && !@mkdir(DATA_DIR, 0775, true) && !is_dir(DATA_DIR)) {
        return;
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return;
    }

    @file_put_contents(DATA_FILE, $json . PHP_EOL, LOCK_EX);
}

/**
 * Cache data per request. Panggil tanpa argumen untuk membaca,
 * atau dengan $replace untuk menyimpan state baru di cache.
 */
function store_state(?array $replace = null): array
{
    static $data = null;

    if ($replace !== null) {
        $data = $replace;
        return $data;
    }

    if ($data === null) {
        $data = store_read();
    }

    return $data;
}

/** ------------------------------------------------------------------
 *  Pengguna
 *  ------------------------------------------------------------------ */
function store_user(int $id): ?array
{
    foreach (store_state()['users'] as $user) {
        if ((int) ($user['id'] ?? 0) === $id) {
            return $user;
        }
    }
    return null;
}

function store_user_by_email(string $email): ?array
{
    $needle = mb_strtolower(trim($email));

    foreach (store_state()['users'] as $user) {
        if (mb_strtolower((string) ($user['email'] ?? '')) === $needle) {
            return $user;
        }
    }
    return null;
}

function store_create_user(array $fields): array
{
    $data = store_state();
    $id   = max(1, (int) $data['next_user_id']);

    $user = [
        'id'         => $id,
        'email'      => (string) ($fields['email'] ?? ''),
        'name'       => (string) ($fields['name'] ?? ''),
        'password'   => (string) ($fields['password'] ?? ''),
        'teams'      => (string) ($fields['teams'] ?? ''),
        'created_at' => (string) ($fields['created_at'] ?? date('Y-m-d H:i:s')),
    ];

    $data['users'][]     = $user;
    $data['next_user_id'] = $id + 1;
    store_state($data);
    store_write($data);

    return $user;
}

function store_update_user(int $id, array $fields): bool
{
    $data    = store_state();
    $changed = false;

    foreach ($data['users'] as $index => $user) {
        if ((int) ($user['id'] ?? 0) !== $id) {
            continue;
        }

        foreach (['email', 'name', 'password', 'teams'] as $key) {
            if (array_key_exists($key, $fields)) {
                $data['users'][$index][$key] = (string) $fields[$key];
            }
        }

        $changed = true;
        break;
    }

    if ($changed) {
        store_state($data);
        store_write($data);
    }

    return $changed;
}

/** ------------------------------------------------------------------
 *  Laporan
 *  ------------------------------------------------------------------ */
function store_report(int $id): ?array
{
    foreach (store_state()['reports'] as $report) {
        if ((int) ($report['id'] ?? 0) === $id) {
            return $report;
        }
    }
    return null;
}

function store_report_with_owner(int $id): ?array
{
    $report = store_report($id);
    if ($report === null) {
        return null;
    }

    $owner = store_user((int) ($report['pengguna_id'] ?? 0));
    $report['owner_name']  = $owner['name'] ?? 'Pengguna FindIt!';
    $report['owner_email'] = $owner['email'] ?? '';
    $report['owner_teams'] = $owner['teams'] ?? '';

    return $report;
}

function store_create_report(array $fields): int
{
    $data = store_state();
    $id   = max(1, (int) $data['next_report_id']);
    $now  = date('Y-m-d H:i:s');

    $report = [
        'id'               => $id,
        'pengguna_id'     => (int) ($fields['pengguna_id'] ?? 0),
        'type'            => (string) ($fields['type'] ?? 'hilang'),
        'title'           => (string) ($fields['title'] ?? ''),
        'category'        => (string) ($fields['category'] ?? ''),
        'location'        => (string) ($fields['location'] ?? ''),
        'date'            => (string) ($fields['date'] ?? date('Y-m-d')),
        'description'     => $fields['description'] ?? null,
        'image'           => $fields['image'] ?? null,
        'storage_location' => $fields['storage_location'] ?? null,
        'status'          => (string) ($fields['status'] ?? 'masih_dicari'),
        'created_at'      => (string) ($fields['created_at'] ?? $now),
        'updated_at'      => (string) ($fields['updated_at'] ?? $now),
    ];

    $data['reports'][]     = $report;
    $data['next_report_id'] = $id + 1;
    store_state($data);
    store_write($data);

    return $id;
}

function store_update_report(int $id, array $fields): bool
{
    $data    = store_state();
    $changed = false;
    $allowed = [
        'type', 'title', 'category', 'location', 'date',
        'description', 'image', 'storage_location', 'status',
    ];

    foreach ($data['reports'] as $index => $report) {
        if ((int) ($report['id'] ?? 0) !== $id) {
            continue;
        }

        foreach ($allowed as $key) {
            if (array_key_exists($key, $fields)) {
                $data['reports'][$index][$key] = $fields[$key];
            }
        }

        $data['reports'][$index]['updated_at'] = date('Y-m-d H:i:s');
        $changed = true;
        break;
    }

    if ($changed) {
        store_state($data);
        store_write($data);
    }

    return $changed;
}

function store_delete_report(int $id): bool
{
    $data    = store_state();
    $kept    = [];
    $removed = false;

    foreach ($data['reports'] as $report) {
        if ((int) ($report['id'] ?? 0) === $id) {
            $removed = true;
            continue;
        }
        $kept[] = $report;
    }

    if ($removed) {
        $data['reports'] = $kept;
        store_state($data);
        store_write($data);
    }

    return $removed;
}

/** ------------------------------------------------------------------
 *  Query laporan (filter, urutan, paginasi)
 *  ------------------------------------------------------------------ */
function store_matches_filters(array $report, array $filters, ?int $userId): bool
{
    if ($userId !== null && (int) ($report['pengguna_id'] ?? 0) !== $userId) {
        return false;
    }

    $keyword = trim((string) ($filters['q'] ?? ''));
    if ($keyword !== '') {
        $haystack = mb_strtolower((string) ($report['title'] ?? '') . ' ' . (string) ($report['description'] ?? ''));
        if (mb_strpos($haystack, mb_strtolower($keyword)) === false) {
            return false;
        }
    }

    if (($filters['kategori'] ?? '') !== '' && (string) ($report['category'] ?? '') !== $filters['kategori']) {
        return false;
    }

    if (($filters['jenis'] ?? '') !== '' && (string) ($report['type'] ?? '') !== $filters['jenis']) {
        return false;
    }

    if (($filters['status'] ?? '') !== '' && (string) ($report['status'] ?? '') !== $filters['status']) {
        return false;
    }

    $location = trim((string) ($filters['lokasi'] ?? ''));
    if (
        $location !== ''
        && mb_strpos(mb_strtolower((string) ($report['location'] ?? '')), mb_strtolower($location)) === false
    ) {
        return false;
    }

    return true;
}

function store_filter_reports(array $filters = [], ?int $userId = null): array
{
    $reports = [];

    foreach (store_state()['reports'] as $report) {
        if (is_array($report) && store_matches_filters($report, $filters, $userId)) {
            $reports[] = $report;
        }
    }

    usort($reports, static function (array $a, array $b): int {
        $byDate = strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
        if ($byDate !== 0) {
            return $byDate;
        }
        return (int) ($b['id'] ?? 0) <=> (int) ($a['id'] ?? 0);
    });

    return $reports;
}

function store_reports_for(array $filters = [], ?int $userId = null, ?int $limit = null, int $offset = 0): array
{
    $reports = store_filter_reports($filters, $userId);

    if ($limit === null) {
        return array_slice($reports, $offset);
    }

    return array_slice($reports, $offset, $limit);
}

function store_count_reports(array $filters = [], ?int $userId = null): int
{
    return count(store_filter_reports($filters, $userId));
}

/**
 * Statistik ringkas. Tanpa $userId dihitung dari semua laporan.
 * Kunci: total, hilang, ditemukan, selesai, pending.
 */
function store_stats(?int $userId = null): array
{
    $stats = [
        'total'     => 0,
        'hilang'    => 0,
        'ditemukan' => 0,
        'selesai'   => 0,
        'pending'   => 0,
    ];

    foreach (store_state()['reports'] as $report) {
        if (!is_array($report)) {
            continue;
        }
        if ($userId !== null && (int) ($report['pengguna_id'] ?? 0) !== $userId) {
            continue;
        }

        $stats['total']++;

        if (($report['type'] ?? '') === 'hilang') {
            $stats['hilang']++;
        }
        if (($report['type'] ?? '') === 'ditemukan') {
            $stats['ditemukan']++;
        }
        if (in_array((string) ($report['status'] ?? ''), ['sudah_ditemukan', 'sudah_dikembalikan'], true)) {
            $stats['selesai']++;
        }
        if ((string) ($report['status'] ?? '') === 'belum_dikembalikan') {
            $stats['pending']++;
        }
    }

    return $stats;
}
