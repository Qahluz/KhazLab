<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h4 fw-bold mb-1">Ringkasan Sistem Inventaris</h1>
        <p class="text-muted small mb-0">Informasi ringkas mengenai kondisi aset dan sirkulasi laboratorium komputer</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('barang/tambah') ?>" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Barang</span>
        </a>
        <a href="<?= base_url('peminjaman/tambah') ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left-right"></i>
            <span>Peminjaman Baru</span>
        </a>
    </div>
</div>

<!-- 4 Kartu Metrik Utama -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Jenis Barang</span>
                    <h3 class="fw-bold my-1 text-dark"><?= number_format($totalJenisBarang) ?></h3>
                    <span class="text-muted small"><i class="bi bi-collection me-1"></i>Katalog Aset</span>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3 fs-4">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-custom h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Stok Tersedia</span>
                    <h3 class="fw-bold my-1 text-success"><?= number_format($totalUnitBarang) ?></h3>
                    <span class="text-muted small"><i class="bi bi-layers me-1"></i>Unit Tersedia</span>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-3 fs-4">
                    <i class="bi bi-hdd-stack"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-custom h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Sedang Dipinjam</span>
                    <h3 class="fw-bold my-1 text-warning"><?= number_format($totalSedangDipinjam) ?></h3>
                    <span class="text-muted small"><i class="bi bi-clock-history me-1"></i>Unit di Luar Lab</span>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-3 fs-4">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-custom h-100 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Kategori & Lokasi</span>
                    <h3 class="fw-bold my-1 text-info"><?= $totalKategori ?> / <?= $totalLokasi ?></h3>
                    <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>Kelompok / Ruang</span>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-3 fs-4">
                    <i class="bi bi-diagram-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Visualisasi Statistik Kondisi Barang -->
    <div class="col-lg-5">
        <div class="card-custom h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Statistik Kondisi Barang</span>
                <span class="badge bg-light text-dark border">Kondisi Fisik</span>
            </div>
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                <?php if ($totalJenisBarang == 0) : ?>
                    <p class="text-muted small mb-0">Belum ada data barang untuk ditampilkan pada grafik.</p>
                <?php else : ?>
                    <div style="max-width: 250px; width: 100%;">
                        <canvas id="kondisiChart"></canvas>
                    </div>
                    <div class="row w-100 mt-4 text-center g-2">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <span class="d-block text-success fw-bold fs-5"><?= $kondisiBaik ?></span>
                                <span class="small text-muted">Baik</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <span class="d-block text-warning fw-bold fs-5"><?= $kondisiRusakRingan ?></span>
                                <span class="small text-muted">Rusak Ringan</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <span class="d-block text-danger fw-bold fs-5"><?= $kondisiRusakBerat ?></span>
                                <span class="small text-muted">Rusak Berat</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis -->
    <div class="col-lg-7">
        <div class="card-custom h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="fw-bold"><i class="bi bi-exclamation-circle-fill me-2 text-warning"></i>Perhatian: Stok Kritis / Menipis</span>
                <a href="<?= base_url('barang') ?>" class="small text-decoration-none">Kelola Barang</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Lokasi</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-center">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($barangStokMenipis)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        <i class="bi bi-check-circle fs-4 d-block mb-1 text-success"></i>
                                        Seluruh stok barang dalam kondisi aman (di atas 2 unit).
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($barangStokMenipis as $item) : ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></div>
                                            <div class="small text-muted font-monospace"><?= esc($item['kode_barang']) ?></div>
                                        </td>
                                        <td class="small text-secondary"><?= esc($item['nama_lokasi']) ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= ($item['jumlah'] == 0) ? 'bg-danger' : 'bg-warning text-dark' ?> px-2 py-1">
                                                <?= (int)$item['jumlah'] ?> Unit
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($item['kondisi'] === 'Baik') : ?>
                                                <span class="badge-kondisi-baik small">Baik</span>
                                            <?php elseif ($item['kondisi'] === 'Rusak Ringan') : ?>
                                                <span class="badge-kondisi-rusak-ringan small">Rusak Ringan</span>
                                            <?php else : ?>
                                                <span class="badge-kondisi-rusak-berat small">Rusak Berat</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Transaksi Peminjaman Terkini -->
<div class="card-custom">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Aktivitas Peminjaman Terkini</span>
        <a href="<?= base_url('peminjaman') ?>" class="small text-decoration-none">Lihat Semua Transaksi</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Nama Peminjam</th>
                        <th>Barang</th>
                        <th style="width: 100px;" class="text-center">Jumlah</th>
                        <th style="width: 130px;" class="text-center">Tgl Pinjam</th>
                        <th style="width: 130px;" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($peminjamanTerbaru)) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                Belum ada riwayat transaksi peminjaman.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($peminjamanTerbaru as $p) : ?>
                            <tr>
                                <td class="text-center fw-medium"><?= $no++ ?></td>
                                <td class="fw-semibold text-dark"><?= esc($p['nama_peminjam']) ?></td>
                                <td>
                                    <div><?= esc($p['nama_barang']) ?></div>
                                    <div class="small text-muted font-monospace"><?= esc($p['kode_barang']) ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <?= (int)$p['jumlah_pinjam'] ?> Unit
                                    </span>
                                </td>
                                <td class="text-center small text-muted">
                                    <?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['status'] === 'Dipinjam') : ?>
                                        <span class="badge-status-dipinjam small">Dipinjam</span>
                                    <?php else : ?>
                                        <span class="badge-status-kembali small">Kembali</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    <?php if ($totalJenisBarang > 0) : ?>
    const ctx = document.getElementById('kondisiChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Baik', 'Rusak Ringan', 'Rusak Berat'],
            datasets: [{
                data: [<?= $kondisiBaik ?>, <?= $kondisiRusakRingan ?>, <?= $kondisiRusakBerat ?>],
                backgroundColor: ['#16a34a', '#ca8a04', '#dc2626'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: { size: 12 }
                    }
                }
            },
            cutout: '70%'
        }
    });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>
