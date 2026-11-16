<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LokasiModel;

class Lokasi extends BaseController
{
    protected $lokasiModel;

    public function __construct()
    {
        $this->lokasiModel = new LokasiModel();
    }

    /**
     * Menampilkan daftar master data lokasi.
     */
    public function index()
    {
        $data = [
            'title'      => 'Manajemen Lokasi Penyimpanan',
            'lokasi'     => $this->lokasiModel->getLokasiWithCount(),
            'validation' => \Config\Services::validation(),
        ];

        return view('lokasi/index', $data);
    }

    /**
     * Menyimpan data lokasi baru.
     */
    public function store()
    {
        $rules = [
            'nama_lokasi' => [
                'rules'  => 'required|min_length[3]|max_length[100]|is_unique[lokasi.nama_lokasi]',
                'errors' => [
                    'required'   => 'Nama lokasi wajib diisi.',
                    'min_length' => 'Nama lokasi minimal 3 karakter.',
                    'max_length' => 'Nama lokasi maksimal 100 karakter.',
                    'is_unique'  => 'Nama lokasi sudah digunakan.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('lokasi'))->withInput()->with('error', $this->validator->getError('nama_lokasi'));
        }

        $this->lokasiModel->save([
            'nama_lokasi' => trim($this->request->getPost('nama_lokasi')),
        ]);

        return redirect()->to(base_url('lokasi'))->with('success', 'Lokasi baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data lokasi.
     */
    public function update($id = null)
    {
        if (!$id || !$this->lokasiModel->find($id)) {
            return redirect()->to(base_url('lokasi'))->with('error', 'Data lokasi tidak ditemukan.');
        }

        $rules = [
            'nama_lokasi' => [
                'rules'  => 'required|min_length[3]|max_length[100]|is_unique[lokasi.nama_lokasi,id_lokasi,' . $id . ']',
                'errors' => [
                    'required'   => 'Nama lokasi wajib diisi.',
                    'min_length' => 'Nama lokasi minimal 3 karakter.',
                    'max_length' => 'Nama lokasi maksimal 100 karakter.',
                    'is_unique'  => 'Nama lokasi sudah digunakan.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('lokasi'))->withInput()->with('error', $this->validator->getError('nama_lokasi'));
        }

        $this->lokasiModel->update($id, [
            'nama_lokasi' => trim($this->request->getPost('nama_lokasi')),
        ]);

        return redirect()->to(base_url('lokasi'))->with('success', 'Data lokasi berhasil diperbarui.');
    }

    /**
     * Menghapus data lokasi.
     */
    public function delete($id = null)
    {
        if (!$id || !$this->lokasiModel->find($id)) {
            return redirect()->to(base_url('lokasi'))->with('error', 'Data lokasi tidak ditemukan.');
        }

        try {
            $this->lokasiModel->delete($id);
            return redirect()->to(base_url('lokasi'))->with('success', 'Lokasi berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->to(base_url('lokasi'))->with('error', 'Lokasi tidak dapat dihapus karena masih digunakan sebagai tempat penyimpanan barang inventaris.');
        }
    }
}
