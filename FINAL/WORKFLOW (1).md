# WORKFLOW & SKEMA DATABASE
## SMA NEGERI 6 BANJARMASIN — Sistem Informasi Sekolah
> CI4 + Bootstrap 5 | Versi 3.0 | Semester Genap 2025/2026

---

## DATA SEKOLAH

| Data | Jumlah |
|------|--------|
| Siswa Aktif | 712 siswa (dari tes22.xlsx) |
| Guru Pengajar | 45 guru (dari tes21.xlsx) |
| Kelas | 21 kelas (X: 7, XI: 8, XII: 6) |
| Total Jam/Minggu | 945 jam |

**Kelas:**
- Kelas X (7 kelas): X-1, X-2, X-3, X-4, X-5, X-6, X-7
- Kelas XI (8 kelas): XI-1, XI-2, XI-3, XI-4, XI-5, XI-6, XI-7, XI-8
- Kelas XII (6 kelas): XII-1, XII-2, XII-3, XII-4, XII-5, XII-6

**Mata Pelajaran (17 mapel):**
PAI/BTA, PPKn, B.Indonesia, B.Inggris, Matematika, Fisika, Kimia, Biologi, Ekonomi, Sosiologi, Geografi, Sejarah, Seni Budaya, Penjasorkes, PKWU, Informatika, BK

---

## KONSEP KELAS & MOVING CLASS

> ⚠️ Sistem menggunakan DUA lapisan kelas yang berjalan berdampingan.
> Nama homeroom XII sudah mencerminkan jurusan: **XII MIPA-1, XII MIPA-2, XII MIPA-3, XII IPS-1, XII IPS-2, XII IPS-3**

### Lapisan 1 — Homeroom (Kelas Utama)
Untuk semua tingkat (X, XI, XII). Digunakan untuk: wali kelas, rapor, data pokok siswa, absensi mapel umum/wajib.

### Lapisan 2 — Rombel Moving Class
**Hanya untuk XI dan XII.** Digunakan untuk: absensi mapel peminatan, jadwal peminatan, nilai peminatan.

| Tingkat | Homeroom | Rombel Moving Class | Keterangan |
|---------|----------|---------------------|------------|
| Kelas X | X-1 s/d X-7 | ❌ Tidak ada | Semua mapel di homeroom. Tidak ada peminatan. |
| Kelas XI | XI-1 s/d XI-8 | XI-x BIOLOGI, XI-x FISIKA, XI-x KIMIA, XI-x IPS, dst | Mapel umum → homeroom. Mapel peminatan → rombel. Siswa beda homeroom bisa 1 rombel. |
| Kelas XII | XII-1 s/d XII-6 | XII-x BIOLOGI, XII-x KIMIA, XII-x EKONOMI, dst | **Rombel XII = CLONE dari rombel XI.** Teman sekelas & peminatan SAMA PERSIS dengan rombel XI sebelumnya. |

---

## ROLE PENGGUNA

| Role | Username | Password Default | Akses |
|------|----------|-----------------|-------|
| **DEVELOPER** | Tersimpan di `.env` sebagai `DEVELOPER_KEY` | — | Tidak ada record di DB. Akses penuh + debug panel. |
| **ADMIN** | NIP Kepala Sekolah / Wakasek Kurikulum | — | Kelola semua data + monitoring guru. |
| **GURU** | NUPTK (contoh: `5550768669130060`) | `123123` | Input nilai & absensi, lihat rekap laporan kehadiran. |
| **SISWA** | Nomor Siswa (contoh: `64509133`) | `123123` | Lihat jadwal, nilai, absensi, isi laporan kehadiran guru. |

---

## WORKFLOW PENGEMBANGAN — 5 FASE

### FASE 1 — PERSIAPAN & SETUP LINGKUNGAN

**1. Analisis & Import Data Awal**
- Import data guru (45 guru) dari `tes21.xlsx` → tabel `guru` + `users`
- Import data siswa (712 siswa) dari `tes22.xlsx` → tabel `siswa` + `users`
- Import jadwal pelajaran dari PDF → tabel `jadwal` (kode guru 1–45 → `guru.id`)
- Import data rombel moving class dari file absensi XLS (sheet: `XI-1 BIOLOGI`, `XI-2 FISIKA`, dst)
- Assign siswa ke rombel peminatan berdasarkan data absensi existing

