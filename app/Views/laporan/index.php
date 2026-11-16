<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
    <div>
        <h2 class="h5 fw-bold mb-1 text-dark">Laporan Inventaris Laboratorium</h2>
        <p class="text-muted small mb-0">Rekapitulasi siap cetak dan siap ekspor untuk kebutuhan pelaporan akademik.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 no-print">
        <a href="<?= base_url('laporan/export-csv') ?>?<?= http_build_query(['keyword' => $keyword, 'kategori' => $selectedKat, 'lokasi' => $selectedLok, 'kondisi' => $selectedKon]) ?>"
           class="btn btn-sm btn-outline-success"><i class="bi bi-filetype-csv me-1"></i>Export CSV (Excel)</a>
        <a href="<?= base_url('laporan/cetak-peminjaman') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-journal-text me-1"></i>Laporan Peminjaman</a>
        <button type="button" class="btn btn-sm btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Cetak Laporan</button>
    </div>
</div>

<div class="card-custom no-print">
    <div class="card-header"><span><i class="bi bi-funnel me-1 text-primary"></i>Filter Laporan</span></div>
    <div class="p-3">
        <form method="get" action="<?= base_url('laporan') ?>" class="filter-bar">
            <div>
                <label class="form-label" for="keyword">Kode / Nama Barang</label>
                <input type="search" class="form-control form-control-sm" id="keyword" name="keyword" value="<?= esc($keyword) ?>">
            </div>
            <div>
                <label class="form-label" for="kategori">Kategori</label>
                <select class="form-select form-select-sm" id="kategori" name="kategori">
                    <option value="">Semua kategori</option>
                    <?php foreach ($kategori as $kat) : ?>
                        <option value="<?= $kat['id_kategori'] ?>" <?= (string) $selectedKat === (string) $kat['id_kategori'] ? 'selected' : '' ?>><?= esc($kat['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="lokasi">Lokasi</label>
                <select class="form-select form-select-sm" id="lokasi" name="lokasi">
                    <option value="">Semua lokasi</option>
                    <?php foreach ($lokasi as $lok) : ?>
                        <option value="<?= $lok['id_lokasi'] ?>" <?= (string) $selectedLok === (string) $lok['id_lokasi'] ? 'selected' : '' ?>><?= esc($lok['nama_lokasi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label" for="kondisi">Kondisi</label>
                <select class="form-select form-select-sm" id="kondisi" name="kondisi">
                    <option value="">Semua kondisi</option>
                    <?php foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kon) : ?>
                        <option value="<?= $kon ?>" <?= $selectedKon === $kon ? 'selected' : '' ?>><?= $kon ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Terapkan</button></div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="p-4">
        <div class="text-center border-bottom pb-3 mb-3">
            <h3 class="h5 fw-bold mb-1 text-dark">LABORATORIUM KOMPUTER - PROGRAM STUDI SISTEM INFORMASI</h3>
            <div class="small text-muted">Sistem Informasi Inventaris Laboratorium (SIM-LAB)</div>
            <div class="small text-muted">Jl. Kampus Praktikum No. 1, Jakarta &middot; labkom@kampus.ac.id</div>
        </div>

        <div class="d-flex justify-content-between flex-wrap small mb-3">
            <div><strong>Jenis laporan:</strong> Rekapitulasi Inventaris Barang</div>
            <div><strong>Tanggal cetak:</strong> <?= esc($tanggalCetak) ?></div>
        </div>

        <div class="row g-2 mb-3 small">
            <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted">Jenis Barang</div><div class="fw-bold"><?= $totalJenis ?></div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted">Total Unit Stok</div><div class="fw-bold"><?= $totalUnit ?></div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted">Kondisi Baik / Ringan</div><div class="fw-bold"><?= $countBaik ?> / <?= $countRusakRingan ?></div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><div class="text-muted">Rusak Berat</div><div class="fw-bold text-danger"><?= $countRusakBerat ?></div></div></div>
        </div>

        <table class="table table-custom table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width:42px">No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Kondisi</th>
                    <th class="text-center">Tgl Masuk</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($barang)) : ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data barang pada filter ini.</td></tr>
            <?php else : ?>
                <?php $no = 1; foreach ($barang as $item) : ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="mono"><?= esc($item['kode_barang']) ?></td>
                        <td><?= esc($item['nama_barang']) ?></td>
                        <td><?= esc($item['nama_kategori']) ?></td>
                        <td><?= esc($item['nama_lokasi']) ?></td>
                        <td class="text-center"><?= (int) $item['jumlah'] ?></td>
                        <td class="text-center"><?= esc($item['kondisi']) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($item['tanggal_masuk'])) ?></td>
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
