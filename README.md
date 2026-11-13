# FindIt

Papan temuan kampus untuk melaporkan barang hilang dan barang ditemukan. Dibuat 100% native: XHTML, CSS, Vanilla JavaScript, PHP native. Versi ini **front-end only**: tidak memakai MySQL, data demo disimpan di `data/findit.json` (tanpa framework, library, atau CDN). Tampilan minimalis dengan palet biru–emas UAJY.

## Menjalankan

1. Pastikan Apache XAMPP berjalan (MySQL **tidak** diperlukan).
2. Proyek sudah berada di `D:\XAMPP\htdocs\findit`.
3. Buka `http://localhost/findit/`.
4. Data demo dibuat otomatis saat halaman pertama dibuka.

Untuk mengembalikan data demo ke kondisi awal, hapus file `data/findit.json` lalu muat ulang halaman.

Versi lengkap dengan MySQL tersedia di folder `findit_w_db` (di luar folder ini).

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
config/data.php              penyimpanan data demo (JSON)
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
data/findit.json             data demo (dibuat otomatis)
index.php                    beranda, pencarian, filter
register.php login.php logout.php profil.php
laporan_buat.php laporan_edit.php laporan_detail.php
laporan_saya.php laporan_hapus.php laporan_status.php
```

## Catatan teknis

- Markup ditulis well-formed sesuai aturan XHTML. Halaman dikirim sebagai `text/html` (fallback yang diizinkan PRD); ubah `APP_CONTENT_TYPE` di `includes/functions.php` menjadi `application/xhtml+xml` bila ingin mode ketat.
- Tipografi memakai Google Fonts (Space Grotesk untuk judul, Manrope untuk body) yang dimuat lewat CDN. Ini adalah pengecualian dari aturan "tanpa CDN" pada PRD untuk kebutuhan tipografi.
- Upload divalidasi MIME asli (`finfo`), ekstensi, ukuran maksimal 2 MB, nama file acak, serta folder `uploads/` diblokir dari eksekusi skrip.
- Login tetap memakai `password_hash()` / `password_verify()`, jadi akun demo dan akun hasil registrasi bisa dipakai seperti biasa tanpa MySQL.
