<?php

namespace App\Models;

use App\Libraries\PeminjamanStatus;
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
        'tanggal_jatuh_tempo',
        'tanggal_kembali',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'id_barang'           => 'required|is_not_unique[barang.id_barang]',
        'nama_peminjam'       => 'required|min_length[3]|max_length[100]',
        'jumlah_pinjam'       => 'required|integer|greater_than[0]',
        'tanggal_pinjam'      => 'required|valid_date',
        'tanggal_jatuh_tempo' => 'required|valid_date',
        'status'              => 'required|in_list[Dipinjam,Kembali]',
    ];

    public function getPeminjamanWithDetails(array $params = []): array
    {
        $status  = $params['status'] ?? null;
        $keyword = trim((string) ($params['keyword'] ?? ''));
        $page    = max(1, (int) ($params['page'] ?? 1));
        $perPage = max(1, (int) ($params['perPage'] ?? 10));

        $this->select('peminjaman.*, barang.kode_barang, barang.nama_barang, kategori.nama_kategori, lokasi.nama_lokasi')
            ->join('barang', 'barang.id_barang = peminjaman.id_barang')
            ->join('kategori', 'kategori.id_kategori = barang.id_kategori')
            ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi');

        if ($keyword !== '') {
            $this->groupStart()
                ->like('peminjaman.nama_peminjam', $keyword)
                ->orLike('barang.nama_barang', $keyword)
                ->orLike('barang.kode_barang', $keyword)
                ->groupEnd();
        }

        if ($status === 'Terlambat') {
            $this->where('peminjaman.status', 'Dipinjam')
                ->where('peminjaman.tanggal_jatuh_tempo <', date('Y-m-d'));
        } elseif (! empty($status)) {
            $this->where('peminjaman.status', $status);
        }

        $total = $this->countAllResults(false);

        $rows = $this->orderBy("CASE WHEN peminjaman.status = 'Dipinjam' THEN 0 ELSE 1 END", 'ASC', false)
            ->orderBy('peminjaman.tanggal_jatuh_tempo', 'ASC')
            ->orderBy('peminjaman.id_peminjaman', 'DESC')
            ->paginate($perPage, 'peminjaman', $page);

        return ['rows' => $rows, 'pager' => $this->pager, 'total' => $total];
    }

    public function getPeminjamanTerkini(int $limit = 5): array
    {
        return $this->select('peminjaman.*, barang.kode_barang, barang.nama_barang, lokasi.nama_lokasi')
            ->join('barang', 'barang.id_barang = peminjaman.id_barang')
            ->join('lokasi', 'lokasi.id_lokasi = barang.id_lokasi')
            ->orderBy('peminjaman.id_peminjaman', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getTerlambat(): array
    {
        return $this->select('peminjaman.*, barang.kode_barang, barang.nama_barang')
            ->join('barang', 'barang.id_barang = peminjaman.id_barang')
            ->where('peminjaman.status', 'Dipinjam')
            ->where('peminjaman.tanggal_jatuh_tempo <', date('Y-m-d'))
            ->orderBy('peminjaman.tanggal_jatuh_tempo', 'ASC')
            ->findAll();
    }

    public function countByIdBarang(int $idBarang): int
    {
        return $this->where('id_barang', $idBarang)->countAllResults();
    }

    public function getRingkasan(): array
    {
        $row = $this->db->query(
            "SELECT SUM(status = 'Dipinjam') AS dipinjam,
                    SUM(status = 'Kembali') AS kembali,
                    SUM(status = 'Dipinjam' AND tanggal_jatuh_tempo < CURDATE()) AS terlambat,
                    COUNT(*) AS total
             FROM peminjaman"
        )->getRowArray();

        return [
            'dipinjam'  => (int) $row['dipinjam'],
            'kembali'   => (int) $row['kembali'],
            'terlambat' => (int) $row['terlambat'],
            'total'     => (int) $row['total'],
        ];
    }

    public function getUnitDipinjamPerBarang(): array
    {
        $rows = $this->select('id_barang, SUM(jumlah_pinjam) AS dipinjam')
            ->where('status', 'Dipinjam')
            ->groupBy('id_barang')
            ->findAll();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['id_barang']] = (int) $row['dipinjam'];
        }

        return $map;
    }

    public function getTrenPeminjaman(int $bulan = 6): array
    {
        $start = date('Y-m-01', strtotime('-' . ($bulan - 1) . ' months'));

        $rows = $this->select("DATE_FORMAT(tanggal_pinjam, '%Y-%m') AS bulan, COUNT(*) AS total")
            ->where('tanggal_pinjam >=', $start)
            ->groupBy('bulan')
            ->orderBy('bulan', 'ASC')
            ->findAll();

        $map = array_column($rows, 'total', 'bulan');

        $namaBulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $labels    = [];
        $data      = [];
        for ($i = 0; $i < $bulan; $i++) {
            $key      = date('Y-m', strtotime("{$start} +{$i} months"));
            $labels[] = $namaBulan[(int) date('n', strtotime($key . '-01'))] . ' ' . date('y', strtotime($key . '-01'));
            $data[]   = (int) ($map[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function pinjamBarang(array $data): array
    {
        $db          = \Config\Database::connect();
        $barangModel = new BarangModel();
        $idBarang    = (int) ($data['id_barang'] ?? 0);
        $jumlah      = (int) ($data['jumlah_pinjam'] ?? 0);
        $tanggal     = (string) ($data['tanggal_pinjam'] ?? date('Y-m-d'));
        $jatuhTempo  = ! empty($data['tanggal_jatuh_tempo'])
            ? (string) $data['tanggal_jatuh_tempo']
            : PeminjamanStatus::defaultJatuhTempo($tanggal);

        if ($idBarang <= 0 || $jumlah <= 0) {
            return ['status' => false, 'message' => 'Data peminjaman tidak valid.'];
        }

        if ($jatuhTempo < $tanggal) {
            return ['status' => false, 'message' => 'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal pinjam.'];
        }

        $db->transBegin();

        try {
            $barang = $barangModel->find($idBarang);
            if (! $barang) {
                $db->transRollback();

                return ['status' => false, 'message' => 'Data barang tidak ditemukan.'];
            }

            // Penjagaan stok atomik: kondisi evaluated oleh InnoDB di bawah row lock,
            // sehingga dua request paralel tidak dapat menembus stok yang sama.
            $db->query(
                'UPDATE barang SET jumlah = jumlah - ?, updated_at = ? WHERE id_barang = ? AND jumlah >= ?',
                [$jumlah, date('Y-m-d H:i:s'), $idBarang, $jumlah]
            );

            if ($db->affectedRows() !== 1) {
                $db->transRollback();
                $stokTersedia = (int) $db->query('SELECT jumlah FROM barang WHERE id_barang = ?', [$idBarang])->getRow()->jumlah;

                return [
                    'status'  => false,
                    'message' => "Stok barang '{$barang['nama_barang']}' tidak mencukupi (Tersedia: {$stokTersedia}, Diminta: {$jumlah}).",
                ];
            }

            $insertId = $this->insert([
                'id_barang'           => $idBarang,
                'nama_peminjam'       => $data['nama_peminjam'],
                'jumlah_pinjam'       => $jumlah,
                'tanggal_pinjam'      => $tanggal,
                'tanggal_jatuh_tempo' => $jatuhTempo,
                'tanggal_kembali'     => null,
                'status'              => 'Dipinjam',
            ]);

            if (! $insertId || $this->db->error()['code'] !== 0 || ! $this->db->transStatus()) {
                $db->transRollback();
                log_message('error', 'Insert peminjaman gagal: errors={errors} db={db}', [
                    'errors' => json_encode($this->errors()),
                    'db'     => json_encode($this->db->error()),
                ]);

                return ['status' => false, 'message' => 'Gagal menyimpan transaksi peminjaman. Periksa kembali data yang diisikan.'];
            }

            $db->transCommit();

            return [
                'status'  => true,
                'message' => 'Peminjaman ' . $barang['nama_barang'] . ' berhasil dicatat. Jatuh tempo ' . date('d/m/Y', strtotime($jatuhTempo)) . '.',
            ];
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Gagal memproses peminjaman: {message}', ['message' => $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine()]);

            return ['status' => false, 'message' => 'Transaksi peminjaman gagal diproses. Silakan coba lagi.'];
        }
    }

    public function kembalikanBarang(int $idPeminjaman, string $tanggalKembali): array
    {
        $db = \Config\Database::connect();

        $db->transBegin();

        try {
            $locked = $db->query(
                'SELECT id_peminjaman, id_barang, jumlah_pinjam, status, tanggal_jatuh_tempo FROM peminjaman WHERE id_peminjaman = ? FOR UPDATE',
                [$idPeminjaman]
            )->getRowArray();

            if (! $locked) {
                $db->transRollback();

                return ['status' => false, 'message' => 'Data peminjaman tidak ditemukan.'];
            }

            if ($locked['status'] === 'Kembali') {
                $db->transRollback();

                return ['status' => false, 'message' => 'Barang pada transaksi ini sudah pernah dikembalikan sebelumnya.'];
            }

            $db->query(
                'UPDATE peminjaman SET status = ?, tanggal_kembali = ?, updated_at = ? WHERE id_peminjaman = ? AND status = ?',
                ['Kembali', $tanggalKembali, date('Y-m-d H:i:s'), $idPeminjaman, 'Dipinjam']
            );

            if ($db->affectedRows() !== 1) {
                $db->transRollback();

                return ['status' => false, 'message' => 'Status peminjaman berubah saat diproses. Muat ulang halaman lalu coba lagi.'];
            }

            $db->query(
                'UPDATE barang SET jumlah = jumlah + ?, updated_at = ? WHERE id_barang = ?',
                [(int) $locked['jumlah_pinjam'], date('Y-m-d H:i:s'), (int) $locked['id_barang']]
            );

            $db->transCommit();

            $telat = $locked['tanggal_jatuh_tempo'] && $tanggalKembali > $locked['tanggal_jatuh_tempo'];

            return [
                'status'  => true,
                'message' => 'Pengembalian berhasil dicatat dan stok telah dipulihkan' . ($telat ? ' (keterlambatan tercatat sesuai tanggal jatuh tempo).' : '.'),
            ];
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Gagal memproses pengembalian: {message}', ['message' => $e->getMessage()]);

            return ['status' => false, 'message' => 'Pengembalian gagal diproses. Silakan coba lagi.'];
        }
    }
}