**2. Setup CI4 & Struktur Folder**
```
composer create-project codeigniter4/appstarter sman6_bjm
```
- `.env`: `CI_ENVIRONMENT=development`, `baseURL`, database config, `DEVELOPER_KEY`
- `app/Controllers/{Auth,Admin,Guru,Siswa,Laporan,Rombel}/`
- `app/Models/`
- `app/Views/{admin,guru,siswa,auth}/`
- `app/Filters/`

**3. Setup Database**
```bash
php spark migrate
php spark db:seed GuruSeeder
php spark db:seed SiswaSeeder
php spark db:seed KelasSeeder
php spark db:seed RombelSeeder        # buat rombel moving class dari XLS
php spark db:seed SiswaRombelSeeder   # assign siswa ke rombel
php spark db:seed MapelSeeder
php spark db:seed JadwalSeeder        # jadwal homeroom + rombel
```

---

### FASE 2 — AUTENTIKASI & ROLE BASED ACCESS

**1. Sistem Login**
- Guru: `username = NUPTK`, password default `123123`
- Siswa: `username = nomor_siswa`, password default `123123`
- Admin: `username = NIP` kepala sekolah / wakasek kurikulum
- Simpan role di session: `developer / admin / guru / siswa`

**2. Filter & Middleware**
```php
// app/Filters/
AuthFilter.php    // cek session login
AdminFilter.php
GuruFilter.php
SiswaFilter.php

// Routes
$routes->group('/admin',  ['filter' => 'adminfilter'],  ...);
$routes->group('/guru',   ['filter' => 'gurufilter'],   ...);
$routes->group('/siswa',  ['filter' => 'siswafilter'],  ...);
```
- Developer: key tersembunyi di `.env` → `DEVELOPER_KEY`, tidak ada di tabel `users`

**3. Ganti Password Pertama Kali**
- Kolom `is_first_login` (default `1`) di tabel `users`
- Redirect ke halaman ganti password jika `is_first_login = 1`
- Setelah ganti password → set `is_first_login = 0`

---

### FASE 3 — MODUL AKADEMIK (NILAI & JADWAL)

**1. Data Master**
- Tahun ajaran: `2025/2026` | Semester: `Genap`
- Wali kelas: dari kolom `Wali Kelas` di `tes21.xlsx`

**2. Manajemen Jadwal — 2 Jenis**
- **Jadwal Homeroom**: untuk kelas X semua mapel, dan XI/XII mapel wajib/umum → pakai `kelas_id`
- **Jadwal Rombel**: untuk XI/XII mapel peminatan (moving class) → pakai `rombel_id`
- Tampil otomatis ke Guru sesuai kelas/rombel yang diajar
- Tampil ke Siswa: jadwal homeroom + jadwal rombel peminatannya
- Validasi konflik: 1 guru tidak boleh 2 slot di jam yang sama

**3. Input Nilai & Rapor**
- Guru input: `UH1, UH2, UH3, UTS, UAS, Tugas, Praktik`
- Guru peminatan input nilai di rombel yang menjadi tugasnya
- Rumus nilai akhir dikonfigurasi Admin (% UH, UTS, UAS)
- Siswa & wali kelas bisa lihat dan unduh rapor PDF

---

### FASE 4 — MODUL ABSENSI & LAPORAN KEHADIRAN

**1. Absensi Siswa — 2 Jenis (oleh Guru)**

| Jenis | Kelas | Pakai kolom | Keterangan |
|-------|-------|-------------|------------|
| Absensi Homeroom | X, XI/XII (mapel umum) | `kelas_id` | Guru wali/mapel umum input per kelas |
| Absensi Rombel | XI, XII (moving class) | `rombel_id` | Guru peminatan input per rombel |

- Status: `Hadir / Sakit / Izin / Alpha` + keterangan
- Validasi: 1 kelas/rombel + 1 `jadwal_id` + 1 tanggal = hanya 1 record (bisa diedit)
- Rekap: % kehadiran per bulan, per semester

