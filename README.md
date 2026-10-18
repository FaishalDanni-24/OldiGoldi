# OldiGoldi

Aplikasi E-Commerce untuk elektronik bekas, aplikasi ini adalah bagian untuk memenuhi tugas Ulangan Akhir Semester Mata Kuliah E-Commerce.

## Developer
- Syahrial Ahmad Yusuf (3337240008)
- Faishal Danni (3337240072)

## Cara instalasi
Tahap ini mengasumsi anda menggunakan terminal laragon. Pastikan anda sudah menginstalasi laravel dan dependensi utamanya jika menggunakan terminal biasa.
Pastikan ikut setiap tahap ini untuk menginstall project ke sistem anda, pertama tulis di terminal:
1. Masuk ke terminal dan pilih folder seperti dibawah:
```bash
# Sesuaikan dengan folder instalasi laragon masing-masing
cd C:\laragon\www\
```
2. clone project dari git di bawah:
```bash
git clone https://github.com/FaishalDanni-24/OldiGoldi.git
cd C:\laragon\www\cineflick
```
3. install semua dependensi yang diperlukan:
```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
```
4. Buat data (php artisan serve bisa diskip kalau website udah bisa diakses dari laragon)
```bash
php artisan migrate # Jika ditanya seperti ini, Database cineflick_db does not exist. Do you want to create it? pilih yes
php artisan db:seed
npm run dev # Menjalankan vite untuk frontend (Jika lagi developing)
npm run build # Membuat folder build di public (Untuk Hosting)
php artisan serve
```
5. Setup file ".env"
Di file ".env", selain di bagian mail (tahap 6). Pastikan nilainya yang ada di file seperti ini:
```txt
APP_URL=https://oldigoldi.test # Sesuaikan dengan URL website

DB_CONNECTION=mysql
DB_HOST=127.0.0.1 # Sesuaikan dengan alamat host database
DB_PORT=3306 # Sesuaikan port database
DB_DATABASE=cineflick_db
DB_USERNAME=root # Sesuaikan dengan username ke database
DB_PASSWORD= # Jika database mysql ada password, masukkan passwordnya

# Bagian di bawah ini belum difinalisasi, nanti disesuaikan untuk email
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```
6. Lalu jalankan kode ini di terminal laragon, untuk membersihkan semua cache:
```bash
php artisan optimize:clear
```
7. Membuat symbolic link folder 'storage/app/public' untuk data gambar poster dan makanan & minuman
```bash
php artisan storage:link
```