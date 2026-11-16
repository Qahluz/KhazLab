<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PeminjamanModel;
use App\Models\BarangModel;

class Peminjaman extends BaseController
{
    protected $peminjamanModel;
    protected $barangModel;

    public function __construct()
    {
        $this->peminjamanModel = new PeminjamanModel();
        $this->barangModel     = new BarangModel();
    }

    /**
     * Menampilkan daftar transaksi peminjaman dan pengembalian.
     */
    public function index()
    {
        $status = $this->request->getGet('status');

        $data = [
            'title'          => 'Peminjaman & Pengembalian Barang',
            'peminjaman'     => $this->peminjamanModel->getPeminjamanWithDetails($status),
            'selectedStatus' => $status,
        ];

        return view('peminjaman/index', $data);
    }

    /**
     * Menampilkan formulir pencatatan peminjaman baru.
     */
    public function create()
    {
        $data = [
            'title'      => 'Catat Peminjaman Barang Baru',
            'barang'     => $this->barangModel->getAvailableForLoan(),
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ];

        return view('peminjaman/create', $data);
    }

    /**
     * Menyimpan transaksi peminjaman baru dan memotong stok barang.
     */
    public function store()
    {
        $rules = [
            'id_barang' => [
                'rules'  => 'required|is_not_unique[barang.id_barang]',
                'errors' => [
                    'required'      => 'Silakan pilih barang yang dipinjam.',
                    'is_not_unique' => 'Barang yang dipilih tidak terdaftar di sistem.',
                ],
            ],
            'nama_peminjam' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Nama peminjam wajib diisi.',
                    'min_length' => 'Nama peminjam minimal 3 karakter.',
                ],
            ],
            'jumlah_pinjam' => [
                'rules'  => 'required|integer|greater_than[0]',
                'errors' => [
                    'required'     => 'Jumlah barang yang dipinjam wajib diisi.',
                    'integer'      => 'Jumlah pinjam harus berupa bilangan bulat.',
                    'greater_than' => 'Jumlah pinjam harus lebih dari 0 unit.',
                ],
            ],
            'tanggal_pinjam' => [
                'rules'  => 'required|valid_date',
                'errors' => [
                    'required'   => 'Tanggal peminjaman wajib diisi.',
                    'valid_date' => 'Format tanggal peminjaman tidak valid.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('peminjaman/tambah'))->withInput()->with('validation', $this->validator);
        }

        $inputData = [
            'id_barang'      => (int)$this->request->getPost('id_barang'),
            'nama_peminjam'  => trim($this->request->getPost('nama_peminjam')),
            'jumlah_pinjam'  => (int)$this->request->getPost('jumlah_pinjam'),
            'tanggal_pinjam' => $this->request->getPost('tanggal_pinjam'),
        ];

        $result = $this->peminjamanModel->pinjamBarang($inputData);

        if (!$result['status']) {
            return redirect()->to(base_url('peminjaman/tambah'))->withInput()->with('error', $result['message']);
        }

        return redirect()->to(base_url('peminjaman'))->with('success', $result['message']);
    }

    /**
     * Memproses pengembalian barang dan memulihkan stok barang.
     */
    public function returnItem($id = null)
    {
        if (!$id) {
            return redirect()->to(base_url('peminjaman'))->with('error', 'ID Transaksi peminjaman tidak valid.');
        }

        $tanggalKembali = $this->request->getPost('tanggal_kembali');
        if (empty($tanggalKembali)) {
            $tanggalKembali = date('Y-m-d');
        }

        $result = $this->peminjamanModel->kembalikanBarang($id, $tanggalKembali);

        if (!$result['status']) {
            return redirect()->to(base_url('peminjaman'))->with('error', $result['message']);
        }

        return redirect()->to(base_url('peminjaman'))->with('success', $result['message']);
    }
}
