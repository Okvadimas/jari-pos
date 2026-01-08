Berikut adalah draf `README.md` yang profesional dan lengkap untuk proyek **JARIPOS** Anda. File ini disusun khusus berdasarkan *tech stack* yang baru saja kita siapkan (Laravel 12, Vite, Docker Compose) dan dioptimalkan untuk lingkungan pengembangan di MacOS.

---

# JARIPOS - Point of Sale System

JARIPOS adalah aplikasi kasir (Point of Sale) modern yang dibangun menggunakan **Laravel 12** dan **Vite**. Proyek ini sepenuhnya menggunakan **Docker** untuk memastikan lingkungan pengembangan yang konsisten, terutama dioptimalkan untuk arsitektur **Apple Silicon.

## 🚀 Tech Stack

* **Framework:** Laravel 12
* **Frontend Tooling:** Vite (Hot Module Replacement aktif)
* **Database:** MySQL 8.0
* **Cache/Queue:** Redis
* **Containerization:** Docker & Docker Compose
* **Server:** Nginx & PHP 8.3-FPM (Alpine Linux)

---

## 🛠️ Persyaratan Sistem

* Docker Desktop 
* Git

---

## 📦 Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan proyek di mesin lokal Anda:

### 1. Clone Repositori

```bash
git clone https://github.com/username/jaripos.git
cd jaripos

```

### 2. Konfigurasi Environment

Salin file `.env.example` menjadi `.env` dan sesuaikan kredensial database:

```bash
cp .env.example .env

```

**Konfigurasi Database di `.env`:**

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=jaripos
DB_USERNAME=root
DB_PASSWORD=root_password_anda

```

### 3. Build dan Jalankan Container

Jalankan perintah ini untuk membangun image dan menginstal semua dependensi (PHP & Node.js) secara otomatis:

```bash
docker compose up --build -d

```

### 4. Generasi App Key & Migrasi

Jalankan perintah ini di dalam kontainer `app`:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed

```

---

## 🌐 Akses Aplikasi

Setelah kontainer berjalan, Anda dapat mengakses layanan berikut:

* **Web Application:** [http://localhost](https://www.google.com/search?q=http://localhost) (via Nginx)
* **Vite HMR Server:** [http://localhost:5173](https://www.google.com/search?q=http://localhost:5173)
* **MySQL Port:** `3306`

---

## 💻 Alur Kerja Pengembangan

### Menjalankan Perintah Artisan/Composer

Gunakan `docker compose exec app` untuk menjalankan perintah di dalam kontainer:

```bash
# Menambah library PHP
docker compose exec app composer require <package-name>

# Menambah package NPM
docker compose exec app npm install <package-name>

# Membuat Controller
docker compose exec app php artisan make:controller NamaController

```

### Kapan Harus Build Ulang?

Anda hanya perlu melakukan `docker compose up --build` jika:

1. Mengubah `Dockerfile` (menambah ekstensi PHP seperti `zip` atau `gd`).
2. Mengubah konfigurasi utama di `docker-compose.yml`.

---

## 📂 Struktur Folder Docker

```text
.
├── docker/
│   └── nginx/
│       └── conf.d/
│           └── default.conf   # Konfigurasi Nginx
├── Dockerfile                  # Konfigurasi PHP & Node.js
├── docker-compose.yml          # Orkestrasi layanan
└── ... file Laravel lainnya

```

---

## ⚠️ Troubleshooting

* **MySQL Access Denied:** Pastikan Anda tidak mendefinisikan `MYSQL_USER: root` di `docker-compose.yml`. Gunakan hanya `MYSQL_ROOT_PASSWORD` untuk user root.
* **Zip Extension Missing:** Image ini sudah dilengkapi `libzip-dev` dan ekstensi `zip` untuk mendukung `yajra/laravel-datatables`.
* **Vite Not Loading:** Pastikan `vite.config.js` Anda sudah diatur ke `host: '0.0.0.0'` agar dapat diakses dari luar docker.

---

**Dibuat dengan ❤️ untuk pengembangan JARIPOS.**

---