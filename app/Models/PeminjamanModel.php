<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table            = 'peminjaman';
    protected $primaryKey       = 'id_peminjaman';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_barang',
        'nama_peminjam',
        'jumlah_pinjam',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'id_barang'      => 'required|is_not_unique[barang.id_barang]',
        'nama_peminjam'  => 'required|min_length[3]|max_length[100]',
        'jumlah_pinjam'  => 'required|integer|greater_than[0]',
        'tanggal_pinjam' => 'required|valid_date',
        'status'         => 'required|in_list[Dipinjam,Kembali]',
    ];

    /**
     * Mengambil daftar seluruh riwayat peminjaman beserta detail barang, kategori, dan lokasi.
     */
    public function getPeminjamanWithDetails($status = null, $limit = null)
    {
        $builder = $this->select('peminjaman.*, barang.kode_barang, barang.nama_barang, kategori.nama_kategori, lokasi.nama_lokasi')
                        ->join('barang', 'barang.id_barang = peminjaman.id_barang')
                        ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
                        ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi');

        if (!empty($status)) {
            $builder->where('peminjaman.status', $status);
        }

        if ($limit !== null) {
            $builder->limit((int) $limit);
        }

        // Urutkan yang masih 'Dipinjam' di bagian atas, kemudian berdasarkan tanggal terbaru
        return $builder->orderBy("CASE WHEN peminjaman.status = 'Dipinjam' THEN 0 ELSE 1 END", 'ASC')
                       ->orderBy('peminjaman.id_peminjaman', 'DESC')
                       ->findAll();
    }

    /**
     * Memproses transaksi peminjaman baru secara atomik (mengurangi stok barang).
     */
    public function pinjamBarang(array $data)
    {
        $db = \Config\Database::connect();
        $barangModel = new \App\Models\BarangModel();

        $db->transStart();

        // 1. Verifikasi stok terkini di database
        $barang = $barangModel->find($data['id_barang']);
        if (!$barang) {
            $db->transRollback();
            return ['status' => false, 'message' => 'Data barang tidak ditemukan.'];
        }

        $jumlahPinjam = (int)$data['jumlah_pinjam'];
        $stokTersedia = (int)$barang['jumlah'];

        if ($jumlahPinjam <= 0) {
            $db->transRollback();
            return ['status' => false, 'message' => 'Jumlah pinjam harus lebih dari 0.'];
        }

        if ($jumlahPinjam > $stokTersedia) {
            $db->transRollback();
            return [
                'status'  => false, 
                'message' => "Stok barang '{$barang['nama_barang']}' tidak mencukupi (Tersedia: {$stokTersedia}, Diminta: {$jumlahPinjam})."
            ];
        }

        // 2. Simpan record peminjaman dengan status 'Dipinjam'
        $saved = $this->save([
            'id_barang'       => $data['id_barang'],
            'nama_peminjam'   => $data['nama_peminjam'],
            'jumlah_pinjam'   => $jumlahPinjam,
            'tanggal_pinjam'  => $data['tanggal_pinjam'],
            'tanggal_kembali' => null,
            'status'          => 'Dipinjam',
        ]);

        if (!$saved) {
            $db->transRollback();
            $errors = $this->errors();

            return [
                'status'  => false,
                'message' => 'Gagal menyimpan transaksi peminjaman: ' . implode(' ', $errors ?: ['data tidak valid.']),
            ];
        }

        // 3. Kurangi stok barang
        $sisaStok = $stokTersedia - $jumlahPinjam;
        $stokDiperbarui = $barangModel->update($data['id_barang'], [
            'jumlah' => $sisaStok,
        ]);

        if (!$stokDiperbarui) {
            $db->transRollback();
            return ['status' => false, 'message' => 'Gagal memperbarui stok barang pada database.'];
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['status' => false, 'message' => 'Gagal memproses transaksi peminjaman pada database.'];
        }

        return ['status' => true, 'message' => 'Transaksi peminjaman berhasil dicatat dan stok barang telah dikurangi.'];
    }

    /**
     * Memproses pengembalian barang secara atomik (memulihkan stok barang).
     */
    public function kembalikanBarang($idPeminjaman, $tanggalKembali)
    {
        $db = \Config\Database::connect();
        $barangModel = new \App\Models\BarangModel();

        $db->transStart();

        $peminjaman = $this->find($idPeminjaman);
        if (!$peminjaman) {
            $db->transRollback();
            return ['status' => false, 'message' => 'Data peminjaman tidak ditemukan.'];
        }

        if ($peminjaman['status'] === 'Kembali') {
            $db->transRollback();
            return ['status' => false, 'message' => 'Barang pada transaksi ini sudah pernah dikembalikan sebelumnya.'];
        }

        $barang = $barangModel->find($peminjaman['id_barang']);
        if (!$barang) {
            $db->transRollback();
            return ['status' => false, 'message' => 'Data barang tidak ditemukan.'];
        }

        // 1. Update status transaksi peminjaman menjadi 'Kembali' dan set tanggal_kembali
        $transaksiDiperbarui = $this->update($idPeminjaman, [
            'status'          => 'Kembali',
            'tanggal_kembali' => $tanggalKembali,
        ]);

        if (!$transaksiDiperbarui) {
            $db->transRollback();
            $errors = $this->errors();

            return [
                'status'  => false,
                'message' => 'Gagal memperbarui transaksi pengembalian: ' . implode(' ', $errors ?: ['data tidak valid.']),
            ];
        }

        // 2. Pulihkan jumlah stok barang
        $stokBaru = (int)$barang['jumlah'] + (int)$peminjaman['jumlah_pinjam'];
        $stokDiperbarui = $barangModel->update($peminjaman['id_barang'], [
            'jumlah' => $stokBaru,
        ]);

        if (!$stokDiperbarui) {
            $db->transRollback();
            return ['status' => false, 'message' => 'Gagal memulihkan stok barang pada database.'];
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['status' => false, 'message' => 'Gagal memproses pengembalian barang pada database.'];
        }

        return ['status' => true, 'message' => 'Pengembalian barang berhasil dicatat dan stok barang telah dipulihkan.'];
    }
}
