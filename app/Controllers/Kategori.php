<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    /**
     * Menampilkan daftar master data kategori.
     */
    public function index()
    {
        $data = [
            'title'      => 'Manajemen Kategori Barang',
            'kategori'   => $this->kategoriModel->getKategoriWithCount(),
            'validation' => \Config\Services::validation(),
        ];

        return view('kategori/index', $data);
    }

    /**
     * Menyimpan data kategori baru.
     */
    public function store()
    {
        $rules = [
            'nama_kategori' => [
                'rules'  => 'required|min_length[3]|max_length[100]|is_unique[kategori.nama_kategori]',
                'errors' => [
                    'required'   => 'Nama kategori wajib diisi.',
                    'min_length' => 'Nama kategori minimal 3 karakter.',
                    'max_length' => 'Nama kategori maksimal 100 karakter.',
                    'is_unique'  => 'Nama kategori sudah digunakan.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('kategori'))->withInput()->with('error', $this->validator->getError('nama_kategori'));
        }

        $this->kategoriModel->save([
            'nama_kategori' => trim($this->request->getPost('nama_kategori')),
        ]);

        return redirect()->to(base_url('kategori'))->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data kategori.
     */
    public function update($id = null)
    {
        if (!$id || !$this->kategoriModel->find($id)) {
            return redirect()->to(base_url('kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }

        $rules = [
            'nama_kategori' => [
                'rules'  => 'required|min_length[3]|max_length[100]|is_unique[kategori.nama_kategori,id_kategori,' . $id . ']',
                'errors' => [
                    'required'   => 'Nama kategori wajib diisi.',
                    'min_length' => 'Nama kategori minimal 3 karakter.',
                    'max_length' => 'Nama kategori maksimal 100 karakter.',
                    'is_unique'  => 'Nama kategori sudah digunakan.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('kategori'))->withInput()->with('error', $this->validator->getError('nama_kategori'));
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => trim($this->request->getPost('nama_kategori')),
        ]);

        return redirect()->to(base_url('kategori'))->with('success', 'Data kategori berhasil diperbarui.');
    }

    /**
     * Menghapus data kategori.
     */
    public function delete($id = null)
    {
        if (!$id || !$this->kategoriModel->find($id)) {
            return redirect()->to(base_url('kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }

        try {
            $this->kategoriModel->delete($id);
            return redirect()->to(base_url('kategori'))->with('success', 'Kategori berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->to(base_url('kategori'))->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data barang inventaris.');
        }
    }
}
