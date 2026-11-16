# PENGUJIAN.md — Hasil Pengujian SIM-LAB

Dokumen ini mencatat pengujian yang **benar-benar dijalankan** pada aplikasi, bukan rencana pengujian.

Lingkungan pengujian:

| Komponen | Nilai |
|---|---|
| PHP | 8.1.6 (`C:\xampp\php\php.exe`) |
| CodeIgniter | 4.4.8 |
| MySQL | 8.0.30 |
| Web server | `php spark serve --host 127.0.0.1 --port 8081` |
| Database | `db_inventaris_lab` hasil import `database.sql` |
| Tanggal pengujian | 16 November 2026 |

## Ringkasan Hasil

| No | Skenario | Metode | Hasil |
|---|---|---|---|
| 1 | Login kredensial valid | HTTP | LULUS |
| 2 | Rate limiting 5 percobaan gagal / 60 detik | HTTP + cek tabel | LULUS |
| 3 | Peminjaman melebihi stok ditolak | HTTP + cek stok | LULUS |
| 4 | Peminjaman valid mengurangi stok | HTTP + cek stok | LULUS |
| 5 | Pengembalian memulihkan stok | HTTP + cek stok | LULUS |
| 6 | Pengembalian ganda tidak menambah stok | HTTP + cek stok | LULUS |
| 7 | Dua request peminjaman paralel tidak membuat stok minus | HTTP paralel (`curl_multi`) | LULUS |
| 8 | Hapus barang beriwayat ditolak dengan pesan ramah | HTTP + cek tabel | LULUS |
| 9 | Hapus kategori yang dipakai ditolak dengan pesan ramah | HTTP + cek tabel | LULUS |
| 10 | Hapus lokasi yang dipakai ditolak dengan pesan ramah | HTTP + cek tabel | LULUS |
| 11 | Pencarian, filter multi-kriteria, pagination | HTTP | LULUS |
| 12 | Export CSV laporan dan analitik | HTTP + cek header | LULUS |
| 13 | Stok tidak boleh diubah di bawah unit yang dipinjam | HTTP | LULUS |
| 14 | Edit stok valid tersimpan | HTTP + cek database | LULUS |
| 15 | Kode barang unik saat tambah dan edit | HTTP + cek database | LULUS |
| 16 | Semua route administrator terlindungi filter `auth` | `php spark routes` | LULUS |
| 17 | CSRF aktif di seluruh route POST | `php spark routes` + render form | LULUS |
| 18 | Syntax seluruh file PHP aplikasi | `php -l` (78 file) | LULUS |
| 19 | Layout cetak menyembunyikan navigasi | Emulasi media `print` | LULUS |
| 20 | Layout responsif tanpa scroll horizontal | Chromium headless 1440px & 504px | LULUS |

## Detail dan Bukti

### 1. Login kredensial valid

Request `POST /login/auth` dengan `admin` / `admin123`.

```text
LOGIN status code 303 (expect 303)
```

Redirect 303 menuju dashboard, sesi terbentuk (cookie `ci_session` diterbitkan).

### 2. Rate limiting login

Enam kali percobaan dengan password salah, lalu login benar setelah 60 detik.

```text
attempt 1: 303
attempt 2: 303
attempt 3: 303
attempt 4: 303
attempt 5: 303
attempt 6: 303
baris login_attempts tercatat: 5 (percobaan ke-6 gagal sebelum verifikasi password)
setelah 61 detik login benar: 303, sisa baris attempts: 0
```

Yang membuktikan mekanismenya bekerja:

- Hanya **5 baris** tercatat untuk 6 percobaan. Percobaan berikutnya ditolak sebelum password diverifikasi, sehingga tidak menambah baris.
- Setelah login berhasil, `login_attempts` untuk IP tersebut **dibersihkan** (sisa 0).
- Pemblokiran disimpan di tabel database, bukan session, sehingga berlaku lintas proses PHP.

### 3–4. Stok atomik saat peminjaman

```text
pinjam 9999 unit: 303 -> Stok barang 'PC Desktop Lenovo ThinkCentre M70q Core i7 (16GB/512GB)' tidak mencukupi (Tersedia: 24, Diminta: 9999).
stok id_barang=1 tetap: 24
pinjam 2 unit: 303 -> Peminjaman berhasil dicatat. Jatuh tempo 23/11/2026.
stok setelah pinjam 2: 22
row baru: jatuh_tempo=2026-11-23 status=Dipinjam
```

