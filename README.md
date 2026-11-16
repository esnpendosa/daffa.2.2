# AgroNusa (versi PHP + Panel Admin)

## Kebutuhan
- PHP 7.4 atau lebih baru dengan ekstensi `pdo_sqlite` (aktif di hampir semua hosting)
- Apache (file `.htaccess` sudah disiapkan). Di Nginx, blokir akses ke folder `data/` secara manual.

## Pemasangan
1. Unggah seluruh isi folder ini ke hosting (misal `public_html/`).
2. Pastikan folder `data/` dan `assets/photos/` bisa ditulis server (chmod 755, atau 775 bila perlu).
3. Buka `https://domain-anda/admin/`. Pada kunjungan pertama Anda diminta membuat akun admin.
   **Lakukan segera setelah mengunggah**, sebelum orang lain membuka alamat itu.
4. Database `data/agronusa.sqlite` dibuat otomatis dan diisi konten dari situs lama.

## Yang bisa dikelola di admin
- **Artikel**: tulis, edit, hapus, terbit/draf
- **Kategori**: teks halaman, ikon, warna, urutan, foto asli (menggantikan ilustrasi), komoditas dan fakta kuncinya, tips budidaya, tabel perbandingan
- **Pesan masuk**: dari formulir Kontak (tersimpan di database, bisa dibalas lewat email)
- **Pengaturan**: email, WhatsApp, media sosial, teks beranda, footer, ganti kata sandi

## Struktur
- `*.php` di root: halaman publik (nama sama seperti sebelumnya, `.html` menjadi `.php`; tautan `.html` lama otomatis diarahkan)
- `includes/`: database, fungsi bantu, kerangka halaman
- `admin/`: panel admin
- `data/`: database SQLite + `seed.json` (data awal)
- `css/`, `js/`, `assets/`: aset tampilan

## Cadangan
Cukup salin `data/agronusa.sqlite` dan isi `assets/photos/`.
