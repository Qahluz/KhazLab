# PENGUJIAN.md — Hasil Pengujian Khazlab

Dokumen ini mencatat pengujian yang **benar-benar dijalankan** pada aplikasi, bukan rencana pengujian.

Lingkungan pengujian:

| Komponen | Nilai |
|---|---|
| PHP | 8.1.6 (`C:\xampp\php\php.exe`) |
| CodeIgniter | 4.4.8 |
| MySQL | 8.0.30 |
| Web server | `php spark serve --host 127.0.0.1 --port 8081` |
| Database | `db_inventaris_lab` hasil import `database.sql` |
| Tanggal pengujian | 16 November 2026 (skenario RBAC & auto-reset: 23 November 2026, server `--port 8095`) |

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
| 18 | Syntax seluruh file PHP aplikasi | `php -l` (81 file) | LULUS |
| 19 | Layout cetak menyembunyikan navigasi | Emulasi media `print` | LULUS |
| 19a | Label Stiker QR: tombol `QR Label` dirender untuk admin dan laboran, QR dibuat di browser dengan `qrcodejs` CDN, URL QR membuka tepat satu baris barang saat dipindai | Render HTML + eksekusi pustaka QR | LULUS |
| 19b | Cetak stiker tidak mengganggu cetak laporan: seluruh aturan stiker dibatasi `body.qr-printing`, `khazlab.css` dan view laporan tidak berubah | Audit aturan CSS + render laporan | LULUS |
| 20 | Layout responsif tanpa scroll horizontal | Chromium headless 1440px & 504px | LULUS |
| 21 | Login Petugas Lab (`laboran` / `laboran123`) | HTTP | LULUS |
| 22 | Petugas Lab dapat membaca data master dan mencatat peminjaman | HTTP | LULUS |
| 23 | Petugas Lab diblokir dari mutasi master (`barang/tambah`, `barang/hapus`, `kategori/simpan`) dengan 303 + flash error | HTTP | LULUS |
| 24 | Petugas Lab diblokir dari `POST demo/reset` (data tidak berubah) | HTTP + cek tabel | LULUS |
| 25 | Tombol Tambah/Ubah/Hapus tidak dirender untuk Petugas Lab; tombol `QR Label` tetap ada; badge role tampil | Chromium headless (DOM) | LULUS |
| 26 | Quick login mengisi kredensial dan berhasil masuk | Chromium headless (DOM) | LULUS |
| 27 | Tombol Reset Data Demo (Kepala Lab) memulihkan data seed | HTTP + cek tabel | LULUS |
| 28 | Auto-reset 24 jam: penanda baru tidak reset, penanda kedaluwarsa melakukan reset | HTTP + cek tabel | LULUS |
| 29 | Berkas seed hilang tidak merusak halaman login (dicatat via `log_message`) | HTTP + cek log | LULUS |
| 30 | `PeminjamanStatus::buildWhatsappReminderUrl()` menghasilkan URL `https://wa.me/?text=…` multi-baris (`%0A`, bukan `+`), format tebal WhatsApp, rincian perangkat/jumlah/tanggal, dan template berbeda untuk `Dipinjam` vs `Terlambat` | CLI PHP | LULUS |
| 31 | Tombol **Ingatkan** hanya tampil pada baris `Dipinjam`/`Terlambat` (admin & laboran), tidak pada baris `Kembali` | HTTP + render HTML | LULUS |
| 32 | Logo Khazlab tampil di sidebar, halaman login, dan kop cetak; favicon terpasang; berkas SVG tersaji `image/svg+xml` | HTTP + render HTML | LULUS |
| 33 | `CI_ENVIRONMENT = production` menghilangkan Debug Toolbar (0 kemunculan `ci-debugbar`) pada `/login` dan `/dashboard` | HTTP + render HTML | LULUS |
| 34 | Tampilan mobile: overlay sidebar hanya aktif saat `.show`, `color-scheme: light` di `<meta>` kedua halaman dan `:root` CSS | Inspeksi CSS + render HTML | LULUS |
| 35 | Tanpa horizontal overflow pada 8 halaman utama di viewport `360×800` dan `390×844` (`scrollWidth <= innerWidth`), tombol **Reset Data Demo**/**Logout** di dalam batas layar (`right <= innerWidth`: 344/344 pada 360px, 285/374 pada 390px), dan backdrop drawer hanya tampil saat terbuka | Chromium headless (Playwright) | LULUS |

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
GET  barang/tambah, /edit/(n)   | auth csrf        (group auth:admin)
GET  peminjaman, /tambah        | auth csrf
GET  laporan, /cetak-peminjaman | auth csrf
GET  laporan/export-csv         | auth csrf
POST kategori/simpan|update|hapus, lokasi/simpan|update|hapus,
     barang/simpan|update|hapus, demo/reset                    | auth csrf  (group auth:admin)
POST peminjaman/simpan|kembalikan                               | auth csrf
POST logout                     | csrf
```

Seluruh 29 route terdaftar; route baca & transaksi memakai filter `auth` (semua role login), sedangkan route mutasi master dan `demo/reset` memakai `auth:admin`. Setiap form POST dirender dengan `csrf_field()`.

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

### 21–25. Multi-role RBAC (Kepala Lab vs Petugas Lab)

Diuji melalui HTTP nyata (`curl` dengan cookie jar terpisah) dan Chromium headless.

```text
POST /login/auth  laboran/laboran123   -> 303 /dashboard   (LULUS)
GET  /dashboard                        -> 200, badge "Petugas Lab" (2x: sidebar + topbar)
GET  /barang                           -> 200, tombol ubah: 0, tombol hapus: 0, tautan barang/tambah: 0,
                                          tombol "QR Label" (read-only): 10
GET  /barang?keyword=LAB-KRS-001       -> 200, 1 baris (URL yang dibuka oleh hasil pindai QR)
GET  /barang/tambah                    -> 302 /barang        (diblokir sebelum render)
POST /barang/hapus/1  (CSRF valid)     -> 303 -> /barang
     flash pada request berikutnya     -> "Akses ditolak: akun Petugas Lab tidak berwenang mengubah data master.
                                            Hanya Kepala Lab yang dapat menambah, mengubah, atau menghapus data."
POST /kategori/simpan (CSRF valid)     -> 303 -> halaman sebelumnya, jumlah kategori tetap 4
POST /demo/reset      (CSRF valid)     -> 303 -> halaman sebelumnya, data tidak berubah
GET  /peminjaman, /peminjaman/tambah   -> 200
GET  /laporan, /laporan/cetak-peminjaman, /dashboard/analitik -> 200
POST /peminjaman/simpan                -> "Peminjaman Access Point Ubiquiti UniFi U6-Lite berhasil dicatat.
                                            Jatuh tempo 26/11/2026."
```

Catatan: CodeIgniter mengembalikan 302 untuk `GET` yang dialihkan dan 303 untuk `POST`; keduanya tidak mengeksekusi handler mutasi. Data master diverifikasi tidak berubah setelah request diblokir (kategori tetap 4 baris, barang tetap 14 baris).

### 26. Quick login & badge Public Sandbox

Chromium headless pada `/login`:

```text
judul kartu      : "Public Live Demo Environment"
tombol           : "Isi Akun Kepala Lab (Admin)", "Isi Akun Petugas Lab (Laboran)"
setelah klik     : username = laboran, password = laboran123, input password fokus
submit           : berhasil masuk ke /dashboard
meta             : "Reset otomatis setiap 24 jam."
                   "Terakhir direset: 23/11/2026 22:57 WIB"
                   "Jadwal reset berikutnya: 24/11/2026 22:57 WIB"
overflow horizontal 1280px & 390px : 0
console error / pageerror           : 0
```

### 27. Reset Data Demo manual (Kepala Lab)

```text
GET  /dashboard (admin)      -> tombol "Reset Data Demo" tampil (1 form POST /demo/reset + csrf_field)
klik tombol                  -> modal khazlabConfirm() tampil, label "Ya, Reset Data"
POST /demo/reset             -> 303 -> /dashboard
flash                        -> "Data demo berhasil direset ke kondisi awal (19 perintah SQL dijalankan)."
setelah reset                -> users=2, kategori=4, lokasi=4, barang=14, peminjaman=20
writable/demo_last_reset.txt -> 1795449355 (epoch detik)
```

Saat pengujian pertama ditemukan kegagalan nyata `Cannot access protected property ...Connection::$DBDebug`; baris tersebut dihapus dari `DemoResetService` dan reset diulang hingga berhasil (bukti di atas).

### 28–29. Auto-reset 24 jam

```text
1. Sisipkan baris kategori "TEMP-JUNK" (kategori = 5), penanda = sekarang
   GET /login -> 200, kategori tetap 5            (belum 24 jam, reset dilewati)
2. Penanda dimundurkan 120000 detik (25+ jam)
   GET /login -> 200
   kategori = 4, barang = 14, peminjaman = 20, users = 2   (reset otomatis berjalan)
   penanda diperbarui = 1795449397 (now = 1795449399)
3. Berkas app/Database/demo_seed.sql dipindahkan sementara, penanda dihapus
   GET /login -> 200, kartu sandbox tetap tampil
   log CI4: "Auto-reset data demo dilewati: Berkas seed demo tidak ditemukan ..."
   berkas seed dikembalikan; halaman login tidak pernah crash
```

## Temuan yang Diperbaiki Selama Pengujian

| Temuan | Akar masalah | Perbaikan |
|---|---|---|
| Peminjaman valid gagal disimpan dengan pesan kosong | Pengecekan hasil `insert()` membaca state error model yang sudah tidak relevan | Memakai nilai balik `insert()` (insert ID) dan `$db->error()` untuk menentukan sukses/gagal, lalu rollback eksplisit |
| Edit stok barang selalu ditolak "Kode barang sudah digunakan" | Aturan `is_unique[...,id_barang,{id_barang}]` pada model tidak menginterpolasi `{id_barang}`, sehingga baris sendiri dianggap duplikat | Validasi dipusatkan di controller; aturan edit memakai id hasil interpolasi `is_unique[barang.kode_barang,id_barang,{id}]` |
| Hapus data berelasi memunculkan halaman error CI4 | `DatabaseException` tidak ditangkap | `try/catch DatabaseException` + pesan ramah + `log_message()` |
| Rate limit login hilang saat proses PHP berganti | Disimpan di session | Tabel `login_attempts` per IP + pembersihan otomatis |
| Stok bisa minus saat dua request bersamaan | Cek stok di PHP lalu update terpisah | `UPDATE ... WHERE jumlah >= ?` di dalam `transBegin()`/`transCommit()` |
| Reset data demo selalu gagal (`Cannot access protected property ...::$DBDebug`) | Service mencoba menulis properti internal koneksi CI4 | Properti tidak disentuh lagi; SQL dieksekusi apa adanya lewat `$db->query()` |
| Request Petugas Lab ke halaman mutasi memakai status 302/303 yang tidak konsisten | `redirect()->to()` mengikuti method asal | Diuji ulang: `POST` -> 303, `GET` -> 302, keduanya dengan flash error; handler mutasi tidak pernah dieksekusi |

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

Skenario 1–20 di atas dijalankan melalui HTTP nyata terhadap server tersebut (bukan unit test tiruan), sehingga hasilnya mencerminkan perilaku aplikasi yang dilihat pengguna. Skenario 21–29 dijalankan pada 23 November 2026 dengan perintah yang sama (`php spark serve --port 8095`, `curl`, Chromium headless).

### 30. URL pengingat WhatsApp (`buildWhatsappReminderUrl`)

Dijalankan lewat CLI PHP dengan `today = 2026-12-01` setelah template diubah menjadi pesan multi-baris berformat tebal WhatsApp; keluaran apa adanya:

```text
=== TERLAMBAT (Terlambat) ===
*PENGINGAT KETERLAMBATAN PENGEMBALIAN*
*Laboratorium Komputer — Khazlab*

Halo *Siti Rahmawati*,
Peminjaman inventaris laboratorium berikut telah *MELEWATI* tanggal jatuh tempo:

• *Perangkat:* Kursi Ergonomis (LAB-KRS-001)
• *Jumlah:* 2 unit
• *Tgl Pinjam:* 20/11/2026
• *Jatuh Tempo:* 27/11/2026

Mohon segera mengembalikan perangkat tersebut ke ruang laboratorium. Terima kasih.

URL: https://wa.me/?text=%2APENGINGAT%20KETERLAMBATAN%20PENGEMBALIAN%2A%0A%2ALaboratorium%20Komputer%20%E2%80%94%20Khazlab%2A%0A%0AHalo%20%2ASiti%20Rahmawati%2A%2C%0A...%0A%0AMohon%20segera%20mengembalikan%20perangkat%20tersebut%20ke%20ruang%20laboratorium.%20Terima%20kasih.
  [OK]   prefix wa.me
  [OK]   ada %0A
  [OK]   tidak ada +
  [OK]   tidak ada spasi mentah
  [OK]   charset aman
  [OK]   jumlah baris 12
  [OK]   format perangkat / jumlah / tgl pinjam / jatuh tempo
  [OK]   kata MELEWATI

=== DIPINJAM (Dipinjam) ===
*PENGINGAT JATUH TEMPO PEMINJAMAN*
*Laboratorium Komputer — Khazlab*

Halo *Siti Rahmawati*,
Berikut pengingat jadwal pengembalian inventaris laboratorium yang sedang Anda pinjam:

• *Perangkat:* Kursi Ergonomis (LAB-KRS-001)
• *Jumlah:* 2 unit
• *Tgl Pinjam:* 20/11/2026
• *Jatuh Tempo:* 05/12/2026

Mohon mengembalikan perangkat tepat waktu sesuai jadwal. Terima kasih.

URL: https://wa.me/?text=%2APENGINGAT%20JATUH%20TEMPO%20PEMINJAMAN%2A%0A...%0A%0AMohon%20mengembalikan%20perangkat%20tepat%20waktu%20sesuai%20jadwal.%20Terima%20kasih.
  [OK]   prefix wa.me
  [OK]   ada %0A
  [OK]   tidak ada +
  [OK]   tidak ada spasi mentah
  [OK]   charset aman
  [OK]   jumlah baris 12
  [OK]   format perangkat / jumlah / tgl pinjam / jatuh tempo
  [OK]   tanpa MELEWATI

SEMUA CEK LULUS
```

Kriteria `jumlah baris 12` = hasil `rawurldecode()` memuat tepat 11 karakter `\n`, yaitu dua baris judul, satu baris kosong, dua baris kalimat pembuka, satu baris kosong, empat butir rincian, satu baris kosong, dan satu kalimat penutup.

### 31. Tombol Ingatkan pada `/peminjaman`

Diuji melalui HTTP nyata (`curl` + cookie jar) sebagai admin dan laboran. Seed bawaan berisi 5 transaksi `Dipinjam` yang seluruhnya sudah lewat jatuh tempo, sehingga satu baris (`id_peminjaman = 8`) sementara diberi `tanggal_jatuh_tempo = CURDATE() + 3` agar kasus `Dipinjam` belum jatuh tempo ikut teruji, lalu dikembalikan ke nilai asli.

```text
GET /peminjaman (admin)   -> 200, 11 baris: badge Dipinjam 1, Terlambat 4, Kembali 5
                             tautan wa.me: 5 (persis jumlah baris Dipinjam+Terlambat)
                             baris Kembali: 0 tautan, menampilkan "Tuntas"
  contoh href (setelah entitas HTML di-unescape, seperti dibaca browser):
    https://wa.me/?text=%2APENGINGAT%20KETERLAMBATAN%20PENGEMBALIAN%2A%0A%2ALaboratorium%20Komputer%20%E2%80%94%20Khazlab%2A%0A%0AHalo%20%2ARio%20Wirawan%2C%20S.Kom.%2C%20MMSI%20%28Dosen%29%2A%2C%0A...
  baris Dipinjam belum jatuh tempo memakai template:
    "*PENGINGAT JATUH TEMPO PEMINJAMAN* ... Berikut pengingat jadwal pengembalian inventaris laboratorium ..."
GET /peminjaman (laboran) -> 200, tombol Ingatkan dirender sama (5), tombol Kembalikan juga tersedia
GET /peminjaman?page=2,3  -> 200, seluruh baris berstatus Kembali, 0 tautan wa.me
```

Catatan: `esc($url, 'attr')` mengubah `:/?%=` menjadi entitas HTML di dalam `href`. Itu perilaku resmi escaper CodeIgniter dan aman karena browser meng-unescape atribut sebelum menavigasi — URL akhir yang dibuka WhatsApp identik dengan keluaran CLI pada skenario 30.

### 35. Uji responsif smartphone (`360×800` dan `390×844`)

Dijalankan dengan Chromium headless (Playwright) pada login `admin`, mengunjungi 8 halaman: `/dashboard`, `/dashboard/analitik`, `/barang`, `/kategori`, `/lokasi`, `/peminjaman`, `/laporan`, `/laporan/cetak-peminjaman`. Setiap halaman dianggap gagal jika `scrollWidth > innerWidth`, halaman merender error, atau tombol aksi global keluar dari batas layar. Keluaran apa adanya:

```text
===== VIEWPORT 360x800 =====
PASS /dashboard                   http=200 scrollWidth=360 innerWidth=360 resetBtn=344 logout=344
PASS /dashboard/analitik          http=200 scrollWidth=360 innerWidth=360 resetBtn=344 logout=344
PASS /barang                      http=200 scrollWidth=360 innerWidth=360 resetBtn=344 logout=344
PASS /kategori                    http=200 scrollWidth=360 innerWidth=360 resetBtn=344 logout=344
PASS /lokasi                      http=200 scrollWidth=360 innerWidth=360 resetBtn=344 logout=344
PASS /peminjaman                  http=200 scrollWidth=360 innerWidth=360 resetBtn=344 logout=344
PASS /laporan                     http=200 scrollWidth=360 innerWidth=360 resetBtn=344 logout=344
PASS /laporan/cetak-peminjaman    http=200 scrollWidth=360 innerWidth=360 resetBtn=344 logout=344
PASS drawer-open {"backdrop":"block","sidebarShow":true,"ariaExpanded":"true","actionsDisplay":"flex","actionsRight":264,"scrollWidth":360,"innerWidth":360}
PASS drawer-closed {"backdrop":"none","sidebarShow":false,"ariaExpanded":"false"}

===== VIEWPORT 390x844 =====
PASS /dashboard                   http=200 scrollWidth=390 innerWidth=390 resetBtn=285 logout=374
PASS /dashboard/analitik          http=200 scrollWidth=390 innerWidth=390 resetBtn=285 logout=374
PASS /barang                      http=200 scrollWidth=390 innerWidth=390 resetBtn=285 logout=374
PASS /kategori                    http=200 scrollWidth=390 innerWidth=390 resetBtn=285 logout=374
PASS /lokasi                      http=200 scrollWidth=390 innerWidth=390 resetBtn=285 logout=374
PASS /peminjaman                  http=200 scrollWidth=390 innerWidth=390 resetBtn=285 logout=374
PASS /laporan                     http=200 scrollWidth=390 innerWidth=390 resetBtn=285 logout=374
PASS /laporan/cetak-peminjaman    http=200 scrollWidth=390 innerWidth=390 resetBtn=285 logout=374
PASS drawer-open {"backdrop":"block","sidebarShow":true,"ariaExpanded":"true","actionsDisplay":"flex","actionsRight":264,"scrollWidth":390,"innerWidth":390}
PASS drawer-closed {"backdrop":"none","sidebarShow":false,"ariaExpanded":"false"}

ALL CHECKS PASSED
```

Catatan: elemen `<table>` memang lebih lebar dari viewport dan itu disengaja — setiap tabel berada di dalam `.table-responsive` sehingga yang bergeser hanya area tabel di dalam kartunya, sementara `documentElement.scrollWidth` tetap sama dengan lebar layar.

## Catatan Cakupan

- Pengujian dilakukan pada satu instance server lokal; pengujian beban (load test) dan pengujian lintas browser (Firefox, Safari) belum dilakukan.
- Belum ada automated test (PHPUnit). Seluruh bukti di dokumen ini berasal dari pengujian manual terotomasi via skrip HTTP.
- Pembatasan login bersifat per alamat IP; pengguna di balik satu NAT berbagi kuota percobaan.
