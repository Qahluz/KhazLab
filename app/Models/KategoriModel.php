<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id_kategori';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_kategori'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama_kategori' => 'required|min_length[3]|max_length[100]|is_unique[kategori.nama_kategori,id_kategori,{id_kategori}]',
    ];

    protected $validationMessages = [
        'nama_kategori' => [
            'required'   => 'Nama kategori wajib diisi.',
            'min_length' => 'Nama kategori minimal 3 karakter.',
            'max_length' => 'Nama kategori maksimal 100 karakter.',
            'is_unique'  => 'Nama kategori sudah digunakan.',
        ],
    ];

    /**
     * Mengambil seluruh kategori beserta total unit barang terkait.
     */
    public function getKategoriWithCount()
    {
        return $this->select('kategori.*, COALESCE(SUM(barang.jumlah), 0) AS total_unit')
                    ->join('barang', 'barang.id_kategori = kategori.id_kategori', 'left')
                    ->groupBy('kategori.id_kategori')
                    ->orderBy('kategori.nama_kategori', 'ASC')
                    ->findAll();
    }
}
