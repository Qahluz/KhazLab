<?php

namespace App\Controllers;

use App\Libraries\PeminjamanStatus;
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

    public function index()
    {
        $data = [
            'title'             => 'Dashboard Inventaris Laboratorium',
            'ringkasan'         => $this->barangModel->getRingkasanAset(),
            'ringkasanPinjam'   => $this->peminjamanModel->getRingkasan(),
            'totalKategori'     => $this->kategoriModel->countAllResults(),
            'totalLokasi'       => $this->lokasiModel->countAllResults(),
            'peminjamanTerbaru' => $this->peminjamanModel->getPeminjamanTerkini(5),
            'barangStokMenipis' => $this->barangModel->getStokMenipis(PeminjamanStatus::AMBANG_STOK_MENIPIS),
            'barangRusakBerat'  => $this->barangModel->getRusakBerat(),
            'peminjamanTelat'   => $this->peminjamanModel->getTerlambat(),
            'trenPeminjaman'    => $this->peminjamanModel->getTrenPeminjaman(6),
        ];

        return view('dashboard/index', $data);
    }

    public function analitik()
    {
        $tren = $this->peminjamanModel->getTrenPeminjaman(6);

        $data = [
            'title'     => 'Dashboard Analitik Laboratorium',
            'ringkasan' => $this->barangModel->getRingkasanAset(),
            'tren'      => $tren,
            'kategori'  => $this->kategoriModel->getKategoriWithCount(),
            'lokasi'    => $this->lokasiModel->getLokasiWithCount(),
            'stok'      => $this->peminjamanModel->getUnitDipinjamPerBarang(),
            'barangList' => $this->barangModel->getBarangWithDetails(),
        ];

        return view('dashboard/analitik', $data);
    }

    public function exportAnalitikCsv()
    {
        $ringkasan = $this->barangModel->getRingkasanAset();
        $tren      = $this->peminjamanModel->getTrenPeminjaman(6);

        $rows = [
            ['Metrik', 'Nilai'],
            ['Total Aset Fisik (unit)', $ringkasan['total_aset']],
            ['Stok Tersedia di Gudang (unit)', $ringkasan['stok_tersedia']],
            ['Sedang Dipinjam (unit)', $ringkasan['dipinjam']],
            ['Jenis Barang', $ringkasan['total_jenis']],
            ['Kondisi Baik', $ringkasan['baik']],
            ['Kondisi Rusak Ringan', $ringkasan['rusak_ringan']],
            ['Kondisi Rusak Berat', $ringkasan['rusak_berat']],
            [],
            ['Bulan', 'Jumlah Transaksi Peminjaman'],
        ];

        foreach ($tren['labels'] as $i => $label) {
            $rows[] = [$label, $tren['data'][$i]];
        }

        return $this->downloadCsv('analitik-laboratorium-' . date('Ymd-His') . '.csv', $rows);
    }
}
