# PRD — FindIt! (Campus Lost & Found)

| | |
| --- | --- |
| **Versi** | 1.0 (Draft) |
| **Tanggal** | 12 November 2026 |
| **Platform** | Website (localhost) |
| **Stack** | 100% native: XHTML, CSS, JavaScript, PHP Native, MySQL — tanpa framework/library/CDN apa pun |

---

## 1. Ringkasan Produk

FindIt! adalah website Lost & Found untuk lingkungan kampus. Mahasiswa dan Cleaning Service (CS) dapat melaporkan barang hilang atau ditemukan, mencari barang lewat pencarian dan filter, serta melihat apakah barang yang ditemukan masih dibawa penemu atau sudah diserahkan ke CS.

FindIt! hanya **menyediakan informasi**. Pengambilan dan verifikasi kepemilikan barang tetap dilakukan langsung sesuai prosedur kampus.

## 2. Latar Belakang & Masalah

- Barang yang ditemukan biasanya diserahkan ke CS dan dicatat manual di buku.
- Mahasiswa yang kehilangan barang tidak punya platform khusus untuk mencari informasi barang temuan.
- Informasi tersebar dan tidak bisa dicari berdasarkan kategori, lokasi, atau waktu.

## 3. Tujuan

**Tujuan utama:** mempermudah mahasiswa melaporkan, mencari, dan memperoleh informasi barang hilang maupun ditemukan di kampus.

**Indikator keberhasilan (untuk evaluasi di localhost / demo):**

| Indikator | Target |
| --- | --- |
| Pengguna dapat membuat laporan lengkap (dengan foto) | ≤ 2 menit |
| Pengguna dapat menemukan laporan lewat pencarian/filter | ≤ 3 klik dari halaman beranda |
| Seluruh fitur CRUD berfungsi tanpa error pada skenario uji | 100% |
| Halaman dapat digunakan di layar ≥ 360 px | Lolos |

## 4. Pengguna

Hanya ada **satu jenis akun** dengan hak akses yang sama. Tidak ada admin.

| Persona | Kebutuhan |
| --- | --- |
| **Mahasiswa (kehilangan barang)** | Memposting laporan barang hilang, mencari apakah barangnya sudah ditemukan, tahu di mana barang disimpan. |
| **Mahasiswa (penemu)** | Memposting barang temuan dengan cepat, menandai kalau sudah dikembalikan. |
| **Cleaning Service** | Mencatat barang yang diserahkan kepadanya secara digital menggantikan buku manual. |

## 5. Ruang Lingkup

### Termasuk (In Scope)

- Registrasi, login, logout, pengelolaan profil.
- CRUD laporan barang hilang dan barang ditemukan.
- Upload foto dan informasi barang.
- Pencarian dan filter (nama, kategori, jenis laporan, lokasi).
- Informasi lokasi penyimpanan barang.
- Kontak pelapor via Microsoft Teams.
- Pengelolaan status oleh pembuat laporan.

### Tidak Termasuk (Out of Scope)

- Admin khusus / moderasi oleh admin.
- Smart Match (pencocokan otomatis hilang ↔ ditemukan).
- Chat internal.
- Verifikasi klaim otomatis.
- Notifikasi email/push.
- Deployment produksi (pengembangan hanya di localhost).

## 6. Batasan Teknologi (Wajib)

| Lapisan | Ketentuan |
| --- | --- |
| Frontend | XHTML (well-formed, `<!DOCTYPE html>` + atribut `xmlns`), CSS murni, JavaScript murni (Vanilla JS). **Dilarang**: Bootstrap, Tailwind, jQuery, React, dsb. |
| Backend | PHP Native (tanpa Laravel/CodeIgniter/dll). Struktur sederhana berbasis file + `include`/`require`. |
| Database | MySQL, diakses dengan **PDO** (bawaan PHP, bukan library eksternal). |
| Server | Localhost (Apache + PHP + MySQL, mis. XAMPP/Laragon). |
| Aset | Font sistem (tanpa Google Fonts), ikon dengan SVG inline / karakter Unicode. Tidak memakai CDN. |