**2. Laporan Kehadiran Guru oleh Siswa**
- Form muncul otomatis sesuai jadwal hari ini (homeroom + rombel siswa)
- Status: `hadir | tugas | tidak_hadir` + keterangan opsional
- Validasi: `UNIQUE(siswa_id, jadwal_id, tanggal)` — hanya 1x per hari
- Deadline: H+1 pukul 23:59

**3. Monitoring Admin**
- Rekap kehadiran guru per hari/minggu/bulan
- Filter per guru, mapel, kelas, rombel
- Alert: guru tidak hadir ≥ 3x berturut-turut
- Verifikasi: `Terverifikasi` atau `Perlu Klarifikasi`
- Export rekap ke PDF

---

### FASE 5 — TESTING, DEPLOYMENT & MAINTENANCE

**Testing**
- Test login semua role (NUPTK / nomor siswa)
- Test absensi homeroom (kelas X) dan absensi rombel (XI/XII)
- UAT dengan guru & siswa SMAN 6 Banjarmasin
- Security: CSRF, XSS sanitasi input, SQL injection

**Deployment**
```bash
# .env
CI_ENVIRONMENT = production
# Matikan error display, aktifkan HTTPS
# Import data Excel ke database production
```

**Maintenance**
- Arsip data tiap akhir tahun ajaran
- Activity log: siapa mengubah nilai/absensi kapan
- Backup database otomatis setiap malam

---

## SKEMA DATABASE LENGKAP

### `users` — Tabel Autentikasi
```sql
id            INT PK AI
username      VARCHAR(20)       -- NUPTK (guru) atau nomor siswa
password      VARCHAR(255)      -- Bcrypt hash. Default: 123123
role          ENUM('admin','guru','siswa')  -- developer TIDAK di sini
entity_id     INT FK            -- FK ke guru.id atau siswa.id
is_active     TINYINT(1)        -- 1=aktif, 0=nonaktif
is_first_login TINYINT(1)       -- 1=belum ganti password
last_login    DATETIME NULL
created_at    DATETIME
updated_at    DATETIME
```
> Developer: simpan `DEVELOPER_KEY` di `.env` saja, tidak ada record di tabel ini.

---

### `guru` — Data Guru (45 Guru)
```sql
id            INT PK AI
nuptk         VARCHAR(20)       -- contoh: 5550768669130060
nama          VARCHAR(100)      -- contoh: Aya Azmi Hidayah
jenis_kelamin ENUM('L','P')
foto          VARCHAR(255) NULL
created_at    DATETIME
```

---

### `siswa` — Data Siswa (Tidak Pernah Dihapus)
```sql
id            INT PK AI
username      VARCHAR(15)       -- nomor siswa, contoh: 64509133
nisn          VARCHAR(15) NULL
nis           VARCHAR(15) NULL
nama          VARCHAR(100)
jenis_kelamin ENUM('L','P')
tempat_lahir  VARCHAR(50) NULL
tanggal_lahir DATE NULL
nama_ortu     VARCHAR(100) NULL
no_hp_ortu    VARCHAR(15) NULL
kelas_id      INT FK NULL       -- FK ke kelas.id (homeroom). NULL jika lulus/keluar
tahun_masuk   YEAR
tahun_lulus   YEAR NULL         -- diisi saat lulus
status        ENUM('aktif','lulus','pindah','keluar') DEFAULT 'aktif'
foto          VARCHAR(255) NULL
sumber_data   ENUM('dapodik','manual','excel')
created_at    DATETIME
updated_at    DATETIME
```
> Data siswa **TIDAK PERNAH dihapus**. Status diubah jadi `lulus/pindah/keluar`.

---

### `kelas` — 21 Kelas Homeroom Aktif
```sql
id              INT PK AI
nama_kelas      VARCHAR(15)     -- contoh: X-1, XI-3, XII MIPA-1, XII IPS-2
tingkat         ENUM('X','XI','XII')
jurusan         ENUM('MIPA','IPS') NULL
                                -- NULL untuk kelas X dan XI
                                -- MIPA atau IPS untuk kelas XII
                                -- Ditentukan Admin saat proses naik kelas XI→XII
wali_kelas_id   INT FK          -- FK ke guru.id
tahun_ajaran_id INT FK          -- FK ke tahun_ajaran.id
created_at      DATETIME
```
> Nama kelas XII mencerminkan jurusan langsung: **XII MIPA-1, XII MIPA-2, XII MIPA-3, XII IPS-1, XII IPS-2, XII IPS-3**

