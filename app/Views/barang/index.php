<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php $isAdmin = session('role') === 'admin'; ?>
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h5 fw-bold mb-1 text-dark">Manajemen Data Barang</h2>
        <p class="text-muted small mb-0">Kelola seluruh perangkat laboratorium komputer beserta stok, lokasi, dan kondisinya.</p>
    </div>
    <?php if ($isAdmin) : ?>
    <a href="<?= base_url('barang/tambah') ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i><span>Tambah Barang</span>
    </a>
    <?php endif; ?>
</div>

<div class="card-custom">
    <div class="card-header">
        <span><i class="bi bi-funnel me-1 text-primary"></i>Pencarian &amp; Filter Multi-Kriteria</span>
        <?php if ($keyword !== '' || $selectedKat || $selectedLok || $selectedKon) : ?>
            <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Reset</a>
        <?php endif; ?>
    </div>
    <div class="p-3">
        <form method="get" action="<?= base_url('barang') ?>" class="filter-bar">
            <div>
                <label class="form-label" for="keyword">Kode / Nama Barang</label>
                <input type="search" class="form-control form-control-sm" id="keyword" name="keyword" value="<?= esc($keyword) ?>" placeholder="cth: LAB-PC atau monitor">
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
            <div>
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Terapkan</button>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header">
        <span><i class="bi bi-box-seam me-1 text-primary"></i>Daftar Inventaris</span>
        <span class="text-muted small">Menampilkan <?= count($barang) ?> dari <?= $pager ? $pager->getTotal('barang') : count($barang) ?> barang</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width:56px">No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Kondisi</th>
                    <th class="text-center">Tgl Masuk</th>
                    <th class="text-center" style="width:110px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($barang)) : ?>
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                            <h6>Barang tidak ditemukan</h6>
                            <p>Tidak ada data yang cocok dengan filter saat ini.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-outline-secondary">Reset Filter</a>
                                <?php if ($isAdmin) : ?>
                                <a href="<?= base_url('barang/tambah') ?>" class="btn btn-sm btn-primary">Tambah Barang</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php else : ?>
                <?php $no = ($pager ? ($pager->getCurrentPage('barang') - 1) * 10 : 0) + 1; ?>
                <?php foreach ($barang as $item) : ?>
                    <?php
                    $kelas = ['Baik' => 'badge-kondisi-baik', 'Rusak Ringan' => 'badge-kondisi-rusak-ringan', 'Rusak Berat' => 'badge-kondisi-rusak-berat'][$item['kondisi']] ?? 'badge-kondisi-baik';
                    ?>
                    <tr>
                        <td class="text-center text-muted"><?= $no++ ?></td>
                        <td class="mono"><?= esc($item['kode_barang']) ?></td>
                        <td class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></td>
                        <td class="small text-muted"><?= esc($item['nama_kategori']) ?></td>
                        <td class="small text-muted"><?= esc($item['nama_lokasi']) ?></td>
                        <td class="text-center fw-semibold <?= (int) $item['jumlah'] <= 3 ? 'text-danger' : 'text-dark' ?>"><?= (int) $item['jumlah'] ?></td>
                        <td class="text-center"><span class="<?= $kelas ?>"><?= esc($item['kondisi']) ?></span></td>
                        <td class="text-center small text-muted"><?= date('d/m/Y', strtotime($item['tanggal_masuk'])) ?></td>
                        <td class="text-center">
                            <?php if ($isAdmin) : ?>
                            <div class="d-inline-flex gap-1">
                                <a href="<?= base_url('barang/edit/' . $item['id_barang']) ?>" class="btn btn-sm btn-outline-primary" title="Ubah data">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                        data-id="<?= $item['id_barang'] ?>"
                                        data-nama="<?= esc($item['nama_barang']) ?>"
                                        title="Hapus data">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <?php else : ?>
                            <span class="badge bg-light text-secondary border">Baca saja</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (! empty($barang) && $pager && $pager->getPageCount('barang') > 1) : ?>
        <div class="p-3 border-top">
            <?= $pager->only(['keyword', 'kategori', 'lokasi', 'kondisi'])->links('barang', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>

<form id="formHapusBarang" method="post" class="d-none"><?= csrf_field() ?></form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.querySelectorAll('.btn-hapus').forEach(button => {
        button.addEventListener('click', function () {
            const form = document.getElementById('formHapusBarang');
            form.action = '<?= base_url('barang/hapus') ?>/' + this.dataset.id;
            khazlabConfirm(
                'Hapus <strong>' + this.dataset.nama + '</strong> dari inventaris? Data yang masih memiliki riwayat peminjaman akan ditolak sistem demi integritas laporan.',
                () => form.submit(),
                'Ya, Hapus Barang'
            );
        });
    });
</script>
<?= $this->endSection() ?>
