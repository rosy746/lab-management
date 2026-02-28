# 🖥️ Lab Management System
**Sistem Informasi Manajemen Laboratorium Komputer**
*Nuris Jember*

---

## 📋 Tentang Proyek

Lab Management System adalah aplikasi web berbasis Laravel untuk mengelola penggunaan laboratorium komputer di lingkungan sekolah. Sistem ini mencakup jadwal lab, booking, kontrol internet via MikroTik, pengumpulan tugas siswa, dan notifikasi WhatsApp otomatis.

---

## ✨ Fitur Utama

### 🗓️ Jadwal & Booking
- Jadwal tetap mingguan per lab
- Booking lab oleh guru (tanpa login) dengan verifikasi nama & HP
- Multi-slot booking (pilih beberapa slot sekaligus)
- Approve booking tunggal atau grup (semua slot sekaligus)
- Notifikasi WA otomatis saat booking disetujui

### 🌐 Kontrol Internet Lab
- Token akses unik per sesi (format: `XXXX-XXXX`)
- Link kontrol dikirim via WhatsApp ke guru
- Hidupkan/matikan internet lab dari HP
- Monitoring perangkat yang terhubung
- Auto-generate token H-5 menit sebelum jadwal rutin
- Auto-invalidate token setelah sesi berakhir

### 📚 Pengumpulan Tugas
- Guru buat tugas dengan token khusus (tanpa login)
- Upload file lampiran soal untuk didownload siswa
- Siswa kumpul tugas tanpa perlu login
- Filter tugas per lembaga & kelas
- Guru beri nilai & feedback

### 📦 Inventaris
- Manajemen inventaris lab
- Laporan bulanan penggunaan lab (Excel/CSV/PDF)

### 👥 Manajemen User
- Role: Admin, Operator, Guru
- Operator dibatasi akses per lab
- Teacher database dengan autocomplete

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 10 |
| Database | MariaDB |
| Frontend | Blade + Tailwind CSS |
| WhatsApp | Baileys (primary) + Fonnte (fallback) |
| MikroTik | PHP Socket API + Python Flask Proxy |
| Server | Ubuntu 24, Apache, Virtualmin |

---

## ⚙️ Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/rosy746/lab-management.git
cd lab-management
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuai konfigurasi server:
```env
APP_URL=https://domain-kamu.com
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=lab_management
DB_USERNAME=root
DB_PASSWORD=your_password

# MikroTik Bot Python
BOT_URL=http://IP_BOT:5000
BOT_WEBHOOK_URL=http://IP_BOT:5000/api/webhook/lab-session

MIKROTIK_HOST=your_mikrotik_ip
MIKROTIK_PORT=8728
MIKROTIK_USERNAME=admin
MIKROTIK_PASSWORD=your_password
```

### 4. Migrasi Database
```bash
php artisan migrate
php artisan db:seed
```

### 5. Storage Link
```bash
php artisan storage:link
```

### 6. Crontab (Scheduler)
```bash
crontab -e
# Tambahkan:
* * * * * cd /path/to/lab-management && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔄 Update Server

```bash
cd /home/maikel/lab-management
git pull
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 📁 Struktur Penting

```
app/
├── Http/Controllers/
│   ├── BookingController.php
│   ├── LabControlController.php
│   ├── ScheduleController.php
│   ├── AssignmentAdminController.php
│   └── AssignmentPublicController.php
├── Models/
│   ├── Booking.php
│   ├── LabSession.php
│   ├── Schedule.php
│   └── Teacher.php
├── Services/
│   └── MikroTikService.php
└── Console/
    └── Kernel.php
```

---

## 🔐 Catatan Keamanan

- File `.env` tidak disertakan di repository
- Kredensial MikroTik disimpan di `.env`
- Token WA disimpan di bot Python terpisah
- Rate limiting aktif pada endpoint booking & tugas

---

## 📞 Kontak

**Nuris Jember** — Sistem Informasi Laboratorium Komputer
