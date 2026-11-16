<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'barang';
    protected $primaryKey       = 'id_barang';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_barang',
        'nama_barang',
        'id_kategori',
        'id_lokasi',
        'jumlah',
        'kondisi',
        'tanggal_masuk',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'kode_barang'   => 'required|min_length[3]|max_length[50]|is_unique[barang.kode_barang,id_barang,{id_barang}]',
        'nama_barang'   => 'required|min_length[3]|max_length[150]',
        'id_kategori'   => 'required|is_not_unique[kategori.id_kategori]',
        'id_lokasi'     => 'required|is_not_unique[lokasi.id_lokasi]',
        'jumlah'        => 'required|integer|greater_than_equal_to[0]',
        'kondisi'       => 'required|in_list[Baik,Rusak Ringan,Rusak Berat]',
        'tanggal_masuk' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'kode_barang' => [
            'required'   => 'Kode barang wajib diisi.',
            'min_length' => 'Kode barang minimal 3 karakter.',
            'is_unique'  => 'Kode barang sudah digunakan oleh perangkat lain.',
        ],
        'nama_barang' => [
            'required'   => 'Nama barang wajib diisi.',
            'min_length' => 'Nama barang minimal 3 karakter.',
        ],
        'id_kategori' => [
            'required'      => 'Kategori barang wajib dipilih.',
            'is_not_unique' => 'Kategori yang dipilih tidak valid.',
        ],
        'id_lokasi' => [
            'required'      => 'Lokasi penyimpanan wajib dipilih.',
            'is_not_unique' => 'Lokasi yang dipilih tidak valid.',
        ],
        'jumlah' => [
            'required'               => 'Jumlah stok wajib diisi.',
            'integer'                => 'Jumlah stok harus berupa bilangan bulat.',
            'greater_than_equal_to'  => 'Jumlah stok tidak boleh bernilai negatif.',
        ],
        'kondisi' => [
            'required' => 'Kondisi barang wajib dipilih.',
            'in_list'  => 'Pilihan kondisi barang harus Baik, Rusak Ringan, atau Rusak Berat.',
        ],
        'tanggal_masuk' => [
            'required'   => 'Tanggal masuk wajib diisi.',
            'valid_date' => 'Format tanggal masuk tidak valid.',
        ],
    ];

    /**
     * Mengambil daftar barang dengan relasi kategori dan lokasi, mendukung pencarian dan filter.
     */
    public function getBarangWithDetails($keyword = null, $id_kategori = null, $kondisi = null)
    {
        $builder = $this->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
                        ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
                        ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi');

        // Pencarian berdasarkan kode_barang atau nama_barang
        if (!empty($keyword)) {
            $builder->groupStart()
                    ->like('barang.kode_barang', $keyword)
                    ->orLike('barang.nama_barang', $keyword)
                    ->groupEnd();
        }

        // Filter berdasarkan kategori
        if (!empty($id_kategori)) {
            $builder->where('barang.id_kategori', $id_kategori);
        }

        // Filter berdasarkan kondisi
        if (!empty($kondisi)) {
            $builder->where('barang.kondisi', $kondisi);
        }

        return $builder->orderBy('barang.id_barang', 'DESC')->findAll();
    }

    /**
     * Mengambil satu data barang lengkap dengan nama kategori dan lokasi.
     */
    public function getBarangDetail($id)
    {
        return $this->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
                    ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
                    ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi')
                    ->where('barang.id_barang', $id)
                    ->first();
    }

    /**
     * Mengambil data barang yang tersedia untuk dipinjam (jumlah > 0).
     */
    public function getAvailableForLoan()
    {
        return $this->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
                    ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
                    ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi')
                    ->where('barang.jumlah >', 0)
                    ->orderBy('barang.nama_barang', 'ASC')
                    ->findAll();
    }
}
