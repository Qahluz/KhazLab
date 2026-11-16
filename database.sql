-- Sistem Informasi Inventaris Barang Laboratorium (SIM-LAB)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- ----------------------------------------------------------------------------
-- Buat Database (Otomatis dibuat apabila belum tersedia)
-- ----------------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `db_inventaris_lab`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `db_inventaris_lab`;

DROP TABLE IF EXISTS `peminjaman`;
DROP TABLE IF EXISTS `barang`;
DROP TABLE IF EXISTS `lokasi`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `users`;

-- ----------------------------------------------------------------------------
-- 1. Struktur & Data Tabel `users`
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

-- Akun Default: username = admin | password = admin123
INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$tmCuaQrRmcV9B28iEYh7Gu922Yi7bosaFDbOSV8OHMgEaFMe2XfAu', 'admin', '2026-11-09 10:00:00', '2026-11-09 10:00:00');

-- ----------------------------------------------------------------------------
-- 2. Struktur & Data Tabel `kategori`
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
(1, 'Perangkat Komputer', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(2, 'Periferal & Output', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(3, 'Perangkat Jaringan', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(4, 'Sarana & Furnitur Lab', '2026-11-09 10:00:00', '2026-11-09 10:00:00');

-- ----------------------------------------------------------------------------
-- 3. Struktur & Data Tabel `lokasi`
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
(1, 'Lab Komputer 1 (Pemrograman)', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(2, 'Lab Komputer 2 (Jaringan & Multimedia)', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(3, 'Lab Komputer 3 (Basis Data)', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(4, 'Ruang Server & Gudang Alat', '2026-11-09 10:00:00', '2026-11-09 10:00:00');

-- ----------------------------------------------------------------------------
-- 4. Struktur & Data Tabel `barang`
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
  CONSTRAINT `fk_barang_lokasi` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id_lokasi`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `barang` (`id_barang`, `kode_barang`, `nama_barang`, `id_kategori`, `id_lokasi`, `jumlah`, `kondisi`, `tanggal_masuk`, `created_at`, `updated_at`) VALUES
(1, 'LAB-PC-001', 'PC Desktop Lenovo ThinkCentre Core i7 (16GB/512GB)', 1, 1, 25, 'Baik', '2026-01-15', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(2, 'LAB-MON-001', 'Monitor LED LG 24 Inch Full HD', 2, 1, 25, 'Baik', '2026-01-15', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(3, 'LAB-PRJ-001', 'Proyektor Epson EB-E500 3300 Lumens', 2, 1, 2, 'Baik', '2026-02-10', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(4, 'LAB-SWT-001', 'Switch Cisco Catalyst 24-Port Gigabit', 3, 4, 4, 'Baik', '2026-02-20', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(5, 'LAB-RTR-001', 'Router MikroTik RB4011', 3, 2, 5, 'Baik', '2026-03-05', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(6, 'LAB-PRN-001', 'Printer Canon Pixma G2010 Ink Tank', 2, 1, 2, 'Rusak Ringan', '2026-02-15', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(7, 'LAB-PC-002', 'PC Rakitan Core i5 Lab Jaringan (Motherboard Bermasalah)', 1, 2, 1, 'Rusak Berat', '2026-01-20', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(8, 'LAB-MEJ-001', 'Meja Komputer Praktikum Rangka Baja', 4, 1, 30, 'Baik', '2026-01-10', '2026-11-09 10:00:00', '2026-11-09 10:00:00');

-- ----------------------------------------------------------------------------
-- 5. Struktur & Data Tabel `peminjaman`
-- ----------------------------------------------------------------------------
CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` int(11) NOT NULL,
  `nama_peminjam` varchar(100) NOT NULL,
  `jumlah_pinjam` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('Dipinjam','Kembali') NOT NULL DEFAULT 'Dipinjam',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_peminjaman`),
  KEY `fk_peminjaman_barang` (`id_barang`),
  CONSTRAINT `fk_peminjaman_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_jumlah_pinjam` CHECK (`jumlah_pinjam` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `peminjaman` (`id_peminjaman`, `id_barang`, `nama_peminjam`, `jumlah_pinjam`, `tanggal_pinjam`, `tanggal_kembali`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'Rio Wirawan, S.Kom.,MMSI (Dosen)', 1, '2026-11-07', NULL, 'Dipinjam', '2026-11-09 10:00:00', '2026-11-09 10:00:00'),
(2, 5, 'Afwan Nasuha (Mahasiswa)', 1, '2026-11-04', '2026-11-08', 'Kembali', '2026-11-09 10:00:00', '2026-11-09 10:00:00');

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
COMMIT;