---

### `rombel` — Rombel Moving Class XI & XII 🆕
```sql
id              INT PK AI
nama_rombel     VARCHAR(30)     -- contoh: XI-1 BIOLOGI, XII-3 KIMIA
tingkat         ENUM('XI','XII')
mapel_id        INT FK          -- FK ke mata_pelajaran.id
guru_id         INT FK          -- FK ke guru.id (guru pengampu)
semester_id     INT FK          -- FK ke semester.id
rombel_asal_id  INT FK NULL     -- FK ke rombel.id
                                -- NULL = rombel XI (baru)
                                -- DIISI = rombel XII (clone dari rombel XI ini)
created_at      DATETIME
```
> - Rombel XI: `rombel_asal_id = NULL`
> - Rombel XII: `rombel_asal_id` diisi → menunjuk rombel XI asalnya
> - Saat proses XI→XII: sistem **clone** rombel XI menjadi rombel XII baru, isi `rombel_asal_id`

---

### `siswa_rombel` — Relasi Siswa ke Rombel 🆕
```sql
id          INT PK AI
siswa_id    INT FK              -- FK ke siswa.id
rombel_id   INT FK              -- FK ke rombel.id
semester_id INT FK              -- FK ke semester.id
UNIQUE KEY  (siswa_id, rombel_id, semester_id)
```
> - 1 siswa bisa di beberapa rombel sekaligus (Biologi + Matematika + dll)
> - Siswa dari homeroom berbeda bisa berada dalam 1 rombel yang sama
> - Saat XI→XII: record ini di-clone ke `semester_id` baru dengan `rombel_id` baru (rombel XII)

---

### `tahun_ajaran`
```sql
id          INT PK AI
nama        VARCHAR(20)         -- contoh: 2025/2026
is_aktif    TINYINT(1)          -- hanya 1 yang aktif
```

### `semester`
```sql
id              INT PK AI
tahun_ajaran_id INT FK
nama_semester   ENUM('Ganjil','Genap')
is_aktif        TINYINT(1)      -- hanya 1 yang aktif
```

---

### `mata_pelajaran` — 17 Mapel
```sql
id          INT PK AI
kode        VARCHAR(10)         -- contoh: MTK, BIO, KIM
nama        VARCHAR(50)         -- contoh: Matematika, Biologi
kelompok    VARCHAR(30)         -- contoh: Wajib A, Wajib B, Peminatan
created_at  DATETIME
```

---

### `jadwal` — Jadwal Pelajaran
```sql
id          INT PK AI
kelas_id    INT FK NULL         -- DIISI untuk kelas X & mapel umum XI/XII
rombel_id   INT FK NULL         -- DIISI untuk moving class XI/XII. NULL jika homeroom
mapel_id    INT FK              -- FK ke mata_pelajaran.id
guru_id     INT FK              -- FK ke guru.id
semester_id INT FK
hari        ENUM('Senin','Selasa','Rabu','Kamis','Jumat')
jam_ke      TINYINT             -- 1 s/d 10
jam_mulai   TIME                -- contoh: 07:30
jam_selesai TIME                -- contoh: 08:15
created_at  DATETIME
```
> **Aturan**: `kelas_id` dan `rombel_id` tidak boleh keduanya NULL. Salah satu harus diisi.
> **Validasi konflik**: 1 guru tidak boleh mengajar 2 slot di jam yang sama.

---

### `absensi_siswa` — Diisi oleh Guru
```sql
id          INT PK AI
siswa_id    INT FK              -- FK ke siswa.id
kelas_id    INT FK NULL         -- DIISI untuk absensi homeroom (termasuk kelas X)
rombel_id   INT FK NULL         -- DIISI untuk absensi moving class XI/XII
jadwal_id   INT FK              -- FK ke jadwal.id
tanggal     DATE
status      ENUM('Hadir','Sakit','Izin','Alpha')
keterangan  TEXT NULL
guru_id     INT FK              -- FK ke guru.id (yang menginput)
created_at  DATETIME
```
> Validasi: `UNIQUE(siswa_id, jadwal_id, tanggal)` — 1 record per siswa per jadwal per hari (bisa diedit).

