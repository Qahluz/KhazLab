<?php

namespace App\Controllers;

use App\Libraries\PeminjamanStatus;
use App\Models\BarangModel;
use App\Models\PeminjamanModel;

class Peminjaman extends BaseController
{
    protected $peminjamanModel;
    protected $barangModel;

    public function __construct()
    {
        $this->peminjamanModel = new PeminjamanModel();
        $this->barangModel     = new BarangModel();
    }

    public function index()
    {
        $status  = $this->request->getGet('status');
        $keyword = trim((string) $this->request->getGet('keyword'));
        $page    = (int) ($this->request->getGet('page') ?? 1);

        $result = $this->peminjamanModel->getPeminjamanWithDetails([
            'status'  => $status,
            'keyword' => $keyword,
            'page'    => $page,
            'perPage' => 10,
        ]);

        $data = [
            'title'          => 'Peminjaman & Pengembalian Barang',
            'peminjaman'     => $result['rows'],
            'pager'          => $result['pager'],
            'total'          => $result['total'],
            'keyword'        => $keyword,
            'selectedStatus' => $status,
            'ringkasan'      => $this->peminjamanModel->getRingkasan(),
        ];

        return view('peminjaman/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Catat Peminjaman Barang Baru',
            'barang'     => $this->barangModel->getAvailableForLoan(),
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
            'jatuhTempo' => PeminjamanStatus::defaultJatuhTempo(date('Y-m-d')),
        ];

        return view('peminjaman/create', $data);
    }

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
            'tanggal_jatuh_tempo' => [
                'rules'  => 'required|valid_date',
                'errors' => [
                    'required'   => 'Tanggal jatuh tempo wajib diisi.',
                    'valid_date' => 'Format tanggal jatuh tempo tidak valid.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(base_url('peminjaman/tambah'))->withInput()->with('validation', $this->validator);
        }

        $result = $this->peminjamanModel->pinjamBarang([
            'id_barang'           => (int) $this->request->getPost('id_barang'),
            'nama_peminjam'       => trim((string) $this->request->getPost('nama_peminjam')),
            'jumlah_pinjam'       => (int) $this->request->getPost('jumlah_pinjam'),
            'tanggal_pinjam'      => (string) $this->request->getPost('tanggal_pinjam'),
            'tanggal_jatuh_tempo' => (string) $this->request->getPost('tanggal_jatuh_tempo'),
        ]);

        if (! $result['status']) {
            return redirect()->to(base_url('peminjaman/tambah'))->withInput()->with('error', $result['message']);
        }

        return redirect()->to(base_url('peminjaman'))->with('success', $result['message']);
    }

    public function returnItem($id = null)
    {
        if (! $id) {
            return redirect()->to(base_url('peminjaman'))->with('error', 'ID Transaksi peminjaman tidak valid.');
        }

        $tanggalKembali = (string) ($this->request->getPost('tanggal_kembali') ?: date('Y-m-d'));

        $result = $this->peminjamanModel->kembalikanBarang((int) $id, $tanggalKembali);

        return redirect()->to(base_url('peminjaman'))->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}
