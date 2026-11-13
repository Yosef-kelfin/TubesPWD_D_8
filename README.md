# FindIt

Papan temuan kampus untuk melaporkan barang hilang dan barang ditemukan. Dibuat 100% native: XHTML, CSS, Vanilla JavaScript, PHP native, MySQL via PDO. Tanpa framework, library, atau CDN.

## Menjalankan

1. Pastikan Apache dan MySQL XAMPP berjalan.
2. Proyek sudah berada di `D:\XAMPP\htdocs\findit`.
3. Impor database demo:
   - phpMyAdmin: pilih tab **Import**, jalankan `database/findit.sql`.
   - atau CLI: `D:\XAMPP\mysql\bin\mysql -u root < database\findit.sql`
4. Periksa koneksi di `config/database.php` bila user/password MySQL berbeda.
5. Buka `http://localhost/findit/`.

`database/findit.sql` berisi skema `pengguna` + `laporan` dan 10 laporan demo. Impor ulang file itu kapan saja untuk mengembalikan data demo.

## Akun demo

| Email | Password |
| --- | --- |
| `kelvin@uajy.ac.id` | `password` |
| `cs@uajy.ac.id` | `password` |
| `nadia.putri@students.uajy.ac.id` | `password` |

## Fitur

- Registrasi, login, logout, dan profil (nama, kontak Teams, ganti password).
- CRUD laporan barang hilang dan ditemukan, termasuk upload foto.
- Pencarian kata kunci serta filter jenis, kategori, lokasi, dan status yang bisa dikombinasikan lewat URL `GET`.
- Paginasi 12 laporan per halaman dan halaman "Laporan Saya".
- Status otomatis dan valid per jenis laporan.
- Lokasi penyimpanan wajib untuk barang ditemukan.
- Tombol "Hubungi via Teams" dari kontak pelapor.
- Pratinjau foto sebelum upload, placeholder per kategori, dan konfirmasi hapus.
- Responsif sampai lebar 360 px, fokus keyboard terlihat, dan menghormati `prefers-reduced-motion`.

## Struktur

```
config/database.php          koneksi PDO
includes/auth.php            sesi, login, cek pemilik
includes/csrf.php            token CSRF
includes/functions.php       helper, validasi, upload, label
includes/header.php          head + navbar + flash
includes/footer.php
includes/laporan_form.php    form laporan bersama (buat/edit)
includes/report_card.php     kartu laporan
assets/css/style.css
assets/js/app.js
assets/img/                  placeholder + empty state
uploads/                     foto laporan + .htaccess
database/findit.sql          skema + data demo
index.php                    beranda, pencarian, filter
register.php login.php logout.php profil.php
laporan_buat.php laporan_edit.php laporan_detail.php
laporan_saya.php laporan_hapus.php laporan_status.php
```

## Catatan teknis

- Markup ditulis well-formed sesuai aturan XHTML. Halaman dikirim sebagai `text/html` (fallback yang diizinkan PRD); ubah `APP_CONTENT_TYPE` di `includes/functions.php` menjadi `application/xhtml+xml` bila ingin mode ketat.
- Semua query memakai prepared statement PDO, output di-escape dengan `htmlspecialchars()`, form POST memakai token CSRF, dan kepemilikan laporan dicek di server.
- Upload divalidasi MIME asli (`finfo`), ekstensi, ukuran maksimal 2 MB, nama file acak, serta folder `uploads/` diblokir dari eksekusi skrip.
- Database hanya berisi tabel `pengguna` dan `laporan`. Tabel lama `users` dan `items` sudah dihapus karena tidak dipakai.
