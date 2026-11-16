<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LokasiModel;

class Laporan extends BaseController
{
    protected $barangModel;
    protected $kategoriModel;
    protected $lokasiModel;

    public function __construct()
    {
        $this->barangModel   = new BarangModel();
        $this->kategoriModel = new KategoriModel();
        $this->lokasiModel   = new LokasiModel();
    }

    /**
     * Menampilkan laporan rekapitulasi inventaris laboratorium dengan filter dan mode cetak.
     */
    public function index()
    {
        $keyword     = $this->request->getGet('keyword');
        $id_kategori = $this->request->getGet('kategori');
        $kondisi     = $this->request->getGet('kondisi');

        $barang = $this->barangModel->getBarangWithDetails($keyword, $id_kategori, $kondisi);

        // Menghitung ringkasan statistik untuk laporan
        $totalUnit = 0;
        $countBaik = 0;
        $countRusakRingan = 0;
        $countRusakBerat = 0;

        foreach ($barang as $item) {
            $totalUnit += (int)$item['jumlah'];
            if ($item['kondisi'] === 'Baik') {
                $countBaik++;
            } elseif ($item['kondisi'] === 'Rusak Ringan') {
                $countRusakRingan++;
            } elseif ($item['kondisi'] === 'Rusak Berat') {
                $countRusakBerat++;
            }
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $tanggalCetak = date('d') . ' ' . $namaBulan[(int) date('n')] . ' ' . date('Y');

        $data = [
            'title'            => 'Laporan Inventaris Barang Laboratorium',
            'barang'           => $barang,
            'kategori'         => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'totalJenis'       => count($barang),
            'totalUnit'        => $totalUnit,
            'countBaik'        => $countBaik,
            'countRusakRingan' => $countRusakRingan,
            'countRusakBerat'  => $countRusakBerat,
            'selectedKat'      => $id_kategori,
            'selectedKon'      => $kondisi,
            'keyword'          => $keyword,
            'tanggalCetak'     => $tanggalCetak,
        ];

        return view('laporan/index', $data);
    }
}
