# SiPKL — Sistem Informasi Praktek Kerja Lapangan
## SMK ITIKURIH HIBARNA Ciparay

Aplikasi web berbasis **CodeIgniter 3** untuk manajemen Praktik Kerja Lapangan (PKL). Mendukung tiga peran pengguna: **Siswa**, **Hubin**, dan **Pembimbing**. Dilengkapi dengan sistem presensi berbasis GPS, logbook harian, validasi otomatis berbasis aturan (Rule-Based Validation), dan monitoring real-time.

---

## Informasi Sekolah

| | |
|---|---|
| **Nama** | SMK ITIKURIH HIBARNA Ciparay |
| **Alamat** | Jalan Raya Laswi No. 782 Ciparay Kab. Bandung [40381] |
| **Telpon** | 022-5957900 |
| **Email** | smk@itikurih-hibarna.sch.id |

---

## Persyaratan Sistem

- PHP 7.4+ (diuji pada PHP 8.2)
- MySQL / MariaDB 5.6+
- Apache dengan `mod_rewrite` aktif
- XAMPP (disarankan)
- Composer
- Ekstensi PHP: zip (ZipArchive) — diperlukan oleh PhpSpreadsheet untuk impor/ekspor Excel

---

## Instalasi

### 1. Letakkan project di XAMPP

```
c:\xampp\htdocs\sipkl\
```

### 2. Install dependencies

```bash
composer install
```

> Jika Composer belum terinstall, unduh dari https://getcomposer.org/Composer-Setup.exe  
> Pilih PHP dari XAMPP: `C:\xampp\php\php.exe`

### 3. Import database

- Buka **phpMyAdmin**: `http://localhost/phpmyadmin`
- Tab **Import** → pilih file `db_sipkl.sql` (ada di root project)
- Klik **Go**

File `db_sipkl.sql` adalah **satu-satunya file database** yang dibutuhkan. Sudah mencakup semua tabel, data awal, foreign key, dan index.

Jika database SiPKL sudah terpasang sebelum fitur koordinat perusahaan lain digunakan, jalankan SQL berikut melalui phpMyAdmin:

```sql
ALTER TABLE tb_siswa
  ADD COLUMN other_dudi_latitude DECIMAL(10,8) DEFAULT NULL,
  ADD COLUMN other_dudi_longitude DECIMAL(11,8) DEFAULT NULL;
```

### 4. Konfigurasi database

Edit `application/config/database.php`:

```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',          // sesuaikan dengan password MySQL Anda
'database' => 'db_sipkl',
```

### 5. Buat folder upload

Pastikan folder berikut ada dan dapat ditulis (chmod 777):

```
uploads/profil/
uploads/import/
uploads/presensi/
uploads/logbook/
uploads/pengajuan/
```

### 6. Jalankan aplikasi

- Start Apache & MySQL di XAMPP
- Siswa  : `http://localhost/sipkl/`
- Hubin  : `http://localhost/sipkl/login/hubin`
- Pembimbing : `http://localhost/sipkl/login/pembimbing`

---

## Kredensial Default

| Peran | Username | Password |
|---|---|---|
| Hubin / Admin | `admin` | `password` |
| Pembimbing | `guru_1` | `password` |
| Siswa | `siswa` | `password` |

> Hubin dapat menambah dan mengelola user melalui menu **Data User**.

---

## Fitur per Peran

## FITUR DAN TEKNOLOGI YANG DIGUNAKAN

Aplikasi dikembangkan sebagai sistem monitoring PKL yang terintegrasi dan mendukung:

