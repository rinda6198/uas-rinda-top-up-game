# 🎮 Website Top Up Game

Website Top Up Game merupakan aplikasi berbasis web yang dikembangkan untuk memudahkan pengguna melihat pilihan game, memilih item atau produk game, melakukan pemesanan, dan memantau status transaksi. Aplikasi ini juga menyediakan halaman admin untuk mengelola data game, item, pengguna, dan pesanan.

Proyek ini dikembangkan sebagai proyek perkuliahan untuk menerapkan keterampilan pengembangan web menggunakan PHP dan MySQL.

## ✨ Fitur Utama

### 👤 Fitur Pengguna

- **Registrasi dan Login** — Membuat akun dan masuk ke aplikasi.
- **Katalog Game** — Melihat daftar game yang tersedia.
- **Detail Game** — Melihat informasi game dan item yang ditawarkan.
- **Keranjang Belanja** — Mengelola item yang akan dipesan.
- **Checkout** — Melakukan proses pemesanan.
- **Pelacakan Pesanan** — Memantau status pesanan.
- **Riwayat Transaksi** — Melihat riwayat pesanan sebelumnya.
- **Profil Pengguna** — Mengelola informasi profil.
- **Informasi Layanan** — Mengakses halaman kontak, kebijakan privasi, dan syarat serta ketentuan.

### 🛠️ Fitur Admin

- **Dashboard Admin** — Menampilkan halaman utama pengelolaan aplikasi.
- **Manajemen Game** — Mengelola data game.
- **Manajemen Item** — Mengelola produk atau item game.
- **Manajemen Pengguna** — Mengelola data pengguna.
- **Pengelolaan Pesanan** — Mendukung proses administrasi transaksi dan status pesanan sesuai implementasi aplikasi.

## 🧰 Teknologi yang Digunakan

- **PHP** — Pengembangan logika aplikasi sisi server.
- **MySQL** — Penyimpanan dan pengelolaan data.
- **HTML** — Penyusunan struktur halaman web.
- **CSS** — Pengaturan tampilan dan tata letak.
- **JavaScript** — Mendukung interaksi pada halaman web.
- **XAMPP** — Lingkungan pengembangan lokal.
- **Git dan GitHub** — Pengelolaan versi dan penyimpanan repositori kode.

## 📸 Screenshot Aplikasi

Tambahkan screenshot aplikasi pada folder `screenshots/` di repositori ini.

Contoh tampilan yang dapat ditampilkan:

| Halaman           | Screenshot                        |
| ----------------- | --------------------------------- |
| Halaman Utama     | `screenshots/home.png`            |
| Detail Game       | `screenshots/detail-game.png`     |
| Keranjang Belanja | `screenshots/keranjang.png`       |
| Dashboard Admin   | `screenshots/admin-dashboard.png` |
| Manajemen Game    | `screenshots/admin-games.png`     |

Setelah gambar diunggah, tampilkan screenshot utama menggunakan format berikut:

```markdown
![Halaman Utama](screenshots/home.png)
```
## Screenshot Aplikasi

### Halaman Utama
![Halaman Utama](screenshots/home1.png)
![Halaman Utama](screenshots/home2.png)
![Halaman Utama](screenshots/home3.png)
![Halaman Utama](screenshots/home4.png)
*Catatan: Screenshot perlu ditambahkan sesuai tampilan aplikasi yang sebenarnya.*

## ⚙️ Persyaratan

Sebelum menjalankan aplikasi, pastikan perangkat telah memiliki:

- XAMPP atau lingkungan server lokal sejenis.
- PHP yang kompatibel dengan kode proyek.
- MySQL atau MariaDB.
- Browser web.
- Git (opsional, untuk mengunduh repositori).

## 🚀 Instalasi dan Menjalankan Aplikasi

### 1. Clone Repositori

Jalankan perintah berikut pada terminal:

```bash
git clone https://github.com/rinda6198/uas-rinda-top-up-game.git
```

### 2. Pindahkan ke Folder XAMPP

Pastikan folder proyek berada di:

```text
C:\xampp\htdocs\uas-rinda-top-up-game
```

Jika menggunakan Git Bash atau terminal yang mendukung perintah `cd`, masuk ke folder proyek:

```bash
cd /c/xampp/htdocs/uas-rinda-top-up-game
```

### 3. Jalankan XAMPP

Buka XAMPP Control Panel, kemudian aktifkan:

- Apache
- MySQL

Pastikan kedua layanan berjalan dengan baik.

### 4. Siapkan Database

1. Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
2. Buat database baru sesuai nama database yang digunakan dalam aplikasi.
3. Impor file SQL hasil ekspor database proyek melalui menu **Import**.

**Penting:** Pastikan file ekspor database (`.sql`) sudah disediakan di repositori atau tersedia secara terpisah. Jika belum ada, ekspor database dari phpMyAdmin pada komputer pengembang terlebih dahulu. Tanpa struktur tabel dan data awal yang sesuai, aplikasi mungkin tidak dapat berjalan dengan benar.

### 5. Konfigurasi Database

Buka file konfigurasi database:

```text
uash/config.php
```

Sesuaikan host, nama database, username, dan password berdasarkan konfigurasi MySQL lokal.

Contoh konfigurasi umum XAMPP:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "nama_database";
```

*Contoh di atas hanya ilustrasi. Sesuaikan nama variabel dan struktur konfigurasi dengan isi `config.php` yang sebenarnya.*

Jangan menggunakan kredensial produksi atau menyimpan password sensitif di repositori publik.

### 6. Jalankan Aplikasi

Buka browser dan akses:

```text
http://localhost/uas-rinda-top-up-game/uash/
```

Jika halaman utama belum tampil, periksa kembali struktur folder, konfigurasi database, dan lokasi file `index.php`.

## 📁 Struktur Proyek

```text
uas-rinda-top-up-game/
├── .gitignore
└── uash/
    ├── index.php
    ├── login.php
    ├── register.php
    ├── detail-game.php
    ├── keranjang.php
    ├── checkout.php
    ├── lacak.php
    ├── riwayat.php
    ├── profile.php
    ├── config.php
    ├── admin-dashboard.php
    ├── admin-games.php
    ├── admin-items.php
    ├── admin-users.php
    ├── uploads/
    │   ├── games/
    │   └── items/
    └── ...
```

## 🎯 Tujuan Pengembangan

Proyek ini bertujuan untuk mengimplementasikan konsep pengembangan aplikasi web, pengelolaan basis data, autentikasi pengguna, pengolahan transaksi, serta pengelolaan data melalui antarmuka admin.

## 👩‍💻 Pengembang

**Rinda Wulandari**

Mahasiswa D3 Manajemen Informatika, Politeknik Negeri Sriwijaya.

- GitHub: [@rinda6198](https://github.com/rinda6198)

---

Dikembangkan sebagai proyek perkuliahan dan portofolio pengembangan web.
