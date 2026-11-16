<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LokasiModel;

class Barang extends BaseController
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
     * Menampilkan daftar barang inventaris beserta fitur pencarian dan filter.
     */
    public function index()
    {
        $keyword     = $this->request->getGet('keyword');
        $id_kategori = $this->request->getGet('kategori');
        $kondisi     = $this->request->getGet('kondisi');

        $data = [
            'title'       => 'Manajemen Data Barang',
            'barang'      => $this->barangModel->getBarangWithDetails($keyword, $id_kategori, $kondisi),
            'kategori'    => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'keyword'     => $keyword,
            'selectedKat' => $id_kategori,
            'selectedKon' => $kondisi,
        ];

        return view('barang/index', $data);
    }

    /**
     * Menampilkan form penambahan barang baru.
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Barang Inventaris Baru',
            'kategori'   => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'lokasi'     => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ];

        return view('barang/create', $data);
    }

    /**
     * Menyimpan data barang baru ke database.
     */
    public function store()
    {
        $rules = $this->barangModel->getValidationRules();

        if (!$this->validate($rules, $this->barangModel->getValidationMessages())) {
            return redirect()->to(base_url('barang/tambah'))->withInput()->with('validation', $this->validator);
        }

        $this->barangModel->save([
            'kode_barang'   => trim($this->request->getPost('kode_barang')),
            'nama_barang'   => trim($this->request->getPost('nama_barang')),
            'id_kategori'   => $this->request->getPost('id_kategori'),
            'id_lokasi'     => $this->request->getPost('id_lokasi'),
            'jumlah'        => (int)$this->request->getPost('jumlah'),
            'kondisi'       => $this->request->getPost('kondisi'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
        ]);

        return redirect()->to(base_url('barang'))->with('success', 'Barang inventaris baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir edit data barang.
     */
    public function edit($id = null)
    {
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->to(base_url('barang'))->with('error', 'Data barang tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Barang Inventaris',
            'barang'     => $barang,
            'kategori'   => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'lokasi'     => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ];

        return view('barang/edit', $data);
    }

    /**
     * Memperbarui data barang.
     */
    public function update($id = null)
    {
        if (!$id || !$this->barangModel->find($id)) {
            return redirect()->to(base_url('barang'))->with('error', 'Data barang tidak ditemukan.');
        }

        $rules = [
            'kode_barang'   => "required|min_length[3]|max_length[50]|is_unique[barang.kode_barang,id_barang,{$id}]",
            'nama_barang'   => 'required|min_length[3]|max_length[150]',
            'id_kategori'   => 'required|is_not_unique[kategori.id_kategori]',
            'id_lokasi'     => 'required|is_not_unique[lokasi.id_lokasi]',
            'jumlah'        => 'required|integer|greater_than_equal_to[0]',
            'kondisi'       => 'required|in_list[Baik,Rusak Ringan,Rusak Berat]',
            'tanggal_masuk' => 'required|valid_date',
        ];

        if (!$this->validate($rules, $this->barangModel->getValidationMessages())) {
            return redirect()->to(base_url('barang/edit/' . $id))->withInput()->with('validation', $this->validator);
        }

        $this->barangModel->update($id, [
            'kode_barang'   => trim($this->request->getPost('kode_barang')),
            'nama_barang'   => trim($this->request->getPost('nama_barang')),
            'id_kategori'   => $this->request->getPost('id_kategori'),
            'id_lokasi'     => $this->request->getPost('id_lokasi'),
            'jumlah'        => (int)$this->request->getPost('jumlah'),
            'kondisi'       => $this->request->getPost('kondisi'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
        ]);

        return redirect()->to(base_url('barang'))->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Menghapus data barang.
     */
    public function delete($id = null)
    {
        if (!$id || !$this->barangModel->find($id)) {
            return redirect()->to(base_url('barang'))->with('error', 'Data barang tidak ditemukan.');
        }

        try {
            $this->barangModel->delete($id);
            return redirect()->to(base_url('barang'))->with('success', 'Data barang berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->to(base_url('barang'))->with('error', 'Barang tidak dapat dihapus karena tercatat dalam riwayat peminjaman laboratorium.');
        }
    }
}