1. Sistem menggunakan GPS real-time untuk membantu memverifikasi lokasi siswa saat melakukan presensi PKL.
2. Sistem melakukan tracking lokasi siswa selama aktivitas PKL sesuai dengan kebutuhan monitoring.
3. Sistem menggunakan Artificial Intelligence (AI) untuk membantu proses analisis data monitoring PKL.
4. Sistem dapat menggunakan Machine Learning (ML) apabila diperlukan untuk melakukan analisis atau prediksi berdasarkan data PKL.
5. Sistem menyediakan penilaian otomatis terhadap kinerja siswa berdasarkan indikator dan data yang telah ditentukan.
6. Sistem menyediakan rekomendasi otomatis berdasarkan hasil monitoring, presensi, logbook, dan penilaian siswa.
7. Sistem dapat melakukan verifikasi kesesuaian lokasi siswa dengan lokasi instansi PKL melalui data GPS.
8. Monitoring kehadiran dilakukan dengan memanfaatkan data presensi, waktu, dokumentasi, dan lokasi siswa.
9. Fokus penelitian mencakup monitoring PKL, validasi data, verifikasi lokasi, analisis data, dan evaluasi aktivitas siswa.
10. Rule-Based Validation tetap menjadi komponen utama untuk melakukan validasi terhadap data presensi, logbook, dan data monitoring.
11. GPS, AI, dan Machine Learning digunakan sebagai komponen pendukung sesuai kebutuhan sistem dan harus memiliki fungsi yang jelas dalam proses monitoring.
12. Setiap teknologi yang digunakan harus memiliki hubungan yang jelas dengan masalah penelitian dan tujuan penelitian.
13. Jangan menambahkan teknologi hanya untuk membuat aplikasi terlihat lebih canggih. Setiap teknologi harus memiliki fungsi yang dapat dijelaskan secara akademis dan dapat diuji.


### Siswa (Level 3)

- Pengajuan PKL: pilih DUDI, isi data, kirim ke Hubin
- Pantau status pengajuan (draft → menunggu → disetujui / ditolak)
- Ajukan ulang setelah diproses
- Presensi masuk & pulang berbasis GPS (foto + geolocation)
- Presensi dapat dilakukan kembali pada setiap tanggal baru setelah pukul 00.00 selama periode PKL aktif
- Logbook kegiatan harian (foto dokumentasi wajib)
- Cetak ID Card PKL dengan QR Code setelah disetujui
- Lihat pengumuman dari Hubin
- Kelola profil dan ubah password

**Alur Siswa:**
```
Daftar / Login → Buat Pengajuan → Tunggu Persetujuan Hubin
→ Setelah disetujui: Presensi harian + Logbook harian
→ Cetak ID Card PKL
```

### Hubin / Admin (Level 1)

- Manajemen user (siswa, pembimbing, hubin)
- Manajemen DUDI (tambah/edit perusahaan + koordinat GPS)
- Review, approve, dan tolak pengajuan PKL
- Assign pembimbing ke siswa yang disetujui
- Buat dan cetak ID Card dengan QR Code
- Monitoring presensi global (semua siswa & semua DUDI)
- Monitoring logbook global
- Laporan rekapitulasi nilai
- Buat pengumuman (CRUD)
- Export data ke Excel / CSV (Siswa & Pembimbing)
- Import data siswa dari Excel / CSV
- Import data guru pembimbing dari Excel / CSV
- Dashboard statistik lengkap

### Pembimbing (Level 2)

- Login via portal khusus `/login/pembimbing`
- Dashboard monitoring siswa bimbingan
- Lihat detail presensi harian (jam masuk/pulang, foto, audit GPS)
- Review & beri catatan pada logbook siswa
- Laporan rekapitulasi nilai dan penalti
- Cetak laporan bimbingan

---

## Alur Kerja

### Siklus Harian dan Periode PKL

- Presensi menggunakan tanggal server dan dibatasi satu record per siswa per hari. Setelah pukul 00.00, tanggal berubah sehingga siswa dapat melakukan presensi untuk hari berikutnya.
- Setiap penempatan PKL berlangsung selama 90 hari kalender. Tanggal mulai dan selesai ditetapkan ketika Hubin menyetujui pengajuan.
- Setelah periode berakhir, Hubin dapat membuka dashboard dan memilih **Reset Periode PKL**. Data presensi, logbook, penilaian, dan assignment periode lama diarsipkan, sedangkan akun siswa dan master DUDI tetap dipertahankan.
- Siswa dikembalikan ke status `draft` dan dapat mengajukan penempatan untuk periode berikutnya.

```
SISWA                   HUBIN                   PEMBIMBING
  │                       │                          │
  ├─ Buat Pengajuan ─────>│                          │
  │                       ├─ Review & Approve        │
  │                       ├─ Assign Pembimbing ─────>│
  │                       ├─ Buat ID Card            │
  │<── Notifikasi ────────┤                          │
  │                       │                          │
  ├─ Presensi GPS ────────┤──────────────────────────>│ (monitor)
  ├─ Logbook Harian ──────┤──────────────────────────>│ (review)
  │                       │                          ├─ Beri Catatan
  │                       ├─ Monitoring Global       │
  │                       ├─ Rekap Nilai             │
```

---

## Struktur Database

