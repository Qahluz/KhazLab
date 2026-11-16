<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$totalAset = $ringkasan['total_aset'];
$persenDipinjam = $totalAset > 0 ? round($ringkasan['dipinjam'] / $totalAset * 100) : 0;
?>

<div class="stat-grid">
    <div class="stat-card" style="--stat-color:#1d4ed8; --stat-bg:#e0e9ff">
        <i class="bi bi-boxes stat-icon"></i>
        <div class="stat-label">Total Aset Fisik</div>
        <div class="stat-value"><?= number_format($totalAset, 0, ',', '.') ?></div>
        <div class="stat-hint"><?= $ringkasan['stok_tersedia'] ?> unit tersedia &middot; <?= $ringkasan['dipinjam'] ?> dipinjam</div>
    </div>
    <div class="stat-card" style="--stat-color:#0d9488; --stat-bg:#ccfbf1">
        <i class="bi bi-archive stat-icon"></i>
        <div class="stat-label">Stok Tersedia</div>
        <div class="stat-value"><?= number_format($ringkasan['stok_tersedia'], 0, ',', '.') ?></div>
        <div class="stat-hint">Dari <?= $ringkasan['total_jenis'] ?> jenis perangkat</div>
    </div>
    <div class="stat-card" style="--stat-color:#b45309; --stat-bg:#fef3c7">
        <i class="bi bi-arrow-left-right stat-icon"></i>
        <div class="stat-label">Sedang Dipinjam</div>
        <div class="stat-value"><?= $ringkasan['dipinjam'] ?></div>
        <div class="stat-hint"><?= $persenDipinjam ?>% dari total aset</div>
    </div>
    <div class="stat-card" style="--stat-color:#b91c1c; --stat-bg:#fee2e2">
        <i class="bi bi-exclamation-octagon stat-icon"></i>
        <div class="stat-label">Terlambat Kembali</div>
        <div class="stat-value"><?= count($peminjamanTelat) ?></div>
        <div class="stat-hint"><?= $ringkasanPinjam['kembali'] ?> transaksi selesai</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-custom">
            <div class="card-header">
                <span><i class="bi bi-graph-up me-1 text-primary"></i>Tren Peminjaman 6 Bulan Terakhir</span>
                <a href="<?= base_url('dashboard/analitik') ?>" class="btn btn-sm btn-outline-primary">Analitik Lengkap</a>
            </div>
            <div class="p-3">
                <div class="chart-box"><canvas id="chartTren" aria-label="Grafik tren peminjaman" role="img"></canvas></div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-custom">
            <div class="card-header"><span><i class="bi bi-pie-chart me-1 text-primary"></i>Komposisi Kondisi Aset</span></div>
            <div class="p-3">
                <div class="chart-box" style="height:300px"><canvas id="chartKondisi" aria-label="Grafik komposisi kondisi aset" role="img"></canvas></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">
                <span><i class="bi bi-exclamation-triangle me-1 text-warning"></i>Stok Menipis (maks 3 unit)</span>
                <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-link text-decoration-none">Kelola</a>
            </div>
            <?php if (empty($barangStokMenipis)) : ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-check2-circle"></i></div>
                    <h6>Stok aman</h6>
                    <p>Tidak ada perangkat dengan stok di bawah ambang minimum.</p>
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead><tr><th>Kode</th><th>Barang</th><th class="text-center">Stok</th><th>Lokasi</th></tr></thead>
                        <tbody>
                        <?php foreach ($barangStokMenipis as $item) : ?>
                            <tr>
                                <td class="mono"><?= esc($item['kode_barang']) ?></td>
                                <td class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></td>
                                <td class="text-center"><span class="badge-status-terlambat"><?= (int) $item['jumlah'] ?></span></td>
                                <td class="text-muted small"><?= esc($item['nama_lokasi']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header">
                <span><i class="bi bi-tools me-1 text-danger"></i>Perangkat Rusak Berat</span>
                <span class="badge-kondisi-rusak-berat"><?= count($barangRusakBerat) ?> unit</span>
            </div>
            <?php if (empty($barangRusakBerat)) : ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-emoji-smile"></i></div>
                    <h6>Semua perangkat berfungsi</h6>
                    <p>Belum ada perangkat yang tercatat rusak berat.</p>
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead><tr><th>Kode</th><th>Barang</th><th>Kategori</th><th class="text-center">Unit</th></tr></thead>
                        <tbody>
                        <?php foreach ($barangRusakBerat as $item) : ?>
                            <tr>
                                <td class="mono"><?= esc($item['kode_barang']) ?></td>
                                <td class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></td>
                                <td class="text-muted small"><?= esc($item['nama_kategori']) ?></td>
                                <td class="text-center"><?= (int) $item['jumlah'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card-custom">
            <div class="card-header">
                <span><i class="bi bi-clock-history me-1 text-primary"></i>Peminjaman Terbaru</span>
                <a href="<?= base_url('peminjaman') ?>" class="btn btn-sm btn-link text-decoration-none">Lihat semua</a>
            </div>
            <?php if (empty($peminjamanTerbaru)) : ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                    <h6>Belum ada transaksi</h6>
                    <p>Catat peminjaman pertama melalui menu Peminjaman.</p>
                    <a href="<?= base_url('peminjaman/tambah') ?>" class="btn btn-sm btn-primary">Catat Peminjaman</a>
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-custom align-middle">
                        <thead><tr><th>Peminjam</th><th>Barang</th><th>Jatuh Tempo</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($peminjamanTerbaru as $row) : ?>
                            <?php $label = \App\Libraries\PeminjamanStatus::label($row); ?>
                            <tr>
                                <td class="fw-semibold text-dark"><?= esc($row['nama_peminjam']) ?></td>
                                <td class="small"><?= esc($row['nama_barang']) ?></td>
                                <td class="small text-muted"><?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?></td>
                                <td><span class="<?= \App\Libraries\PeminjamanStatus::badgeClass($label) ?>"><?= $label ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-custom">
            <div class="card-header"><span><i class="bi bi-alarm me-1 text-danger"></i>Peringatan Terlambat</span></div>
            <?php if (empty($peminjamanTelat)) : ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-check2-circle"></i></div>
                    <h6>Tidak ada keterlambatan</h6>
                    <p>Semua peminjaman aktif masih dalam masa jatuh tempo.</p>
                </div>
            <?php else : ?>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($peminjamanTelat as $row) : ?>
                        <li class="d-flex align-items-start gap-2 p-3 border-bottom">
                            <span class="badge-status-terlambat"><?= \App\Libraries\PeminjamanStatus::hariTerlambat($row) ?> hari</span>
                            <div class="small">
                                <div class="fw-semibold text-dark"><?= esc($row['nama_peminjam']) ?></div>
                                <div class="text-muted"><?= esc($row['nama_barang']) ?> &middot; jatuh tempo <?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    const tren = <?= json_encode($trenPeminjaman) ?>;
    const kondisi = <?= json_encode([$ringkasan['baik'], $ringkasan['rusak_ringan'], $ringkasan['rusak_berat']]) ?>;

    const gridColor = 'rgba(148,163,184,.25)';
    Chart.defaults.font.family = '"Segoe UI", system-ui, sans-serif';
    Chart.defaults.color = '#64748b';

    new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: tren.labels,
            datasets: [{
                label: 'Transaksi peminjaman',
                data: tren.data,
                borderColor: '#1d4ed8',
                backgroundColor: 'rgba(29,78,216,.12)',
                fill: true,
                tension: .35,
                pointRadius: 4,
                pointBackgroundColor: '#1d4ed8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: gridColor } }, x: { grid: { display: false } } }
        }
    });

    new Chart(document.getElementById('chartKondisi'), {
        type: 'doughnut',
        data: {
            labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
            datasets: [{ data: kondisi, backgroundColor: ['#16a34a', '#f59e0b', '#dc2626'], borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 14 } } }
        }
    });
</script>
<?= $this->endSection() ?>