### 5–6. Pengembalian idempoten

```text
kembalikan transaksi: 303 -> Pengembalian berhasil dicatat dan stok telah dipulihkan.
stok setelah kembali: 24
kembalikan ULANG: 303 -> Barang pada transaksi ini sudah pernah dikembalikan sebelumnya.
stok tidak bertambah ganda: 24
```

### 7. Bukti tidak terjadi oversell pada request paralel

Dua request `POST /peminjaman/simpan` dikirim **bersamaan** (`curl_multi`) untuk barang yang sama, masing-masing 2 unit, dengan stok awal dipaksa 3 unit.

```text
HTTP kedua request : 303 / 303
Transaksi tersimpan: 1 (harus 1)
Stok akhir         : 1 (harus 1, tidak minus)
HASIL: LULUS - stok tidak pernah minus pada request paralel.
```

Hanya satu transaksi yang berhasil. Request kedua ditolak karena syarat `AND jumlah >= ?` pada query `UPDATE` tidak lagi terpenuhi. Inilah alasan pengecekan stok dilakukan oleh database di dalam transaksi, bukan dengan `SELECT` lalu `UPDATE` di PHP.

### 8–10. Penghapusan data berelasi

```text
hapus barang ber-riwayat: 303 -> Barang tidak dapat dihapus karena masih memiliki riwayat peminjaman. Hapus atau arsipkan transaksi terkait terlebih dahulu.
hapus kategori terpakai : 303 -> Kategori tidak dapat dihapus karena masih digunakan oleh data barang inventaris. Pindahkan barang ke kategori lain terlebih dahulu.
hapus lokasi terpakai   : 303 -> Lokasi tidak dapat dihapus karena masih digunakan sebagai tempat penyimpanan barang inventaris. Pindahkan barang ke lokasi lain terlebih dahulu.
baris masih ada: barang=1 kategori=1 lokasi=1
```

Ketiga kasus menghasilkan pesan berbahasa Indonesia dan redirect normal (303), bukan halaman error CodeIgniter. Data yang dilindungi foreign key tetap utuh.

### 11. Pencarian, filter, dan pagination

```text
barang?keyword=monitor             : 200, 2 baris (cocok pada kode dan nama)
barang?kondisi=Rusak Berat         : memuat LAB-PC-002, tanpa barang kondisi Baik (filter tidak bocor)
barang?lokasi=2                    : 200
barang?page=2                      : 200, menampilkan "4 dari 14"
peminjaman?status=Terlambat        : memuat badge Overdue
peminjaman?status=Kembali          : tidak memuat badge Dipinjam (filter konsisten)
peminjaman?keyword=Afwan           : 200
```

### 12. Export CSV

```text
laporan/export-csv    : 200, content-type=text/csv; charset=UTF-8, 15 baris
dashboard/export-csv  : 200, 15 baris
```

File CSV diawali BOM UTF-8 agar Microsoft Excel membaca karakter Indonesia dengan benar.

### 13–14. Proteksi dan pembaruan stok barang

```text
unit dipinjam barang#5 = 1; update stok jadi 0: 303 -> Jumlah stok total tidak boleh lebih kecil dari 1 unit yang sedang dipinjam. Kurangi peminjaman aktif terlebih dahulu atau isi stok minimal 1 unit.
update stok 3->5: HTTP 303 -> Data barang berhasil diperbarui. | stok=5
```

### 15. Keunikan kode barang

```text
duplikat saat tambah: 303 -> /barang/tambah, pesan "Kode barang sudah digunakan oleh perangkat lain."
  jumlah baris QA-002 = 1 (harus 1)
duplikat saat edit: 303 -> /barang/edit/2, pesan "Kode barang sudah digunakan oleh perangkat lain."
  kode barang#2 sekarang = LAB-MON-001 (tidak tertimpa)
```

### 16–17. Perlindungan route dan CSRF

Keluaran `php spark routes`:

