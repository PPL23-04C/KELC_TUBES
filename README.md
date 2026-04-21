# Kelompok C
# ⚡ WattCare — Aplikasi Monitoring Konsumsi Listrik

**WattCare** adalah aplikasi web berbasis Laravel untuk memantau dan menganalisis penggunaan listrik rumah tangga. Aplikasi ini membantu pengguna mencatat penggunaan perangkat elektronik, menghitung konsumsi energi (kWh), estimasi biaya, serta dampak lingkungan (emisi CO₂) secara otomatis, disajikan dalam bentuk dashboard dan grafik yang mudah dipahami.

---

## 📋 Daftar Isi

- Fitur Utama
- Tech Stack
- Arsitektur Sistem
- Struktur Proyek
- Alur Kerja Aplikasi
- Database Schema
- API Endpoints
- Halaman & Routing
- Panduan Instalasi

---

## ✨ Fitur Utama

| Kode | Fitur | Deskripsi |
|------|-------|-----------|
| F-01 | Registrasi Pengguna | Pengguna baru membuat akun (nama, email, password, daya listrik/VA) |
| F-02 | Login & Logout | Autentikasi pengguna untuk mengakses sistem |
| F-03 | Input Data Perangkat | Form input nama alat, daya (watt), jumlah unit |
| F-04 | Validasi Data Input | Validasi angka positif, semua field wajib diisi |
| F-05 | Kalkulasi kWh Harian | Rumus: (Daya × Jam × Jumlah Unit) ÷ 1000 |
| F-06 | Kalkulasi Estimasi Biaya | Total kWh × Tarif PLN (Rp/kWh) |
| F-07 | Kalkulasi Emisi CO₂ | Total kWh × 0,89 kg CO₂ |
| F-08 | Dashboard Monitoring | Total penggunaan, estimasi biaya, emisi CO₂, grafik 7 hari |
| F-09 | Alert Harian (VA-based) | Alert BOROS jika pemakaian hari ini melebihi batas sesuai VA |
| F-10 | Riwayat Analisis | Histori semua pencatatan per pengguna |
| F-11 | Filter Riwayat | Filter berdasarkan rentang tanggal |
| F-12 | Manajemen Perangkat | Tambah, ubah, hapus perangkat |
| F-13 | Rekomendasi Hemat Energi | Tips & saran penghematan berdasarkan pola penggunaan |
| F-14 | Manajemen Profil | Edit nama, email, password, daya listrik |

---

## 🛠 Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Frontend** | Blade Templates, HTML5, CSS3, JavaScript, Chart.js |
| **Backend** | Laravel 11 (PHP) |
| **Web Server** | XAMPP (Apache) |
| **Database** | MySQL |
| **Authentication** | Session-based (Laravel Auth) |
| **Development Tools** | Visual Studio Code, Postman (optional) |
| **Deployment** | Local Server (XAMPP / artisan serve) |

---

## 🏗 Arsitektur Sistem

WattCare menggunakan **Laravel Monolithic Architecture** dengan struktur MVC (Model-View-Controller). Sistem hanya memiliki **satu role (User)**.