File: **`db_sipkl.sql`** (satu file, import langsung)

| Tabel | Deskripsi |
|---|---|
| `tb_group` | Level pengguna: 1=Hubin, 2=Pembimbing, 3=Siswa |
| `tb_user` | Akun semua pengguna + identitas lengkap |
| `tb_pembimbing` | Data profil guru pembimbing |
| `tb_dudi` | Data perusahaan / instansi PKL + koordinat GPS |
| `tb_siswa` | Data siswa + status & detail pengajuan PKL |
| `tb_pengelompokan` | Relasi siswa ↔ pembimbing |
| `tb_pengumuman` | Pengumuman dari Hubin |
| `tb_presensi` | Presensi harian siswa + audit geolocation |
| `tb_logbook` | Logbook kegiatan harian siswa |
| `tb_penilaian` | Rekapitulasi nilai akhir + penalti otomatis |

### Relasi utama

```
tb_group       ←── tb_user.level
tb_user        ←── tb_pembimbing.user_id
tb_user        ←── tb_siswa.user_id
tb_dudi        ←── tb_siswa.dudi_id
tb_pembimbing  ←── tb_siswa.pembimbing_id
tb_pembimbing  ←── tb_pengelompokan.pembimbing_id
tb_siswa       ←── tb_pengelompokan.siswa_id
tb_user        ←── tb_pengumuman.created_by
tb_siswa       ←── tb_presensi.siswa_id
tb_siswa       ←── tb_logbook.siswa_id
tb_siswa       ←── tb_penilaian.siswa_id
tb_pembimbing  ←── tb_penilaian.pembimbing_id
```

---

## Rule-Based Validation (RBV)

Engine validasi terpusat di `application/libraries/Rule_based_validation.php`. Menggunakan **16 aturan bisnis deterministik** untuk memastikan integritas data presensi dan logbook.

| Kode | Aturan | Kondisi Valid |
|---|---|---|
| RBV-001 | Presensi masuk | Belum presensi di tanggal tersebut, status siswa = disetujui |
| RBV-002 | Presensi pulang | Sudah presensi masuk, jam pulang ≥ jam masuk |
| RBV-003 | Parameter presensi lengkap | `siswa_id`, `tanggal`, `status_kehadiran` terisi |
| RBV-004 | Foto presensi wajib | File foto tersedia (jika status = hadir) |
| RBV-005 | Format & ukuran foto presensi | JPG/JPEG/PNG, ≤ 2048 KB |
| RBV-006 | Geolocation radius | Jarak Haversine ≤ radius DUDI |
| RBV-007 | Duplikasi logbook | Maks 1 logbook per siswa per tanggal |
| RBV-008 | Panjang deskripsi | Minimal 20 karakter |
| RBV-009 | Dokumentasi logbook wajib | JPG/JPEG/PNG/GIF/PDF/DOC/DOCX/XLS/XLSX/PPT/PPTX, ≤ 5120 KB |
| RBV-010 | Parameter logbook lengkap | `siswa_id` dan `tanggal` valid |
| RBV-011 | Tanggal logbook | Tidak boleh tanggal di masa depan |
| RBV-012 | Jam masuk | Antara 05:00 – 23:59 |
| RBV-013 | Jam pulang | Setelah jam masuk di hari yang sama |
| RBV-014 | Integritas presensi-logbook | Siswa melakukan presensi masuk yang valid pada tanggal logbook |
| RBV-015 | Hak akses monitoring | Level = Siswa dan status pengajuan = disetujui |
| RBV-016 | Sinkronisasi nilai & penalti | Otomatis update `tb_penilaian` setiap perubahan |

### Formula Geolocation (Haversine)

Jarak dihitung server-side antara koordinat GPS siswa dan koordinat kantor DUDI:

```
a = sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlon/2)
d = 2 × R × arctan2(√a, √(1−a))     [R = 6.371.000 m]
```

### Formula Penilaian (RBV-016)

```
Nilai Kehadiran  = max(0, 100 − (total_alpha × 10))          [bobot 40%]
Nilai Logbook    = max(0, 100 − (logbook_tidak_valid × 5))   [bobot 60%]
Nilai Akhir      = (Nilai Kehadiran × 0.40) + (Nilai Logbook × 0.60)
```

---

## Struktur File Penting

