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

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $skipValidation = true;

    protected $validationRules = [
        'kode_barang'   => 'required|min_length[3]|max_length[50]|is_unique[barang.kode_barang]',
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
            'required'              => 'Jumlah stok wajib diisi.',
            'integer'               => 'Jumlah stok harus berupa bilangan bulat.',
            'greater_than_equal_to' => 'Jumlah stok tidak boleh bernilai negatif.',
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

    public function getRulesForUpdate(int $id): array
    {
        $rules                = $this->getValidationRules();
        $rules['kode_barang'] = "required|min_length[3]|max_length[50]|is_unique[barang.kode_barang,id_barang,{$id}]";

        return $rules;
    }

    public function getBarangWithDetails(array $filters = [], int $perPage = 0, int $page = 1): array
    {
        $builder = $this->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
            ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
            ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi');

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $builder->groupStart()
                ->like('barang.kode_barang', $keyword)
                ->orLike('barang.nama_barang', $keyword)
                ->groupEnd();
        }

        foreach (['id_kategori' => 'barang.id_kategori', 'id_lokasi' => 'barang.id_lokasi', 'kondisi' => 'barang.kondisi'] as $key => $column) {
            if (! empty($filters[$key])) {
                $builder->where($column, $filters[$key]);
            }
        }

        $builder->orderBy('barang.id_barang', 'DESC');

        return $perPage > 0
            ? $builder->paginate($perPage, 'barang', $page)
            : $builder->findAll();
    }

    public function getBarangDetail($id)
    {
        return $this->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
            ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
            ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi')
            ->where('barang.id_barang', $id)
            ->first();
    }

    public function getAvailableForLoan(): array
    {
        return $this->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
            ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
            ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi')
            ->where('barang.jumlah >', 0)
            ->orderBy('barang.nama_barang', 'ASC')
            ->findAll();
    }

    public function getStokMenipis(int $ambang): array
    {
        return $this->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
            ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
            ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi')
            ->where('barang.jumlah <=', $ambang)
            ->orderBy('barang.jumlah', 'ASC')
            ->findAll();
    }

    public function getRusakBerat(): array
    {
        return $this->select('barang.*, kategori.nama_kategori, lokasi.nama_lokasi')
            ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
            ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi')
            ->where('barang.kondisi', 'Rusak Berat')
            ->orderBy('barang.nama_barang', 'ASC')
            ->findAll();
    }

    public function getRingkasanAset(): array
    {
        $row = $this->db->query(
            "SELECT COALESCE(SUM(jumlah), 0) AS stok_tersedia,
                    COUNT(*) AS total_jenis,
                    SUM(kondisi = 'Baik') AS baik,
                    SUM(kondisi = 'Rusak Ringan') AS rusak_ringan,
                    SUM(kondisi = 'Rusak Berat') AS rusak_berat
             FROM barang"
        )->getRowArray();

        $dipinjam = (int) $this->db->query(
            "SELECT COALESCE(SUM(jumlah_pinjam), 0) AS total FROM peminjaman WHERE status = 'Dipinjam'"
        )->getRow()->total;

        return [
            'total_jenis'    => (int) $row['total_jenis'],
            'stok_tersedia'  => (int) $row['stok_tersedia'],
            'dipinjam'       => $dipinjam,
            'total_aset'     => (int) $row['stok_tersedia'] + $dipinjam,
            'baik'           => (int) $row['baik'],
            'rusak_ringan'   => (int) $row['rusak_ringan'],
            'rusak_berat'    => (int) $row['rusak_berat'],
        ];
    }
}