---

### `laporan_kehadiran_guru` — Diisi oleh Siswa
```sql
id          INT PK AI
siswa_id    INT FK              -- FK ke siswa.id (siswa yang mengisi)
jadwal_id   INT FK              -- FK ke jadwal.id (slot jadwal hari itu)
guru_id     INT FK              -- FK ke guru.id (guru yang dilaporkan)
mapel_id    INT FK              -- FK ke mata_pelajaran.id
kelas_id    INT FK NULL         -- FK ke kelas.id (untuk jadwal homeroom)
rombel_id   INT FK NULL         -- FK ke rombel.id (untuk jadwal moving class)
tanggal     DATE
status      ENUM('hadir','tugas','tidak_hadir')
keterangan  TEXT NULL
is_verified TINYINT(1)          -- 0=belum, 1=terverifikasi, 2=perlu klarifikasi
verified_by INT FK NULL         -- FK ke users.id (admin yang verifikasi)
created_at  DATETIME
UNIQUE KEY  (siswa_id, jadwal_id, tanggal)
```
> Form muncul otomatis sesuai jadwal hari ini (homeroom + rombel siswa).
> Deadline isi: H+1 pukul 23:59.

---

### `nilai` — Input oleh Guru
```sql
id          INT PK AI
siswa_id    INT FK
mapel_id    INT FK
guru_id     INT FK
semester_id INT FK
jenis_nilai ENUM('UH1','UH2','UH3','UTS','UAS','Tugas','Praktik')
nilai       DECIMAL(5,2)        -- 0.00 – 100.00
created_at  DATETIME
updated_at  DATETIME
```

---

### `rapor` — Nilai Akhir per Mapel per Semester
```sql
id          INT PK AI
siswa_id    INT FK
mapel_id    INT FK
semester_id INT FK
nilai_uh    DECIMAL(5,2)        -- rata-rata UH
nilai_uts   DECIMAL(5,2)
nilai_uas   DECIMAL(5,2)
nilai_akhir DECIMAL(5,2)        -- dihitung otomatis (bobot dari admin)
predikat    VARCHAR(2)          -- A/B/C/D
catatan_guru TEXT NULL
generated_at DATETIME
```

---

### `guru_mapel_kelas` — Assignment Guru ke Kelas/Rombel
```sql
id          INT PK AI
guru_id     INT FK
mapel_id    INT FK
kelas_id    INT FK NULL         -- untuk homeroom / kelas X
rombel_id   INT FK NULL         -- untuk moving class XI/XII
semester_id INT FK
UNIQUE KEY  (guru_id, mapel_id, kelas_id, rombel_id, semester_id)
```
> Digunakan untuk validasi: guru hanya bisa input nilai/absensi di kelas/rombel yang menjadi tugasnya.

---

### `riwayat_kelas` — Histori Kelas Siswa per Tahun Ajaran
```sql
id              INT PK AI
siswa_id        INT FK
kelas_id        INT FK              -- homeroom di tahun ajaran tsb
tahun_ajaran_id INT FK
semester_id     INT FK NULL
status_akhir    ENUM('naik_kelas','lulus','pindah','keluar','tinggal_kelas')
catatan         TEXT NULL
created_at      DATETIME
```
> Tulang punggung sistem jangka panjang. Setiap siswa punya 1 record per tahun ajaran.

---

### `proses_naik_kelas` — Log Eksekusi Kenaikan Kelas
```sql
id                  INT PK AI
tahun_ajaran_lama   INT FK
tahun_ajaran_baru   INT FK
total_naik          INT
total_lulus         INT
total_tinggal       INT
total_pindah        INT
dieksekusi_oleh     INT FK          -- FK ke users.id
dieksekusi_at       DATETIME
status              ENUM('draft','confirmed')
```

---

### `activity_log` — Audit Trail
```sql
id          INT PK AI
user_id     INT FK
aksi        VARCHAR(100)        -- contoh: 'input_nilai', 'edit_absensi', 'login'
tabel       VARCHAR(50)
record_id   INT NULL
detail      TEXT NULL           -- JSON before/after
ip_address  VARCHAR(45)
created_at  DATETIME
```