```
sipkl/
├── db_sipkl.sql                        ← Satu-satunya file database
├── composer.json
├── application/
│   ├── config/
│   │   ├── database.php
│   │   └── routes.php
│   ├── controllers/
│   │   ├── Auth.php                    ← Login semua peran
│   │   ├── Home.php                    ← Landing page
│   │   ├── Hubin.php                   ← Panel Hubin (level 1)
│   │   ├── Guru.php                    ← Panel Pembimbing (level 2)
│   │   ├── Siswa.php                   ← Panel Siswa (level 3)
│   │   ├── Export_excel.php            ← Export / Import Excel
│   │   └── Migration_monitoring.php    ← Helper migrasi (dev only)
│   ├── models/
│   │   ├── Login.php
│   │   ├── M_user.php
│   │   ├── M_siswa.php
│   │   ├── M_dudi.php
│   │   ├── M_pembimbing.php
│   │   ├── M_pengelompokan.php
│   │   ├── M_pengumuman.php
│   │   ├── M_presensi.php
│   │   ├── M_logbook.php
│   │   └── M_penilaian.php
│   ├── libraries/
│   │   ├── Qr_generator.php
│   │   └── Rule_based_validation.php   ← Engine RBV-001 s/d RBV-016
│   └── views/
│       ├── landing/
│       ├── authentication/
│       ├── hubin/
│       ├── guru/                       ← Portal pembimbing
│       └── siswa/
└── uploads/
    ├── profil/
    ├── import/
    ├── presensi/
    ├── logbook/
    └── pengajuan/
```

---

## Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | CodeIgniter 3 |
| Frontend | Bootstrap 5, Chart.js, AOS |
| Database | MySQL / MariaDB |
| Excel | PhpSpreadsheet |
| QR Code | Library Qr_generator (internal) |
| Geolocation | Browser Geolocation API + Haversine (server-side) |
| Keamanan | bcrypt, session-based auth, IDOR protection |

---

## Import / Export Excel

### Import Siswa

1. Login sebagai Hubin → menu **Data Siswa** → **Import Data** (atau URL `hubin/view/import-siswa`)
2. Download template resmi (`template-import-siswa-YYYYMMDD.xlsx`)
3. Format file Excel / CSV (8 kolom utama):

| # | Kolom | Status | Keterangan |
|---|---|---|---|
| 1 | Nama Siswa | Wajib | Nama lengkap siswa |
| 2 | NIS | Opsional | Nomor Induk Siswa |
| 3 | NISN | Opsional | Nomor Induk Siswa Nasional |
| 4 | Jenis Kelamin | Opsional | L (Laki-laki) / P (Perempuan) |
| 5 | Kelas | Wajib | Contoh: XI TKJ 1 |
| 6 | Jurusan | Opsional | Contoh: Rekayasa Perangkat Lunak |
| 7 | Username | Wajib | Username login siswa (unik) |
| 8 | Password | Wajib | Password login (min. 6 karakter) |

### Import Pembimbing

1. Login sebagai Hubin → menu **Data Pembimbing** → **Import Pembimbing** (atau URL `hubin/view/import-pembimbing`)
2. Download template resmi (`template-import-pembimbing-YYYYMMDD.xlsx`)
3. Format file Excel / CSV (15 kolom):

| # | Kolom | Status | Keterangan |
|---|---|---|---|
| 1 | Nama Pembimbing | Wajib | Nama lengkap beserta gelar |
| 2 | NIP | Wajib | Nomor Induk Pegawai (unik) |
| 3 | Jenis Kelamin | Opsional | L (Laki-laki) / P (Perempuan) |
| 4 | Email | Opsional | Email aktif pembimbing |
| 5 | No Telepon | Opsional | Nomor telepon / WhatsApp |
| 6 | Alamat | Opsional | Alamat domisili |
| 7 | Tempat Lahir | Opsional | Kota tempat lahir |
| 8 | Tanggal Lahir | Opsional | Format: YYYY-MM-DD |
| 9 | Pendidikan Terakhir | Opsional | S1 / S2 / D3 / dsb |
| 10 | Jabatan | Opsional | Contoh: Guru Pembimbing / Guru Produktif |
| 11 | Jurusan Keahlian | Opsional | Contoh: Rekayasa Perangkat Lunak |
| 12 | Tahun Masuk | Opsional | Tahun mulai bertugas, contoh: 2010 |
| 13 | Status Kepegawaian | Opsional | PNS / GTY / Honorer |
| 14 | Tempat Tugas | Opsional | Contoh: SMK ITIKURIH HIBARNA |
| 15 | Username | Wajib | Username login portal pembimbing (unik) |
| 16 | Password | Wajib | Password login (min. 6 karakter) |

