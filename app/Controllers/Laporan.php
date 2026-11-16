<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LokasiModel;
use App\Models\PeminjamanModel;

class Laporan extends BaseController
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

    public function index()
    {
        $filters = [
            'keyword'     => trim((string) $this->request->getGet('keyword')),
            'id_kategori' => $this->request->getGet('kategori'),
            'id_lokasi'   => $this->request->getGet('lokasi'),
            'kondisi'     => $this->request->getGet('kondisi'),
        ];

        $barang = $this->barangModel->getBarangWithDetails($filters);

        $totalUnit = 0;
        $countBaik = 0;
        $countRusakRingan = 0;
        $countRusakBerat = 0;

        foreach ($barang as $item) {
            $totalUnit += (int) $item['jumlah'];
            match ($item['kondisi']) {
                'Baik'         => $countBaik++,
                'Rusak Ringan' => $countRusakRingan++,
                default        => $countRusakBerat++,
            };
        }

        $data = [
            'title'            => 'Laporan Inventaris Barang Laboratorium',
            'barang'           => $barang,
            'kategori'         => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'lokasi'           => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'totalJenis'       => count($barang),
            'totalUnit'        => $totalUnit,
            'countBaik'        => $countBaik,
            'countRusakRingan' => $countRusakRingan,
            'countRusakBerat'  => $countRusakBerat,
            'selectedKat'      => $filters['id_kategori'],
            'selectedLok'      => $filters['id_lokasi'],
            'selectedKon'      => $filters['kondisi'],
            'keyword'          => $filters['keyword'],
            'tanggalCetak'     => $this->tanggalIndonesia(),
        ];

        return view('laporan/index', $data);
    }

    public function cetakPeminjaman()
    {
        $status = $this->request->getGet('status');

        $data = [
            'title'          => 'Laporan Peminjaman & Pengembalian',
            'peminjaman'     => $this->peminjamanModel->getPeminjamanWithDetails(['status' => $status])['rows'],
            'ringkasan'      => $this->peminjamanModel->getRingkasan(),
            'selectedStatus' => $status,
            'tanggalCetak'   => $this->tanggalIndonesia(),
        ];

        return view('laporan/cetak_peminjaman', $data);
    }

    public function exportCsv()
    {
        $filters = [
            'keyword'     => trim((string) $this->request->getGet('keyword')),
            'id_kategori' => $this->request->getGet('kategori'),
            'id_lokasi'   => $this->request->getGet('lokasi'),
            'kondisi'     => $this->request->getGet('kondisi'),
        ];

        $rows = [
            ['Kode Barang', 'Nama Barang', 'Kategori', 'Lokasi', 'Stok Tersedia', 'Kondisi', 'Tanggal Masuk'],
        ];

        foreach ($this->barangModel->getBarangWithDetails($filters) as $item) {
            $rows[] = [
                $item['kode_barang'],
                $item['nama_barang'],
                $item['nama_kategori'],
                $item['nama_lokasi'],
                (int) $item['jumlah'],
                $item['kondisi'],
                date('d/m/Y', strtotime($item['tanggal_masuk'])),
            ];
        }

        return $this->downloadCsv('laporan-inventaris-' . date('Ymd-His') . '.csv', $rows);
    }

    private function tanggalIndonesia(): string
    {
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return date('d') . ' ' . $namaBulan[(int) date('n')] . ' ' . date('Y');
    }
}