**Catatan XHTML:**

- Semua tag ditutup (`<br />`, `<img ... />`, `<input ... />`), atribut diberi tanda kutip, tag huruf kecil.
- Halaman PHP mengirim header `Content-Type: application/xhtml+xml; charset=UTF-8`. Karena XHTML ketat, **satu kesalahan markup akan membuat halaman gagal tampil**, jadi semua keluaran dinamis harus di-escape dengan `htmlspecialchars()`.
- Jika selama pengembangan terlalu merepotkan, fallback sementara: `text/html` dengan markup tetap sesuai aturan XHTML.

## 7. Aturan Bisnis

1. Satu akun = satu hak akses (tidak ada peran berbeda).
2. Semua pengguna yang login dapat **membuat** laporan dan **melihat** semua laporan.
3. Hanya **pembuat laporan** yang dapat **mengubah status**, **mengedit**, dan **menghapus** laporannya.
4. Laporan salah atau duplikat dihapus oleh pembuatnya.
5. Status yang valid bergantung pada jenis laporan:

| Jenis laporan | Status yang valid |
| --- | --- |
| Barang hilang (`hilang`) | `Masih Dicari` → `Sudah Ditemukan` |
| Barang ditemukan (`ditemukan`) | `Belum Dikembalikan` → `Sudah Dikembalikan` |

1. Status awal: `Masih Dicari` (hilang) atau `Belum Dikembalikan` (ditemukan).
2. Laporan **barang ditemukan** wajib mencantumkan lokasi penyimpanan (dibawa penemu atau diserahkan ke CS).
3. Kategori barang: **Tumbler, Pakaian/Jaket, Barang Elektronik, Dompet/Kartu, Kunci, Lainnya.**
4. Kontak pelapor dilakukan lewat Microsoft Teams; pengguna mengisi identitas Teams di profil.

## 8. Kebutuhan Fungsional

### 8.1 Autentikasi & Profil

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-01 | Registrasi: nama, email, password, konfirmasi password, identitas Teams. | Must |
| FR-02 | Email unik; validasi format email dan panjang minimal password (8 karakter). | Must |
| FR-03 | Login dengan email + password; sesi PHP (`$_SESSION`). | Must |
| FR-04 | Logout menghancurkan sesi. | Must |
| FR-05 | Halaman profil: lihat dan ubah nama, identitas Teams, serta ganti password. | Must |
| FR-06 | Halaman yang butuh login mengalihkan tamu ke halaman login. | Must |

### 8.2 Laporan (CRUD)

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-10 | **Create**: form laporan berisi jenis laporan, judul/nama barang, kategori, lokasi, tanggal, deskripsi, foto, dan lokasi penyimpanan (khusus ditemukan). | Must |
| FR-11 | **Read (daftar)**: tampilan kartu/daftar semua laporan, terbaru di atas, dengan paginasi. | Must |
| FR-12 | **Read (detail)**: halaman detail dengan foto, informasi lengkap, status, lokasi penyimpanan, dan tombol kontak Teams. | Must |
| FR-13 | **Update**: pembuat dapat mengedit data laporan. | Must |
| FR-14 | **Update status**: pembuat mengubah status sesuai aturan bisnis nomor 5. | Must |
| FR-15 | **Delete**: pembuat dapat menghapus laporan (dengan konfirmasi) sekaligus menghapus file foto. | Must |
| FR-16 | Halaman "Laporan Saya" menampilkan laporan milik pengguna yang login. | Should |

### 8.3 Foto

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-20 | Upload satu foto per laporan; format JPG/PNG/WEBP; maksimal 2 MB. | Must |
| FR-21 | Validasi sisi server: MIME type asli (`finfo`), ekstensi, dan ukuran; nama file diganti acak. | Must |
| FR-22 | Pratinjau foto sebelum dikirim (JavaScript, `FileReader`). | Should |
| FR-23 | Jika tidak ada foto, tampilkan gambar placeholder. | Should |

