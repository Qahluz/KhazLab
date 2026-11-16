<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
$stokTersedia = $ringkasan['stok_tersedia'];
$dipinjam     = $ringkasan['dipinjam'];
$totalAset    = $ringkasan['total_aset'];
$totalItem    = max(1, $ringkasan['total_jenis']);
$kondisiBaik  = $ringkasan['baik'];
$kondisiRingan = $ringkasan['rusak_ringan'];
$kondisiBerat = $ringkasan['rusak_berat'];
?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h2 class="h5 fw-bold mb-1 text-dark">Dashboard Analitik Aset Laboratorium</h2>
        <p class="text-muted small mb-0">Rekapitulasi fisik aset, komposisi kondisi, dan tren sirkulasi perangkat laboratorium komputer.</p>
    </div>
    <a href="<?= base_url('dashboard/export-csv') ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
        <i class="bi bi-download"></i><span>Unduh Rekap (CSV)</span>
    </a>
</div>

<div class="stat-grid">
    <div class="stat-card" style="--stat-color:#1d4ed8; --stat-bg:#e0e9ff">
        <i class="bi bi-boxes stat-icon"></i>
        <div class="stat-label">Total Aset Fisik</div>
        <div class="stat-value"><?= number_format($totalAset, 0, ',', '.') ?></div>
        <div class="stat-hint">Seluruh unit yang tercatat</div>
    </div>
    <div class="stat-card" style="--stat-color:#0d9488; --stat-bg:#ccfbf1">
        <i class="bi bi-archive stat-icon"></i>
        <div class="stat-label">Stok Tersedia</div>
        <div class="stat-value"><?= number_format($stokTersedia, 0, ',', '.') ?></div>
        <div class="stat-hint"><?= $totalAset > 0 ? round($stokTersedia / $totalAset * 100) : 0 ?>% dari total aset</div>
    </div>
    <div class="stat-card" style="--stat-color:#b45309; --stat-bg:#fef3c7">
        <i class="bi bi-arrow-left-right stat-icon"></i>
        <div class="stat-label">Unit Dipinjam</div>
        <div class="stat-value"><?= $dipinjam ?></div>
        <div class="stat-hint"><?= $totalAset > 0 ? round($dipinjam / $totalAset * 100) : 0 ?>% utilisasi</div>
    </div>
    <div class="stat-card" style="--stat-color:#7c3aed; --stat-bg:#ede9fe">
        <i class="bi bi-collection stat-icon"></i>
        <div class="stat-label">Jenis Barang</div>
        <div class="stat-value"><?= $ringkasan['total_jenis'] ?></div>
        <div class="stat-hint">Tersebar di <?= count($lokasi) ?> ruangan</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header"><span><i class="bi bi-bar-chart-line me-1 text-primary"></i>Tren Peminjaman 6 Bulan</span></div>
            <div class="p-3"><div class="chart-box"><canvas id="chartTren"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header"><span><i class="bi bi-pie-chart me-1 text-primary"></i>Komposisi Kondisi</span></div>
            <div class="p-3"><div class="chart-box"><canvas id="chartKondisi"></canvas></div></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header"><span><i class="bi bi-tags me-1 text-primary"></i>Sebaran Jenis Barang per Kategori</span></div>
            <div class="p-3"><div class="chart-box"><canvas id="chartKategori"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-custom">
            <div class="card-header"><span><i class="bi bi-geo-alt me-1 text-primary"></i>Sebaran Unit per Lokasi</span></div>
            <div class="p-3"><div class="chart-box"><canvas id="chartLokasi"></canvas></div></div>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="card-header"><span><i class="bi bi-list-check me-1 text-primary"></i>Rincian Perangkat &amp; Utilisasi</span></div>
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr><th>Kode</th><th>Nama Perangkat</th><th>Kategori</th><th>Lokasi</th><th class="text-center">Stok</th><th class="text-center">Dipinjam</th><th class="text-center">Total</th><th>Kondisi</th></tr>
            </thead>
            <tbody>
            <?php if (empty($barangList)) : ?>
                <tr><td colspan="8">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                        <h6>Belum ada data perangkat</h6>
                        <p>Tambahkan perangkat laboratorium untuk melihat analitik utilisasi.</p>
                        <a href="<?= base_url('barang/tambah') ?>" class="btn btn-sm btn-primary">Tambah Barang</a>
                    </div>
                </td></tr>
            <?php else : ?>
                <?php foreach ($barangList as $item) : ?>
                    <?php $unitDipinjam = $stok[$item['id_barang']] ?? 0; ?>
                    <tr>
                        <td class="mono"><?= esc($item['kode_barang']) ?></td>
                        <td class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></td>
                        <td class="small text-muted"><?= esc($item['nama_kategori']) ?></td>
                        <td class="small text-muted"><?= esc($item['nama_lokasi']) ?></td>
                        <td class="text-center"><?= (int) $item['jumlah'] ?></td>
                        <td class="text-center"><?= $unitDipinjam ?></td>
                        <td class="text-center fw-semibold"><?= (int) $item['jumlah'] + $unitDipinjam ?></td>
                        <td>
                            <?php $kelas = ['Baik' => 'badge-kondisi-baik', 'Rusak Ringan' => 'badge-kondisi-rusak-ringan', 'Rusak Berat' => 'badge-kondisi-rusak-berat'][$item['kondisi']] ?? 'badge-kondisi-baik'; ?>
                            <span class="<?= $kelas ?>"><?= esc($item['kondisi']) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    const tren = <?= json_encode($tren) ?>;
    const kondisi = <?= json_encode([$kondisiBaik, $kondisiRingan, $kondisiBerat]) ?>;
    const kategori = <?= json_encode(['labels' => array_column($kategori, 'nama_kategori'), 'data' => array_map('intval', array_column($kategori, 'total_barang'))]) ?>;
    const lokasi = <?= json_encode(['labels' => array_column($lokasi, 'nama_lokasi'), 'data' => array_map('intval', array_column($lokasi, 'total_barang'))]) ?>;

    Chart.defaults.font.family = '"Segoe UI", system-ui, sans-serif';
    Chart.defaults.color = '#64748b';
    const gridColor = 'rgba(148,163,184,.25)';
    const baseBar = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: gridColor } }, x: { grid: { display: false } } }
    };

    new Chart(document.getElementById('chartTren'), {
        type: 'bar',
        data: {
            labels: tren.labels,
            datasets: [{
                label: 'Transaksi', data: tren.data,
                backgroundColor: 'rgba(29,78,216,.75)', borderRadius: 6, maxBarThickness: 42
            }]
        },
        options: baseBar
    });

    new Chart(document.getElementById('chartKondisi'), {
        type: 'doughnut',
        data: {
            labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
            datasets: [{ data: kondisi, backgroundColor: ['#16a34a', '#f59e0b', '#dc2626'], borderWidth: 2, borderColor: '#fff' }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 12 } } } }
    });

    new Chart(document.getElementById('chartKategori'), {
        type: 'bar',
        data: { labels: kategori.labels, datasets: [{ label: 'Jenis barang', data: kategori.data, backgroundColor: 'rgba(13,148,136,.8)', borderRadius: 6, maxBarThickness: 38 }] },
        options: baseBar
    });

    new Chart(document.getElementById('chartLokasi'), {
        type: 'bar',
        data: { labels: lokasi.labels, datasets: [{ label: 'Unit', data: lokasi.data, backgroundColor: 'rgba(124,58,237,.8)', borderRadius: 6, maxBarThickness: 38 }] },
        options: { ...baseBar, indexAxis: 'y' }
    });
</script>
<?= $this->endSection() ?>