```
┌─────────────────────────────────────────────────────────────┐
│                      BROWSER (Client)                       │
│  ┌───────────────────────────────────────────────────────┐  │
│  │           Blade Templates + CSS + JavaScript          │  │
│  │  ┌────────┐ ┌────────┐ ┌────────┐ ┌──────────────┐  │  │
│  │  │ Login  │ │Register│ │Dashboard│ │  Riwayat    │  │  │
│  │  └────────┘ └────────┘ └────────┘ └──────────────┘  │  │
│  │  ┌────────┐ ┌────────────────────────────────────┐  │  │
│  │  │Profil  │ │        Rekomendasi Page            │  │  │
│  │  └────────┘ └────────────────────────────────────┘  │  │
│  └─────────────────────┬───────────────────────────────┘  │
└────────────────────────┼───────────────────────────────────┘
                         │ HTTP (Request/Response + Session)
┌────────────────────────┼───────────────────────────────────┐
│              LARAVEL APPLICATION (Monolith)                │
│  ┌─────────────────────┴───────────────────────────────┐  │
│  │                   ROUTES (web.php)                  │  │
│  └─────────────────────┬───────────────────────────────┘  │
│                         │                                  │
│  ┌──────────────────────┴──────────────────────────────┐  │
│  │                   CONTROLLERS                        │  │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────┐ │  │
│  │  │ Auth     │ │ Device   │ │Analysis  │ │Recommend│ │  │
│  │  │Controller│ │Controller│ │Controller│ │Controller│ │  │
│  │  └────┬─────┘ └────┬─────┘ └────┬─────┘ └───┬────┘ │  │
│  └───────┼────────────┼────────────┼───────────┼───────┘  │
│          │            │            │           │          │
│  ┌───────┴────────────┴────────────┴───────────┴───────┐  │
│  │                   MIDDLEWARE                        │  │
│  │                    (auth)                           │  │
│  └─────────────────────┬───────────────────────────────┘  │
│                        │                                   │
│  ┌─────────────────────┴───────────────────────────────┐  │
│  │              BUSINESS LOGIC (Services)              │  │
│  │  ┌─────────────────────────────────────────────┐   │  │
│  │  │ • CalculatorService (kWh, biaya, CO₂)       │   │  │
│  │  │ • AlertService (alert harian VA-based)      │   │  │
│  │  │ • RecommendationService (tips hemat)        │   │  │
│  │  └─────────────────────────────────────────────┘   │  │
│  └─────────────────────┬───────────────────────────────┘  │
│                        │                                   │
│  ┌─────────────────────┴───────────────────────────────┐  │
│  │              ELOQUENT ORM (Models)                  │  │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────┐ │  │
│  │  │  User    │ │  Device  │ │Monitoring│ │Billing │ │  │
│  │  │  Model   │ │  Model   │ │   Log    │ │ Model  │ │  │
│  │  └──────────┘ └──────────┘ └──────────┘ └────────┘ │  │
│  │  ┌──────────┐ ┌──────────────────────────────────┐ │  │
│  │  │ CO2Impact│ │    Recommendation Model          │ │  │
│  │  │  Model   │ │                                  │ │  │
│  │  └──────────┘ └──────────────────────────────────┘ │  │
│  └─────────────────────┬───────────────────────────────┘  │
└────────────────────────┼───────────────────────────────────┘
                         │
┌────────────────────────┼───────────────────────────────────┐
│                   DATA TIER                                │
│  ┌─────────────────────┴───────────────────────────────┐  │
│  │                   MySQL Database                    │  │
│  │  ┌─────────┐ ┌─────────┐ ┌──────────────┐         │  │
│  │  │ users   │ │ devices │ │ monitoring_  │         │  │
│  │  │         │ │         │ │    logs      │         │  │
│  │  └─────────┘ └─────────┘ └──────────────┘         │  │
│  │  ┌─────────┐ ┌─────────┐ ┌──────────────┐         │  │
│  │  │ billing │ │co2_impact│ │recommendation│         │  │
│  │  └─────────┘ └─────────┘ └──────────────┘         │  │
│  └─────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────┘
```

---

## 📁 Struktur Proyek (Laravel)

```
WATTCARE/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DeviceController.php
│   │   │   ├── AnalysisController.php
│   │   │   ├── HistoryController.php
│   │   │   ├── RecommendationController.php
│   │   │   └── ProfileController.php
│   │   └── Requests/
│   │       ├── DeviceRequest.php
│   │       ├── AnalysisRequest.php
│   │       ├── ProfileUpdateRequest.php
│   │       ├── RegisterRequest.php
│   │       └── LoginRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Device.php
│   │   ├── MonitoringLog.php
│   │   ├── Billing.php
│   │   ├── CO2Impact.php
│   │   └── Recommendation.php
│   └── Services/
│       ├── CalculatorService.php
│       ├── AlertService.php
│       └── RecommendationService.php
├── database/
│   └── migrations/
│       ├── 0001_01_01_000000_create_users_table.php
│       ├── 2024_01_01_000001_create_devices_table.php
│       ├── 2024_01_01_000002_create_monitoring_logs_table.php
│       ├── 2024_01_01_000003_create_billings_table.php
│       ├── 2024_01_01_000004_create_co2_impacts_table.php
│       └── 2024_01_01_000005_create_recommendations_table.php
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php
│   │   ├── partials/sidebar.blade.php
│   │   ├── landing.blade.php
│   │   ├── auth/login.blade.php
│   │   ├── auth/register.blade.php
│   │   ├── dashboard/index.blade.php
│   │   ├── devices/index.blade.php
│   │   ├── devices/create.blade.php
│   │   ├── devices/edit.blade.php
│   │   ├── analysis/input.blade.php
│   │   ├── history/index.blade.php
│   │   ├── recommendations/index.blade.php
│   │   └── profile/edit.blade.php
│   ├── css/app.css
│   └── js/app.js
├── routes/web.php
├── config/constants.php
├── config/database.php
├── .env
├── artisan
├── composer.json
└── package.json
```