### 8.4 Pencarian & Filter

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-30 | Pencarian kata kunci pada judul/nama barang dan deskripsi. | Must |
| FR-31 | Filter kategori. | Must |
| FR-32 | Filter jenis laporan (hilang / ditemukan). | Must |
| FR-33 | Filter lokasi (teks atau daftar lokasi kampus). | Must |
| FR-34 | Filter status (opsional, default: tampilkan semua). | Could |
| FR-35 | Kombinasi filter bekerja bersamaan (AND); parameter via `GET` agar URL dapat dibagikan. | Must |
| FR-36 | Tampilan hasil kosong yang informatif ("Tidak ada laporan ditemukan"). | Should |

### 8.5 Lokasi Penyimpanan & Kontak

| ID | Kebutuhan | Prioritas |
| --- | --- | --- |
| FR-40 | Laporan ditemukan menampilkan lokasi penyimpanan, misalnya "Dibawa penemu" atau "Diserahkan ke CS (nama gedung/pos)". | Must |
| FR-41 | Tombol "Hubungi via Teams" membuka tautan Teams pelapor (deep link `https://teams.microsoft.com/l/chat/0/0?users=<email>`). | Must |
| FR-42 | Kontak hanya terlihat oleh pengguna yang login. | Must |

## 9. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
| --- | --- |
| **Keamanan** | `password_hash()` / `password_verify()`; **prepared statement PDO** untuk semua query; `htmlspecialchars()` untuk semua output; token CSRF pada form POST; `session_regenerate_id()` saat login; cek kepemilikan laporan di server sebelum edit/hapus. |
| **Upload aman** | Folder `uploads/` tanpa eksekusi PHP (`.htaccess`), validasi MIME, nama file acak. |
| **Kinerja** | Halaman daftar memuat ≤ 12 laporan per halaman; gambar dengan `loading="lazy"`. |
| **Responsif** | CSS murni dengan media query; mobile-first; minimal lebar 360 px. |
| **Aksesibilitas** | Label pada setiap input, atribut `alt` pada gambar, kontras warna memadai, navigasi dapat dipakai dengan keyboard. |
| **Kompatibilitas** | Chrome, Edge, Firefox versi terbaru. |
| **Validasi** | Validasi dua lapis: JavaScript (kenyamanan) dan PHP (wajib). |
| **Kode** | Terpisah menurut tanggung jawab (config, fungsi, halaman, template); komentar singkat pada bagian penting. |

## 10. Model Data (MySQL)

Sesuai ERD: satu **Pengguna** membuat banyak **Laporan** (1 : N).

```sql
CREATE DATABASE findit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE findit;

CREATE TABLE pengguna (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email       VARCHAR(100) NOT NULL UNIQUE,
  name        VARCHAR(100) NOT NULL,
  password    VARCHAR(255) NOT NULL,          -- hasil password_hash()
  teams       VARCHAR(150) NOT NULL,          -- identitas / email Microsoft Teams
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE laporan (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pengguna_id       INT UNSIGNED NOT NULL,    -- FK ke pembuat laporan
  type              ENUM('hilang','ditemukan') NOT NULL,
  title             VARCHAR(120) NOT NULL,
  category          ENUM('tumbler','pakaian_jaket','elektronik','dompet_kartu','kunci','lainnya') NOT NULL,
  location          VARCHAR(150) NOT NULL,    -- lokasi hilang / ditemukan
  date              DATE NOT NULL,
  description       TEXT NULL,
  image             VARCHAR(255) NULL,        -- nama file di folder uploads/
  storage_location  VARCHAR(150) NULL,        -- wajib untuk type = ditemukan
  status            ENUM('masih_dicari','sudah_ditemukan','belum_dikembalikan','sudah_dikembalikan') NOT NULL,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_laporan_pengguna FOREIGN KEY (pengguna_id)
    REFERENCES pengguna(id) ON DELETE CASCADE,
  INDEX idx_type_category (type, category),
  INDEX idx_status (status),
  INDEX idx_date (date)
) ENGINE=InnoDB;
```

