# PRODUCT REQUIREMENTS DOCUMENT (PRD)

# 🌿 CityZen

## Crowdsourced Public Space Platform

---

# 📌 1. Project Overview

## 1.1 Project Name

**CityZen**

## 1.2 Tagline

> “Dari Warga, Untuk Kota yang Lebih Baik.”

## 1.3 Platform Type

Responsive Web Application

## 1.4 SDGs Alignment

CityZen mendukung tujuan SDG 11 dari United Nations:

> **Sustainable Cities and Communities**

Fokus utama platform adalah membantu masyarakat berpartisipasi dalam monitoring dan peningkatan kualitas ruang publik secara kolaboratif.

---

# 🌆 2. Background

Perkembangan kota modern meningkatkan kebutuhan masyarakat terhadap ruang publik yang nyaman, aman, inklusif, dan mudah diakses. Namun, informasi mengenai kondisi ruang publik masih tersebar dan belum terintegrasi secara efektif dalam satu platform digital.

Selain itu, partisipasi masyarakat dalam memberikan penilaian, review, maupun laporan terhadap kondisi ruang publik masih terbatas, padahal kontribusi warga dapat membantu meningkatkan kualitas lingkungan kota secara langsung.

CityZen hadir sebagai platform crowdsourced public space berbasis komunitas yang memungkinkan pengguna untuk:

* menemukan ruang publik,
* membagikan pengalaman,
* memberikan review,
* menyimpan tempat favorit,
* serta melaporkan kondisi fasilitas publik.

Dengan pendekatan digital community engagement, CityZen diharapkan dapat mendukung terciptanya kota yang lebih nyaman, transparan, dan berkelanjutan.

---

# 🎯 3. Project Goals

## 3.1 Main Goal

Membangun platform digital berbasis komunitas untuk membantu masyarakat menemukan, mengevaluasi, dan menjaga kualitas ruang publik secara kolaboratif.

---

## 3.2 Specific Goals

### 1. Centralized Public Space Information

Menyediakan informasi ruang publik secara terpusat dan mudah diakses.

### 2. Community Participation

Meningkatkan partisipasi masyarakat melalui sistem kontribusi komunitas.

### 3. Public Space Evaluation

Mengumpulkan data pengalaman pengguna terhadap kualitas tempat publik.

### 4. Sustainable City Support

Mendukung implementasi konsep smart city dan SDG 11.

### 5. Digital Civic Engagement

Mendorong keterlibatan masyarakat dalam menjaga fasilitas kota secara digital.

---

# 👥 4. Target Users

Target pengguna utama CityZen:

| User Type       | Description                                |
| --------------- | ------------------------------------------ |
| Masyarakat Umum | Pengguna ruang publik sehari-hari          |
| Mahasiswa       | Pengguna aktif ruang komunitas dan edukasi |
| Wisatawan Lokal | Mencari tempat publik dan wisata           |
| Komunitas Kota  | Organisasi sosial dan komunitas lokal      |
| Urban Explorer  | Pengguna aktif eksplorasi kota             |

---

# 🧠 5. System Concept

CityZen menggunakan konsep:

* Community-based platform
* Crowdsourced information system
* Social feed interaction
* Public space monitoring
* Civic engagement platform
* Sustainable city ecosystem

Pengguna dapat berkontribusi langsung terhadap data ruang publik melalui sistem posting dan interaksi sosial modern.

---

# 🏗️ 6. System Architecture

```txt
Frontend (Next.js + Tailwind CSS)
            ↓
REST API Layer (Laravel)
            ↓
Authentication & Middleware
            ↓
MySQL Database
            ↓
Image Storage & CDN
```

---

## 6.1 Technology Stack

### Frontend

* Next.js
* Tailwind CSS
* Axios
* Framer Motion

### Backend

* Laravel
* Laravel Breeze Authentication
* REST API

### Database

* MySQL

### Deployment

* VPS / Cloud Hosting

### Storage

* Cloud image storage

---

# 🎨 7. Design System

## Design Style

* Eco minimalist
* Modern social feed
* Clean UI
* Responsive layout
* Neobrutalism accent

## UI Inspirations

* Threads
* Pinterest
* Modern SaaS dashboard

## Dominant Colors

| Color        | Purpose               |
| ------------ | --------------------- |
| Green        | Sustainability        |
| White        | Clean interface       |
| Earth Tone   | Natural atmosphere    |
| Black Accent | Neobrutalism emphasis |

---

# 👤 8. User Roles

---

## 8.1 User

Pengguna umum platform.

### User Permissions

