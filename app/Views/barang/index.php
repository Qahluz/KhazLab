<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
    <div>
        <h1 class="h4 fw-bold mb-1">Manajemen Data Barang</h1>
        <p class="text-muted small mb-0">Kelola katalog inventaris aset dan perangkat laboratorium</p>
    </div>
    <a href="<?= base_url('barang/tambah') ?>" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Barang</span>
    </a>
</div>

<!-- Card Filter & Pencarian -->
<div class="card-custom mb-3">
    <div class="card-body p-3">
        <form action="<?= base_url('barang') ?>" method="get" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" 
                           class="form-control" 
                           name="keyword" 
                           value="<?= esc($keyword ?? '') ?>" 
                           placeholder="Cari berdasarkan kode atau nama barang...">
                </div>
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
            <div class="col-md-2">
                <select name="kondisi" class="form-select form-select-sm">
                    <option value="">Semua Kondisi</option>
                    <option value="Baik" <?= (isset($selectedKon) && $selectedKon === 'Baik') ? 'selected' : '' ?>>Baik</option>
                    <option value="Rusak Ringan" <?= (isset($selectedKon) && $selectedKon === 'Rusak Ringan') ? 'selected' : '' ?>>Rusak Ringan</option>
                    <option value="Rusak Berat" <?= (isset($selectedKon) && $selectedKon === 'Rusak Berat') ? 'selected' : '' ?>>Rusak Berat</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
                <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Data Barang -->
<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th style="width: 140px;">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th style="width: 90px;" class="text-center">Stok</th>
                        <th style="width: 130px;" class="text-center">Kondisi</th>
                        <th style="width: 120px;" class="text-center">Tgl Masuk</th>
                        <th style="width: 120px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barang)) : ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-2 d-block mb-2 text-secondary"></i>
                                <?php if (!empty($keyword) || !empty($selectedKat) || !empty($selectedKon)) : ?>
                                    Tidak ditemukan data barang yang sesuai dengan filter pencarian.
                                    <div class="mt-2">
                                        <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-outline-primary">Reset Filter</a>
                                    </div>
                                <?php else : ?>
                                    Belum ada data barang di laboratorium. Silakan tambah barang baru.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($barang as $item) : ?>
                            <tr>
                                <td class="text-center fw-medium"><?= $no++ ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                        <?= esc($item['kode_barang']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></div>
                                </td>
                                <td>
                                    <span class="text-secondary small">
                                        <i class="bi bi-tag me-1"></i><?= esc($item['nama_kategori']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-secondary small">
                                        <i class="bi bi-geo-alt me-1"></i><?= esc($item['nama_lokasi']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold <?= ($item['jumlah'] <= 0) ? 'text-danger' : 'text-dark' ?>">
                                        <?= (int)$item['jumlah'] ?>
                                    </span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <?php if ($item['kondisi'] === 'Baik') : ?>
                                        <span class="badge-kondisi-baik small">Baik</span>
                                    <?php elseif ($item['kondisi'] === 'Rusak Ringan') : ?>
                                        <span class="badge-kondisi-rusak-ringan small">Rusak Ringan</span>
                                    <?php else : ?>
                                        <span class="badge-kondisi-rusak-berat small">Rusak Berat</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center small text-muted">
                                    <?= date('d/m/Y', strtotime($item['tanggal_masuk'])) ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('barang/edit/' . $item['id_barang']) ?>" class="btn btn-outline-primary" title="Ubah Data">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete-barang" 
                                                data-id="<?= $item['id_barang'] ?>" 
                                                data-nama="<?= esc($item['nama_barang']) ?>" 
                                                data-kode="<?= esc($item['kode_barang']) ?>"
                                                title="Hapus Data">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Barang -->
<div class="modal fade" id="modalHapusBarang" tabindex="-1" aria-labelledby="modalHapusBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6 text-danger" id="modalHapusBarangLabel">Konfirmasi Hapus Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-1">Apakah Anda yakin ingin menghapus data inventaris berikut?</p>
                <div class="p-2 bg-light rounded border my-2 small">
                    <div><strong>Kode:</strong> <span id="delKode"></span></div>
                    <div><strong>Nama:</strong> <span id="delNama"></span></div>
                </div>
                <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Data yang terhapus tidak dapat dipulihkan.</p>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusBarang" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" id="btnConfirmDelete" class="btn btn-sm btn-danger">Hapus Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.querySelectorAll('.btn-delete-barang').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const kode = this.getAttribute('data-kode');
            
            document.getElementById('delNama').textContent = nama;
            document.getElementById('delKode').textContent = kode;
            document.getElementById('formHapusBarang').action = '<?= base_url('barang/hapus') ?>/' + id;
            
            const modal = new bootstrap.Modal(document.getElementById('modalHapusBarang'));
            modal.show();
        });
    });
</script>
<?= $this->endSection() ?>
