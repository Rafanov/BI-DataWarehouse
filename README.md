# 🌊 OceanBI — Ocean Plastic Intelligence System

Sistem Business Intelligence berbasis web untuk analisis dan visualisasi data polusi plastik laut secara global. Dibangun sebagai proyek UAS Business Intelligence, Kelompok 6, Program Studi Sistem Informasi, Universitas Mulawarman 2026.

---

## 📋 Deskripsi

OceanBI menggabungkan dua modul utama:

- **MIS Dashboard** — Management Information System untuk monitoring tren produksi plastik, komposisi pengelolaan sampah, dan pemetaan polusi laut secara global (Globe 3D + Atlas 2D).
- **DSS Risk Analysis** — Decision Support System untuk analisis risiko multi-variabel per negara, distribusi kategori risiko, dan rekomendasi prioritas mitigasi.

Selain data ocean plastic statis, user juga bisa upload dataset CSV sendiri dan mendapatkan insight otomatis via **Gemini AI**.

---

## ✨ Fitur Utama

- 🌐 **Globe 3D Interaktif** — visualisasi Three.js dengan marker per negara berdasarkan indikator yang dipilih
- 🗺️ **Atlas 2D** — peta choropleth berbasis D3.js + TopoJSON
- 📊 **MIS Dashboard** — tren produksi plastik (1950–2019), komposisi waste fate, top 10 mismanaged per kapita
- ⚠️ **DSS Risk Analysis** — distribusi risk category, multivariate analysis, profil negara prioritas dengan radar chart
- 📁 **Dataset Manager** — upload CSV, preview data, metadata otomatis (row count, column count)
- 🤖 **AI Insight** — generate insight & chart config otomatis via Gemini API per dataset
- 🔐 **Auth** — register, login, logout dengan session management

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12 (PHP) |
| Database utama | SQLite |
| Database BI | SQLite (`ocean_plastic_dw.db`) |
| Frontend | Blade, Vanilla JS, Canvas API |
| Visualisasi | Three.js (Globe 3D), D3.js + TopoJSON (Atlas) |
| CSV Parsing | `league/csv` |
| AI Insight | Google Gemini API (`gemini-2.0-flash`) |
| Styling | CSS Custom Properties (ocean dark theme) |
| Auth | Laravel Breeze (session-based) |

---

## 🚀 Instalasi & Setup

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js & npm (opsional, untuk build assets)

### Langkah

```bash
# 1. Clone repo
git clone <repo-url>
cd ocean-bi

# 2. Install dependencies
composer install

# 3. Copy env dan generate key
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi .env
# Set DB_CONNECTION=sqlite (default)
# Tambahkan GEMINI_API_KEY untuk fitur AI Insight

# 5. Migrasi database
php artisan migrate

# 6. (Opsional) Seed user default
php artisan db:seed

# 7. Jalankan server
php artisan serve
```

### Database BI

Pastikan file `ocean_plastic_dw.db` ada di folder `database/`:

```
database/
├── database.sqlite       ← dibuat otomatis saat migrate
└── ocean_plastic_dw.db   ← taruh di sini (file terpisah)
```

File `ocean_plastic_dw.db` berisi data warehouse polusi plastik laut (tabel: `fact_production_trend`, `fact_waste_fate`, `fact_ocean_pollution`, `dim_country`, `dim_risk`).

---

## ⚙️ Konfigurasi `.env`

```env
APP_NAME=OceanBI
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite

# Untuk fitur AI Insight (opsional)
GEMINI_API_KEY=your_gemini_api_key_here

# Session & Cache (default database)
SESSION_DRIVER=database
CACHE_STORE=database
```

---

## 📁 Struktur Direktori Penting

```
app/
├── Http/Controllers/
│   ├── DashboardController.php     # Dashboard overview + stats
│   ├── DatasetController.php       # Upload, preview, AI insight CSV
│   ├── ChartController.php         # Chart config & data API
│   └── OceanBIController.php       # MIS & DSS API endpoints
database/
├── migrations/                     # Schema users, datasets, charts
└── ocean_plastic_dw.db             # Data warehouse (taruh manual)
resources/views/
├── layouts/app.blade.php           # Layout utama + sidebar
├── layouts/guest.blade.php         # Layout login/register (underwater canvas)
├── dashboard.blade.php             # Dashboard CSV datasets
└── ocean/
    ├── mis.blade.php               # MIS Dashboard
    └── dss.blade.php               # DSS Risk Analysis
routes/web.php                      # Route auth + dashboard + ocean BI
```

---

## 🔌 API Endpoints

Semua endpoint ocean BI tersedia tanpa auth (untuk kebutuhan fetch dari frontend):

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/ocean/kpi` | KPI summary (produksi, recycling rate, top polluter) |
| GET | `/api/ocean/production` | Tren produksi plastik 1950–2019 |
| GET | `/api/ocean/waste-fate` | Komposisi pengelolaan sampah per entitas |
| GET | `/api/ocean/top-mismanaged` | Top 10 mismanaged per kapita |
| GET | `/api/ocean/geo?indicator=X` | Data geografis per indikator |
| GET | `/api/ocean/risk-dist` | Distribusi kategori risiko |
| GET | `/api/ocean/multivariate` | Analisis multivariat top 20 negara |
| GET | `/api/ocean/top-ocean` | Top 10 kontributor polusi laut |
| GET | `/api/ocean/priority` | 15 negara prioritas mitigasi |

Parameter `indicator` untuk `/geo`: `ocean_pollution_share` | `mismanaged_per_capita` | `recycled_share`

---

## 👥 Tim Pengembang

Kelompok 6 — UAS Business Intelligence  
1. Zyrus Alfredo Randan Malinggato - 2409116120
2. Jen Agresia Misti - 2409116007
3. Raihan Fariz N - 2409116083
Program Studi Sistem Informasi, Universitas Mulawarman 2026

---

## 📄 Lisensi

MIT License
