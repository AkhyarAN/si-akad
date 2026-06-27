# SIAKAD SMP (Sistem Informasi Akademik)

Aplikasi Sistem Informasi Akademik berbasis Web untuk tingkat Sekolah Menengah Pertama (SMP). Dibangun menggunakan **Laravel 10**, sistem ini dilengkapi dengan berbagai fitur mulai dari manajemen master data, penjadwalan, penilaian, perangkat mengajar, hingga pengiriman notifikasi ketidakhadiran siswa langsung ke WhatsApp Orang Tua.

---

## 🛠️ Persyaratan Sistem (System Requirements)
Sebelum menginstal, pastikan komputer/server Anda memiliki:
- **PHP** versi **8.1** atau yang lebih baru (Karena menggunakan Laravel 10)
- **MySQL** atau MariaDB
- **Composer** (Package Manager PHP)
- Node.js & NPM (Opsional, untuk kompilasi *assets* jika diperlukan)

---

## 🚀 Panduan Instalasi & Pengaturan Awal

### 1. Pengaturan Database
1. Buka aplikasi database Anda (seperti **phpMyAdmin** melalui XAMPP/Laragon, atau menggunakan aplikasi seperti DBeaver/HeidiSQL).
2. Buat database baru dengan nama: `siakad_smp` (atau nama lain yang Anda inginkan). Kosongkan saja, tidak perlu membuat tabel secara manual.

### 2. Pengaturan `.env` (Environment)
1. Buka folder proyek ini di *Code Editor* Anda (seperti VS Code).
2. Temukan file bernama `.env`. *(Jika belum ada, copy file `.env.example` dan ubah namanya menjadi `.env`)*.
3. Sesuaikan pengaturan koneksi database (baris 23) menjadi seperti ini:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=siakad_smp
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Catatan: Ubah `DB_PASSWORD` jika MySQL Anda menggunakan password. Untuk XAMPP default biasanya kosong).*

### 3. Migrasi & Pengisian Data (Seeder)
Aplikasi ini sudah dilengkapi dengan *Demo Data* lengkap (Tahun ajaran, guru, kelas, siswa, orang tua, absensi, dan nilai) agar Anda bisa langsung mencoba fitur-fiturnya.

Buka terminal/command prompt, arahkan ke folder proyek ini (`cd /path/ke/proyek`), lalu jalankan perintah:
```bash
php artisan migrate:fresh --seed
```
*Tunggu hingga proses selesai. Perintah ini akan membuat semua tabel dan mengisinya dengan ribuan data contoh.*

---

## 🏃 Cara Menjalankan Aplikasi

1. **Jalankan Web Server Lokal**
   Di terminal, jalankan perintah:
   ```bash
   php artisan serve
   ```
   Aplikasi kini dapat diakses di browser melalui alamat: **`http://localhost:8000/login`**

2. **Akses dari Perangkat Lain (HP/Laptop di jaringan Wi-Fi yang sama)**
   Jika ingin membuka di HP, hentikan perintah di atas (Ctrl+C), lalu jalankan:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   Akses di HP menggunakan `http://[IP-KOMPUTER-ANDA]:8000`. Contoh: `http://192.168.1.5:8000`.

---

## 🔑 Panduan Setup WhatsApp Gateway (Fonnte)

Agar fitur notifikasi ketidakhadiran (Alpha/Sakit) bisa otomatis terkirim ke WhatsApp Orang Tua, lakukan langkah ini:

1. Daftar dan dapatkan Token API di **[fonnte.com](https://md.fonnte.com)**.
2. Buka file `.env` di proyek ini, dan tambahkan/ubah baris berikut:
   ```env
   FONNTE_TOKEN=IsiDenganTokenDariFonnte
   ```
3. Buka **TAB TERMINAL BARU** (biarkan `php artisan serve` tetap jalan di tab pertama).
4. Jalankan antrean pengiriman (*Queue Worker*) dengan perintah:
   ```bash
   php artisan queue:work
   ```
   *(Worker ini harus dibiarkan terus berjalan di latar belakang agar WA bisa terkirim tanpa membuat halaman web loading lama saat guru menyimpan absensi).*

---

## 👥 Akun Demo (Role / Hak Akses)

Gunakan akun berikut untuk mencoba berbagai hak akses (*Password semua akun:* `password123` *kecuali yang ditulis lain*):

| Role | Email Login | Password | Penjelasan Hak Akses |
|------|-------------|----------|----------------------|
| **Admin** | `admin@siakad.com` | `admin123` | Akses penuh mengatur semua Master Data & User. |
| **Kepala Sekolah** | `kepsek@siakad.com` | `kepsek123` | Memantau seluruh akademik (Dashboard khusus) & menyetujui Perangkat Mengajar. |
| **Guru** | `siti@siakad.com` | `guru123` | Upload perangkat, input absensi kelas, dan input nilai. |
| **Wali Kelas** | *(Gunakan akun guru yang ditunjuk)* | `guru123` | Sama dengan Guru + Rekap kelas perwaliannya. |
| **Orang Tua** | `parent1@siakad.com` | `parent123` | Memantau grafik nilai anak dan rekap kehadirannya. |

*(Ada akun orang tua `parent2@siakad.com`, `parent3...` dst yang di-generate oleh seeder).*

---

## 📝 Alur Penggunaan Aplikasi (Cara Pakai Sesungguhnya)

Jika Anda mereset database dan ingin **menggunakannya untuk data asli sekolah**, ikuti urutan (*flow*) input data berikut ini agar relasinya tidak *error*:

1. **Login sebagai Admin**.
2. Buka menu **Tahun Ajaran** -> Tambahkan tahun ajaran baru & *Set Aktif*.
3. Buka menu **Mata Pelajaran** -> Tambahkan KKM dan jam pelajaran.
4. Buka menu **Data Guru** -> Input profil para guru (Akun login guru akan otomatis terbuat).
5. Buka menu **Data Kelas** -> Buat kelas dan tentukan siapa Wali Kelasnya.
6. Buka menu **Data Siswa** -> Input data siswa satu per satu. (Saat Anda menginput No WA Orang Tua, akun login untuk Orang Tua tersebut akan otomatis dibuat).
7. Buka menu **Jadwal Pelajaran** -> Pilih Kelas, lalu susun jadwal (Hari, Jam, Mapel, dan Guru yang mengajar).

Setelah 7 langkah master data ini selesai, **Guru** baru bisa melakukan pekerjaannya (Input Absensi harian dan Input Nilai) sesuai jadwal yang telah ditentukan.

---
*Dikembangkan dengan ❤️ menggunakan Laravel dan modern Glassmorphism UI.*
