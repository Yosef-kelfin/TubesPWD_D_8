-- =====================================================================
-- FindIt! — skema + data demo
-- Database : tubes_findit (sesuai config/database.php)
-- Password akun demo: password
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `tubes_findit` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `tubes_findit`;

-- ---------------------------------------------------------------------
-- Tabel pengguna
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pengguna` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(100) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `teams` VARCHAR(150) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Tabel laporan
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `laporan` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pengguna_id` INT UNSIGNED NOT NULL,
    `type` ENUM('hilang', 'ditemukan') NOT NULL,
    `title` VARCHAR(120) NOT NULL,
    `category` ENUM(
        'tumbler',
        'pakaian_jaket',
        'elektronik',
        'dompet_kartu',
        'kunci',
        'lainnya'
    ) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `date` DATE NOT NULL,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `storage_location` VARCHAR(150) NULL,
    `status` ENUM(
        'masih_dicari',
        'sudah_ditemukan',
        'belum_dikembalikan',
        'sudah_dikembalikan'
    ) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_laporan_pengguna` (`pengguna_id`),
    KEY `idx_type_category` (`type`, `category`),
    KEY `idx_status` (`status`),
    KEY `idx_date` (`date`),
    CONSTRAINT `fk_laporan_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- Reset data demo (tabel legacy users/items tidak disentuh)
-- ---------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `laporan`;

DELETE FROM `pengguna`;

ALTER TABLE `laporan` AUTO_INCREMENT = 1;

ALTER TABLE `pengguna` AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO
    `pengguna` (
        `id`,
        `email`,
        `name`,
        `password`,
        `teams`,
        `created_at`
    )
VALUES (
        1,
        'kelvin@uajy.ac.id',
        'Kelvin Utama',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'https://teams.microsoft.com/l/chat/0/0?users=kelvin@uajy.ac.id',
        '2026-10-02 09:14:00'
    ),
    (
        2,
        'cs@uajy.ac.id',
        'Petugas Cleaning Service',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'https://teams.microsoft.com/l/chat/0/0?users=cs@uajy.ac.id',
        '2026-10-02 09:20:00'
    ),
    (
        3,
        'nadia.putri@students.uajy.ac.id',
        'Nadia Putri',
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'nadia.putri@students.uajy.ac.id',
        '2026-10-08 13:42:00'
    );

INSERT INTO
    `laporan` (
        `id`,
        `pengguna_id`,
        `type`,
        `title`,
        `category`,
        `location`,
        `date`,
        `description`,
        `image`,
        `storage_location`,
        `status`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        1,
        'hilang',
        'Tumbler Tupperware Hitam',
        'tumbler',
        'Kantin Gedung Thomas Aquinas',
        '2026-11-10',
        'Tumbler Tupperware 1 liter warna hitam doff. Ada stiker UAJY di bagian depan. Terakhir terlihat setelah makan siang.',
        NULL,
        NULL,
        'masih_dicari',
        '2026-11-10 13:05:00',
        '2026-11-10 13:05:00'
    ),
    (
        2,
        2,
        'ditemukan',
        'Jaket Hoodie Grey',
        'pakaian_jaket',
        'Parkiran Basement Kampus 2',
        '2026-11-09',
        'Hoodie abu-abu polos ukuran L tanpa sablon. Ditemukan tergantung di pagar basement.',
        NULL,
        'Pos Satpam Kampus 2',
        'belum_dikembalikan',
        '2026-11-09 17:22:00',
        '2026-11-09 17:22:00'
    ),
    (
        3,
        2,
        'ditemukan',
        'AirPods Case Putih',
        'elektronik',
        'Ruang 3203 Gedung Bonaventura',
        '2026-11-08',
        'Case AirPods putih tanpa earbuds. Ada goresan kecil di sudut kanan.',
        NULL,
        'Resepsionis Gedung Bonaventura',
        'belum_dikembalikan',
        '2026-11-08 11:40:00',
        '2026-11-08 11:40:00'
    ),
    (
        4,
        3,
        'hilang',
        'Dompet Coklat Berisi KTM',
        'dompet_kartu',
        'Perpustakaan Lantai 2',
        '2026-11-07',
        'Dompet kulit coklat tua. Berisi KTM dan kartu perpustakaan. Mohon hubungi kalau menemukan.',
        NULL,
        NULL,
        'masih_dicari',
        '2026-11-07 15:10:00',
        '2026-11-07 15:10:00'
    ),
    (
        5,
        3,
        'ditemukan',
        'Kunci Motor Honda',
        'kunci',
        'Parkiran Gedung E',
        '2026-11-06',
        'Kunci motor Honda dengan gantungan kain biru. Ditemukan di dekat pintu parkiran.',
        NULL,
        'Dibawa penemu (hubungi via Teams)',
        'belum_dikembalikan',
        '2026-11-06 18:05:00',
        '2026-11-06 18:05:00'
    ),
    (
        6,
        3,
        'hilang',
        'Kalkulator Casio FX-991',
        'elektronik',
        'Gedung Agustinus Lantai 3',
        '2026-11-05',
        'Kalkulator scientific Casio. Ada nama pemilik di bagian belakang.',
        NULL,
        NULL,
        'sudah_ditemukan',
        '2026-11-05 09:30:00',
        '2026-11-06 10:12:00'
    ),
    (
        7,
        2,
        'ditemukan',
        'Botol Minum Stainless',
        'tumbler',
        'Kantin Kampus 3',
        '2026-11-04',
        'Botol stainless 600 ml warna silver dengan tutup hitam.',
        NULL,
        'Dibawa penemu',
        'sudah_dikembalikan',
        '2026-11-04 12:15:00',
        '2026-11-05 08:45:00'
    ),
    (
        8,
        2,
        'ditemukan',
        'Payung Lipat Biru',
        'lainnya',
        'Lobi Gedung Thomas Aquinas',
        '2026-11-03',
        'Payung lipat biru tua dengan gagang kayu.',
        NULL,
        'Pos CS Gedung Thomas Aquinas',
        'belum_dikembalikan',
        '2026-11-03 08:50:00',
        '2026-11-03 08:50:00'
    ),
    (
        9,
        1,
        'hilang',
        'Tote Bag Kanvas',
        'lainnya',
        'Halte Kampus 1',
        '2026-11-02',
        'Tote bag kanvas krem bertuliskan nama depan. Berisi buku catatan dan alat tulis.',
        NULL,
        NULL,
        'masih_dicari',
        '2026-11-02 16:40:00',
        '2026-11-02 16:40:00'
    ),
    (
        10,
        3,
        'ditemukan',
        'Flashdisk 32GB',
        'elektronik',
        'Lab Komputer 2',
        '2026-11-01',
        'Flashdisk hitam 32GB tanpa merek. Ditemukan menempel di depan CPU.',
        NULL,
        'Laboran Lab Komputer 2',
        'sudah_dikembalikan',
        '2026-11-01 10:20:00',
        '2026-11-02 09:05:00'
    );