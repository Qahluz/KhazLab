-- Sistem Informasi Inventaris Barang Laboratorium Komputer (<KhazLab>)
-- Skema final: tabel users, kategori, lokasi, barang, peminjaman, login_attempts
-- Data dummy: 4 kategori, 4 lokasi, 14 barang, 20 transaksi peminjaman

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

CREATE DATABASE IF NOT EXISTS `db_inventaris_lab`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `db_inventaris_lab`;

DROP TABLE IF EXISTS `login_attempts`;
DROP TABLE IF EXISTS `peminjaman`;
DROP TABLE IF EXISTS `barang`;
DROP TABLE IF EXISTS `lokasi`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `users`;

-- ----------------------------------------------------------------------------
-- 1. Tabel `users`
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin') NOT NULL DEFAULT 'admin',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun default: username = admin | password = admin123
INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$tmCuaQrRmcV9B28iEYh7Gu922Yi7bosaFDbOSV8OHMgEaFMe2XfAu', 'admin', '2026-08-01 09:00:00', '2026-08-01 09:00:00');

-- ----------------------------------------------------------------------------
-- 2. Tabel `kategori`
-- ----------------------------------------------------------------------------
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `nama_kategori` (`nama_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `created_at`, `updated_at`) VALUES
(1, 'Perangkat Komputer', '2026-08-01 09:00:00', '2026-08-01 09:00:00'),
(2, 'Periferal & Output', '2026-08-01 09:00:00', '2026-08-01 09:00:00'),
(3, 'Perangkat Jaringan', '2026-08-01 09:00:00', '2026-08-01 09:00:00'),
(4, 'Sarana & Furnitur Lab', '2026-08-01 09:00:00', '2026-08-01 09:00:00');

-- ----------------------------------------------------------------------------
-- 3. Tabel `lokasi`
-- ----------------------------------------------------------------------------
CREATE TABLE `lokasi` (
  `id_lokasi` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lokasi` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_lokasi`),
  UNIQUE KEY `nama_lokasi` (`nama_lokasi`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lokasi` (`id_lokasi`, `nama_lokasi`, `created_at`, `updated_at`) VALUES
(1, 'Lab Komputer 1 (Pemrograman)', '2026-08-01 09:00:00', '2026-08-01 09:00:00'),
(2, 'Lab Komputer 2 (Jaringan & Multimedia)', '2026-08-01 09:00:00', '2026-08-01 09:00:00'),
(3, 'Lab Komputer 3 (Basis Data)', '2026-08-01 09:00:00', '2026-08-01 09:00:00'),
(4, 'Ruang Server & Gudang Alat', '2026-08-01 09:00:00', '2026-08-01 09:00:00');

-- ----------------------------------------------------------------------------
-- 4. Tabel `barang`
-- Kolom `jumlah` = stok tersedia di gudang/lab. Unit yang sedang dipinjam
-- sudah dikurangi dari kolom ini oleh PeminjamanModel::pinjamBarang().
-- ----------------------------------------------------------------------------
CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `id_lokasi` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `kondisi` enum('Baik','Rusak Ringan','Rusak Berat') NOT NULL DEFAULT 'Baik',
  `tanggal_masuk` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `kode_barang` (`kode_barang`),
  KEY `fk_barang_kategori` (`id_kategori`),
  KEY `fk_barang_lokasi` (`id_lokasi`),
  CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_barang_lokasi` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id_lokasi`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_barang_jumlah` CHECK (`jumlah` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `barang` (`id_barang`, `kode_barang`, `nama_barang`, `id_kategori`, `id_lokasi`, `jumlah`, `kondisi`, `tanggal_masuk`, `created_at`, `updated_at`) VALUES
(1, 'LAB-PC-001', 'PC Desktop Lenovo ThinkCentre M70q Core i7 (16GB/512GB)', 1, 1, 24, 'Baik', '2026-01-15', '2026-08-02 09:00:00', '2026-11-10 09:15:00'),
(2, 'LAB-MON-001', 'Monitor LED LG 24 Inch Full HD IPS', 2, 1, 25, 'Baik', '2026-01-15', '2026-08-02 09:00:00', '2026-08-02 09:00:00'),
(3, 'LAB-KBM-001', 'Keyboard Mekanik Logitech K845', 2, 1, 26, 'Baik', '2026-01-15', '2026-08-02 09:00:00', '2026-08-02 09:00:00'),
(4, 'LAB-MSE-001', 'Mouse Optical Logitech M100', 2, 1, 28, 'Baik', '2026-01-15', '2026-08-02 09:00:00', '2026-08-02 09:00:00'),
(5, 'LAB-PRJ-001', 'Proyektor Epson EB-E500 3300 Lumens', 2, 1, 1, 'Baik', '2026-02-10', '2026-08-02 09:00:00', '2026-11-12 10:00:00'),
(6, 'LAB-PRN-001', 'Printer Canon Pixma G2010 Ink Tank', 2, 3, 1, 'Rusak Ringan', '2026-02-15', '2026-08-02 09:00:00', '2026-11-11 13:00:00'),
(7, 'LAB-SWT-001', 'Switch Cisco Catalyst C1000 24-Port Gigabit', 3, 4, 4, 'Baik', '2026-02-20', '2026-08-02 09:00:00', '2026-08-02 09:00:00'),
(8, 'LAB-RTR-001', 'Router MikroTik RB4011iGS+RM', 3, 4, 4, 'Baik', '2026-03-05', '2026-08-02 09:00:00', '2026-11-13 08:30:00'),
(9, 'LAB-AP-001', 'Access Point Ubiquiti UniFi U6-Lite', 3, 2, 5, 'Baik', '2026-03-05', '2026-08-02 09:00:00', '2026-08-02 09:00:00'),
(10, 'LAB-KBL-001', 'Kabel UTP Cat6 Belden 305m (Roll)', 3, 4, 2, 'Baik', '2026-03-10', '2026-08-02 09:00:00', '2026-08-02 09:00:00'),
(11, 'LAB-PC-002', 'PC Rakitan Core i5 Lab Jaringan (Motherboard Bermasalah)', 1, 2, 1, 'Rusak Berat', '2026-01-20', '2026-08-02 09:00:00', '2026-11-09 11:00:00'),
(12, 'LAB-PC-003', 'PC Rakitan Core i3 Lab Basis Data (PSU Lemah)', 1, 3, 2, 'Rusak Ringan', '2026-01-20', '2026-08-02 09:00:00', '2026-11-09 11:00:00'),
(13, 'LAB-MEJ-001', 'Meja Komputer Praktikum Rangka Baja', 4, 1, 30, 'Baik', '2026-01-10', '2026-08-02 09:00:00', '2026-08-02 09:00:00'),
(14, 'LAB-KRS-001', 'Kursi Ergonomis Praktikum (Hydraulic)', 4, 3, 3, 'Rusak Ringan', '2026-01-10', '2026-08-02 09:00:00', '2026-11-14 14:20:00');

-- ----------------------------------------------------------------------------
-- 5. Tabel `peminjaman`
-- `tanggal_jatuh_tempo` dihitung 7 hari setelah tanggal pinjam saat pencatatan.
-- `status` hanya menyimpan Dipinjam/Kembali; label "Terlambat" dihitung runtime
-- oleh App\Libraries\PeminjamanStatus dari tanggal_jatuh_tempo.
-- ----------------------------------------------------------------------------
CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) NOT NULL,
  `nama_peminjam` varchar(100) NOT NULL,
  `jumlah_pinjam` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('Dipinjam','Kembali') NOT NULL DEFAULT 'Dipinjam',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_peminjaman`),
  KEY `fk_peminjaman_barang` (`id_barang`),
  KEY `idx_peminjaman_status_jatuh_tempo` (`status`, `tanggal_jatuh_tempo`),
  CONSTRAINT `fk_peminjaman_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_jumlah_pinjam` CHECK (`jumlah_pinjam` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `peminjaman` (`id_peminjaman`, `id_barang`, `nama_peminjam`, `jumlah_pinjam`, `tanggal_pinjam`, `tanggal_jatuh_tempo`, `tanggal_kembali`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 'Rio Wirawan, S.Kom., MMSI (Dosen)', 1, '2026-11-07', '2026-11-14', NULL, 'Dipinjam', '2026-11-07 08:10:00', '2026-11-07 08:10:00'),
(2, 8, 'Afwan Nasuha (Mahasiswa)', 1, '2026-11-04', '2026-11-11', '2026-11-08', 'Kembali', '2026-11-04 09:00:00', '2026-11-08 15:40:00'),
(3, 1, 'Yulia Sari (Mahasiswa)', 1, '2026-11-12', '2026-11-19', NULL, 'Dipinjam', '2026-11-12 07:45:00', '2026-11-12 07:45:00'),
(4, 6, 'Lab Basis Data - Praktikum Mingguan', 1, '2026-11-11', '2026-11-18', NULL, 'Dipinjam', '2026-11-11 13:00:00', '2026-11-11 13:00:00'),
(5, 9, 'Rudi Hartono (Asisten Lab)', 1, '2026-11-05', '2026-11-12', '2026-11-12', 'Kembali', '2026-11-05 10:20:00', '2026-11-12 16:05:00'),
(6, 10, 'Praktikum Jaringan Kelas 2A', 1, '2026-11-13', '2026-11-20', NULL, 'Dipinjam', '2026-11-13 08:00:00', '2026-11-13 08:00:00'),
(7, 3, 'Nadia Putri (Mahasiswa)', 1, '2026-11-10', '2026-11-17', '2026-11-13', 'Kembali', '2026-11-10 11:30:00', '2026-11-13 09:10:00'),
(8, 14, 'Laboran - Kegiatan Workshop IoT', 1, '2026-11-14', '2026-11-21', NULL, 'Dipinjam', '2026-11-14 14:20:00', '2026-11-14 14:20:00'),
(9, 2, 'Bayu Pratama (Mahasiswa)', 1, '2026-10-28', '2026-11-04', '2026-11-03', 'Kembali', '2026-10-28 08:15:00', '2026-11-03 13:45:00'),
(10, 7, 'Praktikum Jaringan Kelas 3B', 1, '2026-10-30', '2026-11-06', '2026-11-06', 'Kembali', '2026-10-30 09:40:00', '2026-11-06 11:20:00'),
(11, 1, 'Siska Amelia (Mahasiswa)', 1, '2026-10-25', '2026-11-01', '2026-10-31', 'Kembali', '2026-10-25 07:50:00', '2026-10-31 15:00:00'),
(12, 12, 'Tim Kurikulum - Rapat Daring', 1, '2026-10-20', '2026-10-27', '2026-10-26', 'Kembali', '2026-10-20 13:10:00', '2026-10-26 10:05:00'),
(13, 4, 'Laboran - Maintenance Lab 1', 2, '2026-09-22', '2026-09-29', '2026-09-28', 'Kembali', '2026-09-22 08:30:00', '2026-09-28 16:20:00'),
(14, 13, 'Kegiatan Open House Mahasiswa Baru', 2, '2026-09-14', '2026-09-21', '2026-09-20', 'Kembali', '2026-09-14 09:00:00', '2026-09-20 17:15:00'),
(15, 9, 'Himpunan Mahasiswa - Seminar Teknologi', 1, '2026-09-08', '2026-09-15', '2026-09-15', 'Kembali', '2026-09-08 10:45:00', '2026-09-15 14:30:00'),
(16, 2, 'Dewi Lestari (Mahasiswa)', 1, '2026-08-18', '2026-08-25', '2026-08-24', 'Kembali', '2026-08-18 08:05:00', '2026-08-24 12:00:00'),
(17, 3, 'Praktikum Pemrograman Kelas 1C', 2, '2026-08-11', '2026-08-18', '2026-08-18', 'Kembali', '2026-08-11 07:55:00', '2026-08-18 16:45:00'),
(18, 7, 'Perbaikan Jaringan Lab 2', 1, '2026-08-05', '2026-08-12', '2026-08-09', 'Kembali', '2026-08-05 13:25:00', '2026-08-09 09:30:00'),
(19, 1, 'Fajar Nugroho (Mahasiswa)', 1, '2026-07-21', '2026-07-28', '2026-07-27', 'Kembali', '2026-07-21 09:15:00', '2026-07-27 11:10:00'),
(20, 5, 'Sosialisasi Sistem Informasi Lab', 1, '2026-07-09', '2026-07-16', '2026-07-15', 'Kembali', '2026-07-09 08:40:00', '2026-07-15 15:35:00');

-- ----------------------------------------------------------------------------
-- 6. Tabel `login_attempts` - penunjang rate limiting login 5x / 60 detik
-- Dicatat per alamat IP; baris lebih tua dari 10 menit dibersihkan otomatis.
-- ----------------------------------------------------------------------------
CREATE TABLE `login_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) NOT NULL,
  `attempted_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_login_attempts_ip_time` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
