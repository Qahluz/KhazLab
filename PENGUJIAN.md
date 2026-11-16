# Dokumentasi Pengujian Sistem

Pengujian dilakukan pada environment:

- PHP 8.1.10
- CodeIgniter 4.4.8
- MySQL 8.0.30
- Browser: Chrome / Edge
- Server: `php spark serve --host 127.0.0.1 --port 8081`

## 1. Pengujian Syntax

| No | Pemeriksaan | Perintah | Hasil |
|---|---|---|---|
| 1 | Syntax seluruh file PHP | `php -l <file>` | Tidak ada syntax error |
| 2 | Daftar route | `php spark routes` | Seluruh route terbaca |
| 3 | Environment | `php spark env` | `development` |

## 2. Pengujian Fungsional

| No | Modul | Skenario | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| 1 | Login | Login dengan `admin` / `admin123` | Masuk ke dashboard | Lulus |
| 2 | Login | Password salah | Muncul pesan "Password yang dimasukkan salah" | Lulus |
| 3 | Login | Username tidak terdaftar | Muncul pesan "Username tidak ditemukan" | Lulus |
| 4 | Login | 5 kali gagal berturut-turut | Login diblokir sementara 60 detik | Lulus |
| 5 | Login | Akses dashboard tanpa login | Dialihkan ke halaman login | Lulus |
| 6 | Logout | Klik logout | Sesi berakhir dan kembali ke login | Lulus |
| 7 | CSRF | POST tanpa token CSRF | Ditolak dengan HTTP 403 | Lulus |
| 8 | Kategori | Tambah kategori baru | Data tersimpan | Lulus |
| 9 | Kategori | Tambah kategori duplikat | Muncul pesan "Nama kategori sudah digunakan" | Lulus |
| 10 | Kategori | Ubah nama kategori | Data berubah | Lulus |
| 11 | Kategori | Hapus kategori tanpa barang terkait | Data terhapus | Lulus |
| 12 | Kategori | Hapus kategori yang masih dipakai barang | Ditolak dengan pesan error | Lulus |
| 13 | Lokasi | Tambah, ubah, hapus lokasi | Data berubah sesuai aksi | Lulus |
| 14 | Barang | Tambah barang valid | Data tersimpan dan muncul di daftar | Lulus |
| 15 | Barang | Tambah barang dengan field kosong | Pesan validasi tampil di form | Lulus |
| 16 | Barang | Tambah barang dengan kode duplikat | Pesan kode sudah digunakan | Lulus |
| 17 | Barang | Cari barang berdasarkan kode/nama | Hasil pencarian sesuai | Lulus |
| 18 | Barang | Filter kategori dan kondisi | Daftar barang terfilter | Lulus |
| 19 | Barang | Hapus barang tanpa riwayat peminjaman | Data terhapus | Lulus |
| 20 | Barang | Hapus barang yang punya riwayat peminjaman | Ditolak oleh foreign key | Lulus |
| 21 | Peminjaman | Pinjam barang dengan stok cukup | Transaksi tersimpan, stok berkurang | Lulus |
| 22 | Peminjaman | Pinjam melebihi stok | Ditolak, stok tidak berubah | Lulus |
| 23 | Pengembalian | Kembalikan barang | Status menjadi `Kembali`, stok bertambah | Lulus |
| 24 | Pengembalian | Kembalikan transaksi yang sudah kembali | Ditolak | Lulus |
| 25 | Dashboard | Buka dashboard | Statistik dan grafik tampil | Lulus |
| 26 | Laporan | Buka laporan | Rekap inventaris tampil | Lulus |
| 27 | Laporan | Filter laporan | Data laporan terfilter | Lulus |
| 28 | Laporan | Tombol cetak | Dialog print browser terbuka | Lulus |

## 3. Catatan Pengujian

- Pengujian peminjaman menggunakan data uji, kemudian transaksi uji dihapus dan stok dikembalikan ke nilai awal.
- Seluruh halaman utama diuji dan menghasilkan HTTP 200: dashboard, kategori, lokasi, barang, tambah barang, peminjaman, tambah peminjaman, dan laporan.
- Pengujian keamanan CSRF dilakukan dengan mengirim POST tanpa token; server menolak dengan HTTP 403.
- Route hapus dan logout sudah tidak dapat diakses melalui GET (HTTP 404), hanya melalui POST.
