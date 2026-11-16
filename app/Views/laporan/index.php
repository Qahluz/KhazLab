<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Action & Filter Bar (Disembunyikan saat mode cetak) -->
<div class="card-custom mb-3 no-print">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h5 fw-bold mb-1">Laporan Inventaris Barang</h1>
                <p class="text-muted small mb-0">Cetak dan filter rekapitulasi inventaris laboratorium komputer</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1" onclick="window.print()">
                    <i class="bi bi-printer"></i>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <hr class="my-3">

        <form action="<?= base_url('laporan') ?>" method="get" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" 
                       class="form-control form-control-sm" 
                       name="keyword" 
                       value="<?= esc($keyword ?? '') ?>" 
                       placeholder="Cari kode atau nama barang...">
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategori as $kat) : ?>
                        <option value="<?= $kat['id_kategori'] ?>" <?= (isset($selectedKat) && $selectedKat == $kat['id_kategori']) ? 'selected' : '' ?>>
                            <?= esc($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="kondisi" class="form-select form-select-sm">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" <?= (isset($selectedKon) && $selectedKon === 'Baik') ? 'selected' : '' ?>>Baik</option>
                    <option value="Rusak Ringan" <?= (isset($selectedKon) && $selectedKon === 'Rusak Ringan') ? 'selected' : '' ?>>Rusak Ringan</option>
                    <option value="Rusak Berat" <?= (isset($selectedKon) && $selectedKon === 'Rusak Berat') ? 'selected' : '' ?>>Rusak Berat</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-secondary flex-grow-1">Terapkan</button>
                <a href="<?= base_url('laporan') ?>" class="btn btn-sm btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lembar Dokumen Laporan Resmi -->
<div class="card-custom">
    <div class="card-body p-4 p-md-5">
        <!-- Kop Laporan Resmi -->
        <div class="text-center pb-3 mb-4 border-bottom border-dark border-2">
            <h2 class="h5 fw-bold text-uppercase mb-1 tracking-wide">Laboratorium Komputer Program Studi Sistem Informasi</h2>
            <h3 class="h6 fw-bold text-uppercase text-secondary mb-1">Fakultas Ilmu Komputer &bull; UPN "Veteran" Jakarta</h3>
            <p class="small text-muted mb-0">Laporan Rekapitulasi Data Inventaris Aset & Perangkat Laboratorium</p>
        </div>

        <!-- Ringkasan Statistik Laporan -->
        <div class="row g-2 mb-4">
            <div class="col-6 col-md-3">
                <div class="p-2 border rounded text-center bg-light">
                    <div class="small text-muted">Total Jenis Barang</div>
                    <div class="fw-bold fs-6 text-dark"><?= number_format($totalJenis) ?> Item</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2 border rounded text-center bg-light">
                    <div class="small text-muted">Total Unit Tersedia</div>
                    <div class="fw-bold fs-6 text-primary"><?= number_format($totalUnit) ?> Unit</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2 border rounded text-center bg-light">
                    <div class="small text-muted">Kondisi Baik</div>
                    <div class="fw-bold fs-6 text-success"><?= $countBaik ?> Item</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2 border rounded text-center bg-light">
                    <div class="small text-muted">Perlu Perbaikan</div>
                    <div class="fw-bold fs-6 text-danger"><?= ($countRusakRingan + $countRusakBerat) ?> Item</div>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Inventaris -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-sm align-middle" style="font-size: 0.875rem;">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="width: 130px;">Kode Barang</th>
                        <th>Nama Barang / Perangkat</th>
                        <th style="width: 140px;">Kategori</th>
                        <th style="width: 140px;">Lokasi Ruangan</th>
                        <th style="width: 80px;">Stok</th>
                        <th style="width: 120px;">Kondisi</th>
                        <th style="width: 100px;">Tgl Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barang)) : ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Tidak ada data inventaris yang dapat ditampilkan.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($barang as $item) : ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="font-monospace text-center"><?= esc($item['kode_barang']) ?></td>
                                <td class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></td>
                                <td><?= esc($item['nama_kategori']) ?></td>
                                <td><?= esc($item['nama_lokasi']) ?></td>
                                <td class="text-center fw-bold"><?= (int)$item['jumlah'] ?></td>
                                <td class="text-center">
                                    <?php if ($item['kondisi'] === 'Baik') : ?>
                                        <span class="text-success fw-medium">Baik</span>
                                    <?php elseif ($item['kondisi'] === 'Rusak Ringan') : ?>
                                        <span class="text-warning fw-medium">Rusak Ringan</span>
                                    <?php else : ?>
                                        <span class="text-danger fw-medium">Rusak Berat</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($item['tanggal_masuk'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Lembar Pengesahan / Tanda Tangan -->
        <div class="row pt-4 mt-4 text-center">
            <div class="col-6">
                <p class="small text-muted mb-5">Mengetahui,<br><strong>Kepala Laboratorium Komputer SI</strong></p>
                <div class="mt-4">
                    <div class="fw-bold text-dark text-decoration-underline">I Wayan Widi Pradnyana, M.TI</div>
                    <div class="small text-muted">NIP. 198106252025211045</div>
                </div>
            </div>
            <div class="col-6">
                <p class="small text-muted mb-5">Jakarta, <?= esc($tanggalCetak) ?><br><strong>Petugas Pengelola Inventaris</strong></p>
                <div class="mt-4">
                    <div class="fw-bold text-dark text-decoration-underline">( <?= esc(session()->get('username') ?? 'Laboran Komputer') ?> )</div>
                    <div class="small text-muted">Administrator Laboratorium</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