---

## ⚡ Alur Kerja Aplikasi (Ringkas)

1. User register (nama, email, password, daya_va).
2. User login dan masuk dashboard.
3. User input perangkat (watt, jumlah unit).
4. User input analisis pemakaian (jam pemakaian per tanggal).
5. Sistem menghitung kWh, biaya, emisi CO₂ → simpan ke database.
6. Dashboard menampilkan total, grafik 7 hari, status hemat/sedang/boros.
7. Alert tampil jika pemakaian hari ini melebihi batas berdasarkan VA.

---

## 💾 Database Schema (Inti)

- **users**: data user + daya_va  
- **devices**: perangkat elektronik milik user  
- **monitoring_logs**: catatan pemakaian harian  
- **billings**: estimasi biaya listrik  
- **co2_impacts**: emisi CO₂  
- **recommendations**: tips hemat energi  

---

## 🔌 API Endpoints (Web)

| Method | Path | Fungsi |
|-------|------|--------|
| GET | / | Landing page |
| GET | /login | Form login |
| POST | /login | Proses login |
| GET | /register | Form register |
| POST | /register | Proses register |
| POST | /logout | Logout |
| GET | /dashboard | Dashboard |
| GET | /devices | List perangkat |
| GET | /devices/create | Form tambah perangkat |
| POST | /devices | Simpan perangkat |
| GET | /devices/{id}/edit | Form edit perangkat |
| PUT | /devices/{id} | Update perangkat |
| DELETE | /devices/{id} | Hapus perangkat |
| GET | /analysis/input | Form input analisis |
| POST | /analysis | Simpan analisis |
| GET | /history | Riwayat pemakaian |
| GET | /recommendations | Rekomendasi hemat |
| GET | /profile | Form profil |
| PUT | /profile | Update profil |

---

## 📊 Alert Pemakaian Harian (Berdasarkan VA)

| Golongan VA | 🟢 Hemat | 🟡 Sedang | 🔴 Boros |
|-----------|---------|---------|---------|
| 450–900 VA | < 1,5 kWh | 1,5 – 2,5 kWh | > 2,5 kWh |
| 1.300–2.200 VA | < 2,5 kWh | 2,5 – 4,0 kWh | > 4,0 kWh |
| 3.500–5.500 VA | < 4,0 kWh | 4,0 – 8,0 kWh | > 8,0 kWh |
| ≥ 6.600 VA | < 7,0 kWh | 7,0 – 12,0 kWh | > 12,0 kWh |

---

## 🚀 Panduan Instalasi (Laravel + XAMPP)

1) **Install dependency**
```bash
composer install
npm install
```

2) **Set ENV**
```bash
copy .env.example .env
php artisan key:generate
```

3) **Atur database (.env)**
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wattcare
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

4) **Jalankan migration**
```bash
php artisan migrate
```

5) **Run assets & server**
```bash
npm run dev
php artisan serve
```

Akses: `http://localhost:8000`

---

## 📄 Catatan Penting

- Sistem hanya memiliki **role User**.
- Alert harian berbasis **VA user** dan **kWh harian**.
- Emisi CO₂ menggunakan faktor **0,89 kg CO₂/kWh**.
- Grafik dashboard menampilkan 7 hari terakhir.

---