```text
GET  dashboard                  | auth csrf
GET  dashboard/analitik         | auth csrf
GET  dashboard/export-csv       | auth csrf
GET  kategori / lokasi / barang | auth csrf
GET  barang/tambah, /edit/(n)   | auth csrf
GET  peminjaman, /tambah        | auth csrf
GET  laporan, /cetak-peminjaman | auth csrf
GET  laporan/export-csv         | auth csrf
POST kategori/simpan|update|hapus, lokasi/simpan|update|hapus,
     barang/simpan|update|hapus, peminjaman/simpan|kembalikan  | auth csrf
POST logout                     | csrf
```

Seluruh 28 route terdaftar; semua route administrator memakai filter `auth` dan `csrf`, dan setiap form POST dirender dengan `csrf_field()`.

### 18. Syntax PHP

```text
jumlah file: 78
diperiksa: 78 | gagal: 0
```

`php spark routes` juga berjalan dengan exit code 0.

### 19. Mode cetak

Diuji dengan emulasi media `print` pada `/laporan/cetak-peminjaman`:

```text
sidebar        : tidak tampil
topbar         : tidak tampil
tombol aksi    : tidak tampil (.no-print)
margin kiri    : 0px
kop laporan    : "LABORATORIUM KOMPUTER - PROGRAM STUDI SISTEM INFORMASI"
tanda tangan   : 2 blok (Kepala Laboratorium, Petugas Inventaris)
```

### 20. Responsif

Diuji pada Chromium headless lebar 1440px dan 504px untuk 11 halaman (dashboard, analitik, barang, barang/tambah, barang/edit, kategori, lokasi, peminjaman, peminjaman/tambah, laporan, cetak).

```text
overflow horizontal : tidak ada di seluruh halaman
sidebar desktop     : tampil
sidebar mobile      : tertutup (margin-left -264px), tombol toggle tampil
toggle mobile       : terbuka -> margin-left 0px, tertutup -> -264px
modal konfirmasi    : tersedia di setiap halaman
confirm() bawaan    : 0 pemakaian
```

Tangkapan layar: lihat folder [`docs/screenshots/`](docs/screenshots/).

## Temuan yang Diperbaiki Selama Pengujian

| Temuan | Akar masalah | Perbaikan |
|---|---|---|
| Peminjaman valid gagal disimpan dengan pesan kosong | Pengecekan hasil `insert()` membaca state error model yang sudah tidak relevan | Memakai nilai balik `insert()` (insert ID) dan `$db->error()` untuk menentukan sukses/gagal, lalu rollback eksplisit |
| Edit stok barang selalu ditolak "Kode barang sudah digunakan" | Aturan `is_unique[...,id_barang,{id_barang}]` pada model tidak menginterpolasi `{id_barang}`, sehingga baris sendiri dianggap duplikat | Validasi dipusatkan di controller; aturan edit memakai id hasil interpolasi `is_unique[barang.kode_barang,id_barang,{id}]` |
| Hapus data berelasi memunculkan halaman error CI4 | `DatabaseException` tidak ditangkap | `try/catch DatabaseException` + pesan ramah + `log_message()` |
| Rate limit login hilang saat proses PHP berganti | Disimpan di session | Tabel `login_attempts` per IP + pembersihan otomatis |
| Stok bisa minus saat dua request bersamaan | Cek stok di PHP lalu update terpisah | `UPDATE ... WHERE jumlah >= ?` di dalam `transBegin()`/`transCommit()` |

## Cara Mengulang Pengujian

```powershell
# 1. Import ulang database ke kondisi awal
& 'C:\laragon\bin\mysql\8.0.30\bin\mysql.exe' -u root -e "source C:/laragon/www/Project_Akhir/database.sql"

# 2. Jalankan server
php spark serve --host 127.0.0.1 --port 8081

# 3. Cek syntax dan route
Get-ChildItem -Recurse app -Filter *.php | ForEach-Object { php -l $_.FullName }
php spark routes
```

Skenario 1–20 di atas dijalankan melalui HTTP nyata terhadap server tersebut (bukan unit test tiruan), sehingga hasilnya mencerminkan perilaku aplikasi yang dilihat pengguna.

## Catatan Cakupan

- Pengujian dilakukan pada satu instance server lokal; pengujian beban (load test) dan pengujian lintas browser (Firefox, Safari) belum dilakukan.
- Belum ada automated test (PHPUnit). Seluruh bukti di dokumen ini berasal dari pengujian manual terotomasi via skrip HTTP.
- Pembatasan login bersifat per alamat IP; pengguna di balik satu NAT berbagi kuota percobaan.
