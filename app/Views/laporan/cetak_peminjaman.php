<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-1">
                <li class="breadcrumb-item"><a href="<?= base_url('laporan') ?>" class="text-decoration-none">Laporan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cetak Peminjaman</li>
            </ol>
        </nav>
        <h2 class="h5 fw-bold mb-0 text-dark">Laporan Peminjaman &amp; Pengembalian</h2>
    </div>
    <div class="d-flex gap-2 no-print">
        <a href="<?= base_url('laporan') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
        <button type="button" class="btn btn-sm btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Cetak</button>
    </div>
</div>

<div class="card-custom">
    <div class="p-4">
        <div class="text-center border-bottom pb-3 mb-3">
            <h3 class="h5 fw-bold mb-1 text-dark">LABORATORIUM KOMPUTER - PROGRAM STUDI SISTEM INFORMASI</h3>
            <div class="small text-muted">Sistem Informasi Inventaris Laboratorium (<?= esc('<KhazLab>') ?>)</div>
            <div class="small text-muted">Jl. Kampus Praktikum No. 1, Jakarta &middot; labkom@kampus.ac.id</div>
        </div>

        <div class="d-flex justify-content-between flex-wrap small mb-3">
            <div><strong>Jenis laporan:</strong> Riwayat Peminjaman &amp; Pengembalian Aset</div>
            <div><strong>Tanggal cetak:</strong> <?= esc($tanggalCetak) ?></div>
        </div>

        <div class="row g-2 mb-3 small">
            <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted">Total Transaksi</div><div class="fw-bold"><?= $ringkasan['total'] ?></div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted">Sedang Dipinjam</div><div class="fw-bold"><?= $ringkasan['dipinjam'] ?></div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted">Terlambat</div><div class="fw-bold"><?= $ringkasan['terlambat'] ?></div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted">Sudah Kembali</div><div class="fw-bold"><?= $ringkasan['kembali'] ?></div></div></div>
        </div>

        <table class="table table-custom table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width:42px">No</th>
                    <th>Peminjam</th>
                    <th>Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Tgl Pinjam</th>
                    <th class="text-center">Jatuh Tempo</th>
                    <th class="text-center">Tgl Kembali</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($peminjaman)) : ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada transaksi peminjaman pada filter ini.</td></tr>
            <?php else : ?>
                <?php $no = 1; foreach ($peminjaman as $row) : ?>
                    <?php $label = \App\Libraries\PeminjamanStatus::label($row); ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= esc($row['nama_peminjam']) ?></td>
                        <td>
                            <?= esc($row['nama_barang']) ?>
                            <div class="mono text-muted"><?= esc($row['kode_barang']) ?></div>
                        </td>
                        <td class="text-center"><?= (int) $row['jumlah_pinjam'] ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?></td>
                        <td class="text-center"><?= $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-' ?></td>
                        <td class="text-center"><?= $label ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="row pt-4 mt-4 text-center small">
            <div class="col-6">
                <p class="mb-5">Mengetahui,<br><strong>Kepala Laboratorium Komputer</strong></p>
                <div class="fw-bold text-dark text-decoration-underline">I Wayan Widi Pradnyana, M.TI</div>
                <div class="text-muted">NIP. 198106252025211045</div>
            </div>
            <div class="col-6">
                <p class="mb-5">Jakarta, <?= esc($tanggalCetak) ?><br><strong>Petugas Pengelola Inventaris</strong></p>
                <div class="fw-bold text-dark text-decoration-underline"><?= esc(session()->get('username') ?? 'Laboran Komputer') ?></div>
                <div class="text-muted">Administrator Laboratorium</div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
