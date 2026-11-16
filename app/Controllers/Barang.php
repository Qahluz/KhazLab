<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use App\Models\LokasiModel;
use App\Models\PeminjamanModel;

class Barang extends BaseController
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
     * Menampilkan daftar barang inventaris beserta fitur pencarian dan filter.
     */
    public function index()
    {
        $filters = [
            'keyword'     => trim((string) $this->request->getGet('keyword')),
            'id_kategori' => $this->request->getGet('kategori'),
            'id_lokasi'   => $this->request->getGet('lokasi'),
            'kondisi'     => $this->request->getGet('kondisi'),
        ];
        $page = (int) ($this->request->getGet('page') ?? 1);

        $data = [
            'title'       => 'Manajemen Data Barang',
            'barang'      => $this->barangModel->getBarangWithDetails($filters, 10, $page),
            'pager'       => $this->barangModel->pager,
            'kategori'    => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'lokasi'      => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'keyword'     => $filters['keyword'],
            'selectedKat' => $filters['id_kategori'],
            'selectedLok' => $filters['id_lokasi'],
            'selectedKon' => $filters['kondisi'],
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
        $id = (int) $id;
        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return redirect()->to(base_url('barang'))->with('error', 'Data barang tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Barang Inventaris',
            'barang'     => $barang,
            'kategori'   => $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'lokasi'     => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
            'dipinjam'   => $this->peminjamanModel->getUnitDipinjamPerBarang()[$id] ?? 0,
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ];

        return view('barang/edit', $data);
    }

    /**
     * Memperbarui data barang.
     */
    public function update($id = null)
    {
        $id = (int) $id;
        if (! $id || ! $this->barangModel->find($id)) {
            return redirect()->to(base_url('barang'))->with('error', 'Data barang tidak ditemukan.');
        }

        if (! $this->validate($this->barangModel->getRulesForUpdate($id), $this->barangModel->getValidationMessages())) {
            return redirect()->to(base_url('barang/edit/' . $id))->withInput()->with('validation', $this->validator);
        }

        $jumlahBaru = (int) $this->request->getPost('jumlah');
        $dipinjam   = $this->peminjamanModel->getUnitDipinjamPerBarang()[$id] ?? 0;

        if ($jumlahBaru < $dipinjam) {
            return redirect()->to(base_url('barang/edit/' . $id))
                ->withInput()
                ->with('error', "Jumlah stok total tidak boleh lebih kecil dari {$dipinjam} unit yang sedang dipinjam. Kurangi peminjaman aktif terlebih dahulu atau isi stok minimal {$dipinjam} unit.");
        }

        $tersimpan = $this->barangModel->update($id, [
            'kode_barang'   => trim((string) $this->request->getPost('kode_barang')),
            'nama_barang'   => trim((string) $this->request->getPost('nama_barang')),
            'id_kategori'   => $this->request->getPost('id_kategori'),
            'id_lokasi'     => $this->request->getPost('id_lokasi'),
            'jumlah'        => $jumlahBaru,
            'kondisi'       => $this->request->getPost('kondisi'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
        ]);

        if ($tersimpan === false) {
            return redirect()->to(base_url('barang/edit/' . $id))
                ->withInput()
                ->with('error', 'Perubahan gagal disimpan: ' . implode(' ', $this->barangModel->errors()));
        }

        return redirect()->to(base_url('barang'))->with('success', 'Data barang berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $id = (int) $id;
        if (! $id || ! $this->barangModel->find($id)) {
            return redirect()->to(base_url('barang'))->with('error', 'Data barang tidak ditemukan.');
        }

        try {
            $this->barangModel->delete($id);

            return redirect()->to(base_url('barang'))->with('success', 'Data barang berhasil dihapus.');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            log_message('info', 'Hapus barang ditolak constraint FK: {message}', ['message' => $e->getMessage()]);

            return redirect()->to(base_url('barang'))->with('error', 'Barang tidak dapat dihapus karena masih memiliki riwayat peminjaman. Hapus atau arsipkan transaksi terkait terlebih dahulu.');
        }
    }
}