> Kolom `pengguna_id` dan `updated_at` ditambahkan di luar ERD sebagai implementasi relasi 1 : N dan pencatatan perubahan. Validasi kombinasi `type` ↔ `status` dilakukan di PHP.

## 11. Halaman & Alur

### 11.1 Daftar Halaman

| Halaman | File | Akses |
| --- | --- | --- |
| Beranda (daftar + pencarian + filter) | `index.php` | Publik* |
| Registrasi | `register.php` | Tamu |
| Login | `login.php` | Tamu |
| Logout | `logout.php` | Login |
| Detail laporan | `laporan_detail.php?id=` | Login |
| Buat laporan | `laporan_buat.php` | Login |
| Edit laporan | `laporan_edit.php?id=` | Pemilik |
| Hapus laporan | `laporan_hapus.php` (POST) | Pemilik |
| Ubah status | `laporan_status.php` (POST) | Pemilik |
| Laporan saya | `laporan_saya.php` | Login |
| Profil | `profil.php` | Login |

\* Daftar dan detail laporan boleh dilihat tanpa login; kontak Teams memerlukan login (lihat FR-42). Tamu yang menekan tombol kontak akan diarahkan lewat dialog ke halaman login.

### 11.2 Alur Utama

**Melaporkan barang ditemukan:** Login → Buat Laporan → pilih jenis "Ditemukan" → isi data + foto + lokasi penyimpanan → Simpan → laporan tampil di beranda dengan status *Belum Dikembalikan*.

**Mencari barang hilang:** Beranda → ketik kata kunci / pilih filter → buka detail → lihat lokasi penyimpanan → hubungi pelapor via Teams → ambil barang langsung sesuai prosedur kampus.

**Menutup laporan:** Laporan Saya → buka detail → ubah status ke *Sudah Dikembalikan* / *Sudah Ditemukan*.

### 11.3 Diagram Status

```
Hilang:     [Masih Dicari] ───► [Sudah Ditemukan]
Ditemukan:  [Belum Dikembalikan] ───► [Sudah Dikembalikan]
```

Pembuat laporan boleh mengubah status ke arah sebaliknya jika salah klik (opsional).

## 12. Struktur Proyek

```
findit/
├── index.php
├── register.php
├── login.php
├── logout.php
├── profil.php
├── laporan_buat.php
├── laporan_edit.php
├── laporan_detail.php
├── laporan_saya.php
├── laporan_hapus.php
├── laporan_status.php
├── config/
│   └── database.php        # koneksi PDO
├── includes/
│   ├── auth.php            # sesi, cek login, cek pemilik
│   ├── csrf.php            # buat & verifikasi token
│   ├── functions.php       # helper: e(), validasi, upload, label status
│   ├── header.php          # <head>, navbar (XHTML)
│   └── footer.php
├── assets/
│   ├── css/style.css
│   ├── js/app.js           # preview foto, validasi form, konfirmasi hapus
│   └── img/placeholder.svg
├── uploads/                # foto laporan (+ .htaccess)
└── database/
    └── findit.sql
```

## 13. Kebutuhan Antarmuka (UI)

- Navbar: logo FindIt!, Beranda, Buat Laporan, Laporan Saya, Profil, Login/Logout.
- Beranda: bilah pencarian, filter (kategori, jenis, lokasi), grid kartu laporan.
- Kartu laporan: foto, judul, badge jenis (Hilang/Ditemukan), badge status, kategori, lokasi, tanggal.
- Badge berwarna **disertai teks** (tidak hanya mengandalkan warna).
- Pesan sukses/galat ditampilkan lewat *flash message* di bagian atas halaman.
- Konfirmasi dialog sebelum menghapus laporan.