* Register & login
* Melihat feed tempat
* Menambahkan tempat baru
* Upload foto
* Like postingan
* Review & rating tempat
* Bookmark tempat
* Report kondisi tempat
* Mengelola profil

---

## 8.2 Admin

Bertugas melakukan moderasi sistem.

### Admin Permissions

* Memverifikasi laporan
* Menghapus postingan melanggar
* Menghapus laporan palsu
* Suspend pengguna
* Monitoring aktivitas platform

---

## 8.3 Superadmin

Memiliki akses penuh sistem.

### Superadmin Permissions

* Mengelola admin
* Mengelola kategori
* Mengakses analytics
* Mengelola seluruh data platform
* Monitoring sistem keseluruhan

---

# 🌟 9. Core Features

---

# 9.1 Landing Page

Halaman awal platform sebelum login.

## Contents

* Hero section
* Penjelasan CityZen
* Informasi SDG 11
* Feature highlights
* Team section
* CTA login/register

---

# 9.2 Authentication System

Menggunakan Laravel Breeze Authentication.

## Features

* Register
* Login
* Logout
* Session authentication
* Password hashing
* Middleware protection

---

# 9.3 Social Feed Dashboard

Dashboard utama berbentuk infinite social feed.

## Feed Information

* Nama tempat
* Gallery foto
* Deskripsi singkat
* Kategori
* Jumlah like
* Rating
* User uploader
* Waktu posting

## Feed Features

* Infinite scroll
* Responsive card layout
* Like interaction
* Bookmark interaction
* Share support

---

# 9.4 Add Public Place

Pengguna dapat menambahkan tempat publik baru.

## Input Data

* Nama tempat
* Deskripsi
* Kategori
* Multi image upload
* Google Maps link
* Alamat lokasi

## Categories

* Taman Kota
* Wisata
* Kuliner
* Fasilitas Umum
* Tempat Olahraga
* Ruang Komunitas
* Edukasi
* Transportasi Publik
* Lainnya

---

# 9.5 Place Detail Page

Halaman detail tempat publik.

## Contents

* Full image gallery
* Detail description
* Embedded map
* Rating summary
* User reviews
* Report history
* Bookmark button

---

# 9.6 Rating & Review System

Pengguna dapat memberikan penilaian tempat.

## Review Features

* Star rating
* Text review
* Edit review
* Delete review

## Rules

* Satu user hanya dapat membuat satu review per tempat.

---

# 9.7 Like System

Pengguna dapat memberikan like pada postingan.

## Functions

* Menentukan popularitas tempat
* Menghitung kontribusi user
* Mendukung gamifikasi platform

---

# 9.8 Bookmark System

Menyimpan tempat favorit pengguna.

## Functions

* Quick access tempat favorit
* Personal collection
* Future revisit planning

---

# 9.9 Report System

Pengguna dapat melaporkan kondisi tempat.

## Report Categories

* Sampah
* Kerusakan fasilitas
* Keamanan
* Aksesibilitas
* Vandalisme
* Lainnya

---

## Report Flow

1. User mengirim laporan
2. Sistem menyimpan laporan
3. Admin menerima notifikasi
4. Admin melakukan verifikasi
5. Sistem memperbarui status laporan

---

## Report Status

| Status   | Description             |
| -------- | ----------------------- |
| Pending  | Menunggu verifikasi     |
| Verified | Laporan valid           |
| Rejected | Laporan tidak valid     |
| Resolved | Masalah telah ditangani |

---

## Admin Actions

* Memberikan warning
* Menghapus postingan
* Menghapus laporan
* Suspend user

Postingan tetap tampil publik sampai admin melakukan tindakan.

---

# 9.10 User Profile

Setiap pengguna memiliki halaman profil.

## Profile Information

* Foto profil
* Username
* Bio
* Jumlah kontribusi
* Badge level
* Postingan user
* Bookmark collection

---

# 9.11 Gamification System

Sistem penghargaan kontribusi komunitas.

| Badge       | Requirement             |
| ----------- | ----------------------- |
| Explorer    | 5 posting               |
| Contributor | 20 kontribusi           |
| Guardian    | 10 laporan valid        |
| City Hero   | Top contributor bulanan |

---

# 9.12 Search & Discovery

Sistem pencarian tempat publik.

## Search By

* Nama tempat
* Kategori
* Lokasi
* Rating
* Popularitas

## Filters

* Terbaru
* Rating tertinggi
* Paling populer
* Paling banyak direview

---

# 9.13 Notification System

## Notifications

User mendapatkan notifikasi ketika:

* posting mendapat like,
* laporan diverifikasi,
* memperoleh badge,
* review mendapat balasan.

---

# 9.14 Analytics Dashboard

Dashboard analytics untuk admin dan superadmin.