### Export Data

- **Export Siswa**: Hubin → **Data Siswa** → **Export Data** (XLSX / CSV).
- **Export Pembimbing**: Hubin → **Data Pembimbing** → **Export Data** (XLSX).

---

## Troubleshooting

**`vendor/autoload.php` not found**
→ Jalankan `composer install` di root project.

**Tabel tidak ditemukan**
→ Pastikan sudah import `db_sipkl.sql` via phpMyAdmin. File ini sudah mencakup semua tabel.

**Import Excel tidak berfungsi**
→ Pastikan folder `uploads/import/` ada dan writable (chmod 777). Cek ukuran file (maks 5 MB).

**QR Code tidak muncul**
→ Cek library `application/libraries/Qr_generator.php`. Pastikan data siswa lengkap.

**Presensi GPS ditolak**
→ Pastikan koordinat latitude/longitude DUDI sudah diisi di menu **Data DUDI**. Browser harus mengizinkan akses lokasi.

**Login pembimbing gagal**
→ Gunakan URL `/login/pembimbing`. Pastikan user memiliki level = 2 (Pembimbing) di `tb_user`.

**Error log detail**
→ Cek `application/logs/log-YYYY-MM-DD.php`.

---

## Changelog

### v3.1 (2026-08-18) — Import Data Pembimbing & Auto Account Provisioning
- Fitur **Import Data Guru Pembimbing** via file Excel (.xlsx, .xls) dan CSV
- Download template otomatis `template-import-pembimbing-YYYYMMDD.xlsx`
- Otomatis membuat akun login user (`tb_user` level 2) dan biodata pembimbing (`tb_pembimbing`)
- Halaman UI khusus `hubin/view/import-pembimbing` dengan petunjuk format kolom lengkap
- Tombol integrasi di halaman `hubin/view/data-pembimbing`
- Validasi pencegahan duplikasi NIP dan Username server-side

### v3.0 (2026-08-14) — Final Single Database
- Semua tabel digabung menjadi satu file `db_sipkl.sql`
- Hapus file migrasi terpisah (`database_migration_v2.sql`)
- Hapus dokumentasi terpisah (DATABASE_UPDATE.md, DEVELOPMENT_CHANGELOG.md, dll.)

### v2.5 — Monitoring PKL + Rule-Based Validation
- Modul presensi GPS dengan audit geolocation (Haversine)
- Modul logbook kegiatan harian + foto dokumentasi
- Engine Rule-Based Validation 16 aturan (RBV-001 s/d RBV-016)
- Tabel `tb_presensi`, `tb_logbook`, `tb_penilaian`
- Kolom geolocation (`latitude`, `longitude`, `radius`) pada `tb_dudi`
- Portal login khusus pembimbing (`/login/pembimbing`)
- Dashboard monitoring Guru: presensi, logbook, rekap nilai
- Dashboard monitoring Hubin: presensi global, logbook global
- Penilaian otomatis dengan penalti −5 per logbook tidak valid
- IDOR protection: guru hanya bisa review siswa bimbingannya sendiri
- Koordinat GPS diverifikasi ulang server-side (tidak bisa dimanipulasi)

### v2.3 — Identitas Lengkap & Single Source Database
- Field identitas lengkap pada `tb_user` (email, telepon, alamat, dll.)
- Biodata lengkap pembimbing (pendidikan, jabatan, jurusan keahlian, dll.)
- Interface tab navigasi untuk manajemen identitas
- Migrasi database dipindahkan ke `db_sipkl.sql` sebagai satu sumber kebenaran

### v2.0 — QR Code & Pagination
- Sistem QR Code pada ID Card PKL
- Pagination semua tabel data
- Fitur pencarian dan filter
- Export / Import Excel dengan PhpSpreadsheet

### v1.8 — Pemisahan Tabel & Pengelompokan
- Tabel `tb_pembimbing` dan `tb_pengelompokan` dipisahkan
- Auto-create data siswa/pembimbing saat tambah user
- Sistem assign pembimbing (maks 10 siswa per pembimbing)

---

## Lisensi

© 2026 SMK ITIKURIH HIBARNA Ciparay — All Rights Reserved

Dikembangkan sebagai Sistem Informasi Praktek Kerja Lapangan (SiPKL) v3.0
#   P r o j e c t - S k r i p s i  
 