---

## MANAJEMEN SIKLUS TAHUNAN — 3 POLA NAIK KELAS

> ⚠️ Ada 3 pola berbeda. Jangan samakan prosesnya.

### Pola 1 — X → XI (Manual oleh Admin)
Siswa kelas X naik ke XI dengan homeroom baru (bebas/acak, tidak harus berurutan).
Setelah itu Admin assign ke rombel peminatan.

| Langkah | Proses | Siapa |
|---------|--------|-------|
| 1 | Tutup semester X, pastikan nilai & absensi lengkap | Admin |
| 2 | Assign siswa ke homeroom XI baru (bulk assign) | Admin |
| 3 | Assign siswa ke rombel peminatan: XI BIOLOGI, XI FISIKA, dll | Admin |
| 4 | Konfirmasi eksekusi → update `kelas_id`, isi `siswa_rombel`, catat `riwayat_kelas` | Admin |

### Pola 2 — XI → XII (Otomatis Clone Rombel)
Siswa XI naik ke XII dengan **teman sekelas dan peminatan yang SAMA PERSIS**.
Sistem otomatis clone rombel, Admin hanya konfirmasi.

| Langkah | Proses | Siapa |
|---------|--------|-------|
| 1 | Tutup semester XI, pastikan nilai & absensi lengkap | Admin |
| 2 | Sistem scan semua rombel XI aktif | Otomatis |
| 3 | Buat rombel XII baru per rombel XI → isi `rombel_asal_id` | Otomatis |
| 3b | Admin tentukan jurusan homeroom XII (MIPA/IPS) → isi kolom `jurusan` di tabel `kelas` | Admin |
| 4 | Clone `siswa_rombel` ke semester baru dengan rombel XII | Otomatis |
| 5 | Admin tinjau draft → koreksi jika ada yang tinggal kelas/pindah rombel | Admin |
| 6 | Konfirmasi eksekusi → update `kelas_id`, catat `riwayat_kelas` | Admin |

### Pola 3 — XII → Lulus
```
status siswa = 'lulus'
tahun_lulus  = diisi
users.is_active = 0   (login dinonaktifkan)
Data tetap tersimpan selamanya, bisa diakses via filter status
```

---

## RINGKASAN RELASI TABEL

| Tabel | Berelasi Dengan | Jenis Relasi |
|-------|-----------------|--------------|
| `users` | `guru`, `siswa` (via `entity_id`) | 1:1 |
| `guru` | `kelas` (wali), `jadwal`, `rombel`, `nilai`, `absensi` | 1:N |
| `siswa` | `kelas`, `rombel` (via `siswa_rombel`), `absensi_siswa`, `laporan_kehadiran_guru`, `nilai` | 1:N |
| `kelas` | `siswa`, `jadwal`, `tahun_ajaran` | 1:N |
| `rombel` 🆕 | `siswa` (via `siswa_rombel`), `jadwal`, `guru`, `mata_pelajaran`, `semester` | N:1 masing-masing |
| `siswa_rombel` 🆕 | `siswa`, `rombel`, `semester` | N:N resolver |
| `jadwal` | `kelas`, `rombel`, `guru`, `mata_pelajaran`, `semester` | N:1 masing-masing |
| `absensi_siswa` | `siswa`, `kelas`, `rombel`, `jadwal`, `guru` | N:1 masing-masing |
| `laporan_kehadiran_guru` | `siswa`, `jadwal`, `guru`, `mata_pelajaran`, `kelas`, `rombel` | N:1 masing-masing |
| `nilai` | `siswa`, `mata_pelajaran`, `guru`, `semester` | N:1 masing-masing |
| `rapor` | `siswa`, `mata_pelajaran`, `semester` | N:1 masing-masing |
| `guru_mapel_kelas` | `guru`, `mata_pelajaran`, `kelas`, `rombel`, `semester` | N:N resolver |

---

*SMA Negeri 6 Banjarmasin • Sistem Informasi Sekolah • CI4 + Bootstrap 5 • Dibuat dengan Claude AI*
