<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LokasiModel;
use App\Models\PeminjamanModel;

class Dashboard extends BaseController
{
    protected $barangModel;
    protected $kategoriModel;
    protected $lokasiModel;
    protected $peminjamanModel;

    public function __construct()
    {
        $this->barangModel     = new BarangModel();
        $this->kategoriModel   = new KategoriModel();
        $this->lokasiModel     = new LokasiModel();
        $this->peminjamanModel = new PeminjamanModel();
    }

    /**
     * Menampilkan dashboard utama dengan ringkasan jumlah barang dan statistik kondisi barang.
     */
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Ringkasan Total Data
        $totalJenisBarang = $this->barangModel->countAllResults();
        
        $stokQuery = $db->query("SELECT COALESCE(SUM(jumlah), 0) AS total_unit FROM barang")->getRow();
        $totalUnitBarang = (int)($stokQuery->total_unit ?? 0);

        $pinjamQuery = $db->query("SELECT COALESCE(SUM(jumlah_pinjam), 0) AS total_pinjam FROM peminjaman WHERE status = 'Dipinjam'")->getRow();
        $totalSedangDipinjam = (int)($pinjamQuery->total_pinjam ?? 0);

        $totalKategori = $this->kategoriModel->countAllResults();
        $totalLokasi   = $this->lokasiModel->countAllResults();

        // 2. Statistik Kondisi Barang (Baik, Rusak Ringan, Rusak Berat)
        $kondisiBaik = $this->barangModel->where('kondisi', 'Baik')->countAllResults();
        $kondisiRusakRingan = $this->barangModel->where('kondisi', 'Rusak Ringan')->countAllResults();
        $kondisiRusakBerat = $this->barangModel->where('kondisi', 'Rusak Berat')->countAllResults();

        // 3. Peminjaman Terkini (5 data terbaru)
        $peminjamanTerbaru = $this->peminjamanModel->getPeminjamanWithDetails(null, 5);

        // 4. Barang dengan stok menipis (<= 2 unit)
        $barangStokMenipis = $this->barangModel->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
                                               ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
                                               ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi')
                                               ->where('barang.jumlah <=', 2)
                                               ->orderBy('barang.jumlah', 'ASC')
                                               ->limit(5)
                                               ->findAll();

        $data = [
            'title'               => 'Dashboard Inventaris Laboratorium',
            'totalJenisBarang'    => $totalJenisBarang,
            'totalUnitBarang'     => $totalUnitBarang,
            'totalSedangDipinjam' => $totalSedangDipinjam,
            'totalKategori'       => $totalKategori,
            'totalLokasi'         => $totalLokasi,
            'kondisiBaik'         => $kondisiBaik,
            'kondisiRusakRingan'  => $kondisiRusakRingan,
            'kondisiRusakBerat'   => $kondisiRusakBerat,
            'peminjamanTerbaru'   => $peminjamanTerbaru,
            'barangStokMenipis'   => $barangStokMenipis,
        ];

        return view('dashboard/index', $data);
    }
}