## 14. Kriteria Penerimaan (Contoh)

| ID | Skenario | Hasil yang diharapkan |
| --- | --- | --- |
| AC-01 | Registrasi dengan email yang sudah dipakai | Ditolak, pesan "Email sudah terdaftar". |
| AC-02 | Login dengan password salah | Ditolak, tanpa membocorkan apakah email terdaftar. |
| AC-03 | Tamu membuka `laporan_buat.php` | Dialihkan ke halaman login. |
| AC-04 | Membuat laporan ditemukan tanpa lokasi penyimpanan | Ditolak, pesan validasi. |
| AC-05 | Upload file `.php` yang diganti nama menjadi `.jpg` | Ditolak oleh validasi MIME. |
| AC-06 | Pengguna A membuka URL edit/hapus laporan milik B | Ditolak (HTTP 403). |
| AC-07 | Filter kategori "Kunci" + jenis "Ditemukan" | Hanya laporan yang cocok kedua kriteria. |
| AC-08 | Mengubah status laporan hilang menjadi "Sudah Dikembalikan" | Ditolak (status tidak valid untuk jenis itu). |
| AC-09 | Menghapus laporan | File foto ikut terhapus dari `uploads/`. |
| AC-10 | Input `<script>alert(1)</script>` pada judul | Tampil sebagai teks biasa, tidak dieksekusi. |

## 15. Rencana Pengembangan

| Fase | Cakupan |
| --- | --- |
| **1. Fondasi** | Database, koneksi PDO, template header/footer, CSS dasar. |
| **2. Autentikasi** | Registrasi, login, logout, profil, proteksi halaman. |
| **3. Laporan** | Create, daftar, detail, edit, hapus, upload foto. |
| **4. Pencarian** | Search, filter, paginasi, Laporan Saya. |
| **5. Status & Kontak** | Ubah status, lokasi penyimpanan, tautan Teams. |
| **6. Penyempurnaan** | Responsif, aksesibilitas, validasi, pengujian AC-01 s.d. AC-10. |

## 16. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| --- | --- | --- |
| Tanpa admin, laporan palsu/spam tidak dimoderasi | Informasi tidak akurat | Wajib login; pembuat bisa menghapus; kontak dan detail hanya untuk pengguna login. |
| Tanpa verifikasi klaim, orang bisa mengaku pemilik | Barang diambil orang salah | Pengambilan tetap tatap muka sesuai prosedur kampus; sebaiknya deskripsi detail barang tidak dipublikasikan penuh. |
| XHTML ketat gagal render bila ada error markup | Halaman kosong | Escape semua output; uji dengan validator; fallback ke `text/html`. |
| Upload file berbahaya | Celah keamanan | Validasi MIME, ukuran, nama acak, nonaktifkan eksekusi PHP di `uploads/`. |
| Penyimpanan hanya di localhost | Tidak bisa diakses pengguna lain | Sesuai batasan proyek; dapat dipindah ke hosting pada fase lanjutan. |

## 17. Asumsi & Pertanyaan Terbuka

1. Daftar lokasi kampus: input teks bebas atau dropdown berisi gedung tertentu?
2. Apakah pendaftaran dibatasi email kampus (mis. domain `@students.uajy.ac.id`)?
3. Lokasi penyimpanan: pilihan tetap ("Dibawa penemu" / "Diserahkan ke CS") ditambah keterangan, atau teks bebas?
4. Apakah daftar laporan boleh dilihat tamu tanpa login (diasumsikan boleh pada dokumen ini)?
5. Apakah laporan lama (mis. > 90 hari) perlu ditandai/diarsipkan secara otomatis? *(di luar cakupan versi 1.0)*

---
*Dokumen ini adalah draf PRD versi 1.0 dan dapat direvisi sesuai kebutuhan pengembangan.*

## referensi design
![alt text](image.png)