# 🌾 Aplikasi Kasir Toko Pertanian - Sinar Jaya

Aplikasi Point of Sale (POS) lengkap untuk Toko Pertanian dengan Laravel Backend API dan Vue 3 Frontend SPA.

## 🎯 Fitur Utama

✅ **Authentication** - Login/Logout dengan Laravel Sanctum  
✅ **Dashboard** - Statistik penjualan, grafik, produk terlaris  
✅ **Point of Sale (POS)** - Kasir dengan scan barcode, keranjang, cetak struk  
✅ **Manajemen Produk** - CRUD produk, kategori, stok, harga, gambar  
✅ **Manajemen Kategori** - Kategori produk pertanian  
✅ **Transaksi** - Riwayat, detail, filter, pencarian  
✅ **Laporan** - Stok menipis, produk terlaris, penjualan  

## 🛠️ Teknologi

### Backend
- Laravel 11
- MySQL/SQLite
- Laravel Sanctum (API Authentication)
- RESTful API

### Frontend
- Vue 3 (Composition API)
- Vite
- Pinia (State Management)
- Vue Router
- Axios
- Tailwind CSS
- Chart.js

## 🚀 Quick Start

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js >= 18.x
- MySQL/SQLite

### Instalasi Backend

```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database (pilih SQLite atau MySQL di .env)
php artisan migrate
php artisan db:seed

# Link storage
php artisan storage:link

# Run server
php artisan serve
# Backend: http://localhost:8000
```

### Instalasi Frontend

```bash
# Masuk ke folder frontend
cd frontend

# Install dependencies
npm install

# Run dev server
npm run dev
# Frontend: http://localhost:5173
```

### Login Default

**Email:** `admin@kasir.com`  
**Password:** `password123`

## 📚 Dokumentasi Lengkap

- **[INSTALASI.md](INSTALASI.md)** - Panduan instalasi step-by-step
- **[DOKUMENTASI.md](DOKUMENTASI.md)** - API endpoints, database schema, fitur lengkap
- **[WIREFRAME.md](WIREFRAME.md)** - Wireframe UI semua halaman

## 📁 Struktur Proyek

```
Kasir-Sinarjaya/
├── app/
│   ├── Http/Controllers/API/     # API Controllers
│   └── Models/                   # Eloquent Models
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Data seeders
├── routes/
│   └── api.php                   # API routes
└── frontend/
    ├── src/
    │   ├── api/                  # Axios config
    │   ├── layouts/              # Layout components
    │   ├── router/               # Vue Router
    │   ├── stores/               # Pinia stores
    │   └── views/                # Vue components
    ├── package.json
    └── vite.config.js
```

## 🗃️ Database

### Tables
- **users** - User authentication
- **categories** - Product categories
- **products** - Products with stock, price, barcode
- **transactions** - Sales transactions
- **transaction_items** - Transaction details

### Seeder Data
- 3 Users (Admin, Kasir 1, Kasir 2)
- 8 Categories (Pupuk, Benih, Pestisida, dll)
- 25 Products (Produk pertanian lengkap)

## 🔒 Security

- API Authentication dengan Laravel Sanctum
- Token-based authentication
- Protected routes
- CSRF Protection
- Input validation

## 📝 License

Aplikasi ini dibuat untuk keperluan edukasi dan pembelajaran.

---

**Happy Coding! 🚀**

Untuk bantuan lengkap, lihat:
- `INSTALASI.md` - Panduan instalasi
- `DOKUMENTASI.md` - Dokumentasi API & fitur
- `WIREFRAME.md` - Wireframe UI


Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