## Analytics Data

* User aktif
* Jumlah laporan
* Tempat populer
* Aktivitas komunitas
* Kategori paling aktif
* Growth kontribusi

---

# 🗄️ 10. Database Design Overview

## Main Entities

```txt
users
profiles
places
categories
reviews
ratings
likes
bookmarks
reports
report_photos
notifications
badges
```

---

## Relationship Overview

```txt
users
 ├── profiles
 ├── places
 ├── reviews
 ├── bookmarks
 ├── reports
 └── notifications

places
 ├── categories
 ├── reviews
 ├── likes
 ├── reports
 └── bookmarks
```

---

# 🔌 11. API Overview

| Method | Endpoint         | Function      |
| ------ | ---------------- | ------------- |
| POST   | /api/register    | Register      |
| POST   | /api/login       | Login         |
| GET    | /api/places      | Get feed      |
| POST   | /api/places      | Add place     |
| GET    | /api/places/{id} | Place detail  |
| POST   | /api/reviews     | Add review    |
| POST   | /api/reports     | Submit report |
| POST   | /api/bookmarks   | Save bookmark |

---

# 🔄 12. User Flow

---

## 12.1 User Flow

```txt
Landing Page
    ↓
Login/Register
    ↓
Feed Dashboard
    ↓
Explore Places
    ↓
Interaction:
- Like
- Review
- Bookmark
- Report
    ↓
Add Contribution
```

---

## 12.2 Admin Moderation Flow

```txt
Incoming Report
      ↓
Admin Verification
      ↓
Action Decision
      ↓
System Update
```

---

# 📋 13. Functional Requirements

| No | Requirement                         |
| -- | ----------------------------------- |
| 1  | Sistem dapat melakukan autentikasi  |
| 2  | Sistem dapat menampilkan feed       |
| 3  | User dapat menambahkan tempat       |
| 4  | User dapat memberikan like          |
| 5  | User dapat memberikan review        |
| 6  | User dapat bookmark tempat          |
| 7  | User dapat membuat laporan          |
| 8  | Admin dapat memoderasi sistem       |
| 9  | Superadmin dapat mengelola kategori |
| 10 | Sistem memiliki analytics dashboard |

---

# 🛡️ 14. Non Functional Requirements

| Aspect       | Description                    |
| ------------ | ------------------------------ |
| Performance  | Fast loading & optimized image |
| Security     | Authentication & validation    |
| Responsive   | Mobile & desktop support       |
| Scalability  | Support large community data   |
| Usability    | Easy-to-use interface          |
| Availability | Stable online deployment       |

---

# 🔐 15. Security Considerations

## Security Features

* Authentication middleware
* CSRF protection
* Input validation
* Rate limiting
* Secure password hashing
* Spam prevention
* Image upload restriction
* Session management

---

# ⚡ 16. Scalability Strategy

Jika jumlah pengguna meningkat:

* Pagination system
* Lazy loading images
* Database indexing
* CDN integration
* API optimization
* Cloud storage migration
* Caching system

---

# 🚀 17. Future Development

## Future Features

* AI recommendation system
* Smart city heatmap
* Mobile application
* Community leaderboard
* Smart analytics dashboard
* AI moderation assistance

---

# 📈 18. Key Advantages

## 1. Community-Based Platform

Data berasal langsung dari masyarakat.

## 2. Modern Social Feed

Menggunakan interface modern seperti media sosial.

## 3. Sustainable City Support

Mendukung implementasi SDG 11.

## 4. Civic Engagement

Meningkatkan partisipasi masyarakat terhadap lingkungan kota.

## 5. Crowdsourced Monitoring

Monitoring ruang publik dilakukan secara kolaboratif.

---

# 🎯 19. Success Indicators

Project dianggap berhasil jika:

* jumlah user aktif meningkat,
* kontribusi komunitas meningkat,
* laporan valid meningkat,
* database ruang publik berkembang,
* engagement pengguna tinggi.

---

# 🏁 20. Conclusion

CityZen merupakan platform digital berbasis komunitas yang dirancang untuk membantu masyarakat menemukan, membagikan, dan menjaga kualitas ruang publik secara kolaboratif.

Dengan pendekatan crowdsourced public space platform, CityZen menggabungkan konsep social interaction, civic engagement, dan sustainable city monitoring dalam satu ekosistem digital modern.

Melalui fitur feed sosial, review, bookmark, report monitoring, analytics, dan gamifikasi kontribusi, CityZen diharapkan mampu menjadi solusi digital yang mendukung pembangunan kota yang lebih nyaman, inklusif, dan berkelanjutan sesuai dengan tujuan SDG 11.
