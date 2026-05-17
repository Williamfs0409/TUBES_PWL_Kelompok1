# CityZen

**CityZen** adalah platform web berbasis komunitas untuk membantu masyarakat menemukan, mengevaluasi, dan melaporkan kondisi ruang publik secara kolaboratif.

> Dari Warga, Untuk Kota yang Lebih Baik.

## Project Overview

CityZen mendukung tujuan **SDG 11: Sustainable Cities and Communities** dengan menyediakan platform crowdsourced public space. Pengguna dapat membagikan informasi tempat publik, memberi review, menyimpan tempat favorit, dan melaporkan kondisi fasilitas publik.

## Core Features

- Landing page CityZen
- Authentication system
- Social feed ruang publik
- Tambah tempat publik
- Detail tempat publik
- Rating dan review
- Like dan bookmark
- Report kondisi tempat
- User profile
- Gamification badge
- Search dan discovery
- Admin moderation
- Analytics dashboard

## Current Tech Stack

Repository ini saat ini menggunakan setup Laravel:

- Laravel 12
- PHP 8.2
- Vite
- Tailwind CSS
- SQLite untuk development lokal

Target pengembangan lengkap dapat dilihat di dokumen PRD.

## Documentation

- [Product Requirements Document](docs/PRD.md)

## Local Setup

Clone repository:

```bash
git clone https://github.com/Williamfs0409/TUBES_PWL_Kelompok1.git
cd TUBES_PWL_Kelompok1
```

Install dependency:

```bash
composer install
npm install
```

Siapkan environment:

```bash
cp .env.example .env
php artisan key:generate
```

Jalankan migrasi:

```bash
php artisan migrate
```

Jalankan aplikasi:

```bash
php artisan serve
npm run dev
```

## Git Workflow

- Gunakan `main` hanya untuk versi stabil.
- Buat branch baru untuk setiap fitur.
- Kerjakan perubahan di branch fitur.
- Push branch ke GitHub.
- Buat Pull Request ke `main`.
- Merge setelah review.

Contoh:

```bash
git checkout main
git pull origin main
git checkout -b fitur/login
```

## Team Notes

Jangan commit file berikut:

- `.env`
- `vendor/`
- `node_modules/`
- file credential atau token pribadi

## License

Project ini dibuat untuk kebutuhan tugas pengembangan aplikasi web.
