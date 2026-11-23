<?php

namespace App\Models;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table            = 'lokasi';
    protected $primaryKey       = 'id_lokasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_lokasi'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nama_lokasi' => 'required|min_length[3]|max_length[100]|is_unique[lokasi.nama_lokasi,id_lokasi,{id_lokasi}]',
    ];

    protected $validationMessages = [
        'nama_lokasi' => [
            'required'   => 'Nama lokasi wajib diisi.',
            'min_length' => 'Nama lokasi minimal 3 karakter.',
            'max_length' => 'Nama lokasi maksimal 100 karakter.',
            'is_unique'  => 'Nama lokasi sudah digunakan.',
        ],
    ];

    /**
     * Mengambil seluruh lokasi beserta total unit barang yang tersimpan di lokasi tersebut.
     */
    public function getLokasiWithCount()
    {
        return $this->select('lokasi.*, COALESCE(SUM(barang.jumlah), 0) AS total_unit')
                    ->join('barang', 'barang.id_lokasi = lokasi.id_lokasi', 'left')
                    ->groupBy('lokasi.id_lokasi')
                    ->orderBy('lokasi.nama_lokasi', 'ASC')
                    ->findAll();
    }
}
