<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="h5 fw-bold mb-1 text-dark">Daftar Lokasi Penyimpanan</h2>
        <p class="text-muted small mb-0">Kelola ruangan dan tempat penempatan perangkat laboratorium komputer.</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Lokasi</span>
    </button>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">No</th>
                        <th>Nama Lokasi / Ruangan</th>
                        <th style="width: 200px;" class="text-center">Jumlah Barang Disimpan</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lokasi)) : ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-geo-alt"></i></div>
                                <h6>Belum ada lokasi</h6>
                                <p>Tambahkan ruangan laboratorium pertama sebagai tempat penyimpanan aset.</p>
                            </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($lokasi as $item) : ?>
                            <tr>
                                <td class="text-center fw-medium"><?= $no++ ?></td>
                                <td class="fw-semibold text-dark">
                                    <i class="bi bi-geo-alt text-primary me-1"></i>
                                    <?= esc($item['nama_lokasi']) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-box-seam me-1"></i><?= (int)$item['total_barang'] ?> Unit
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-edit" 
                                                data-id="<?= $item['id_lokasi'] ?>" 
                                                data-nama="<?= esc($item['nama_lokasi']) ?>" 
                                                title="Ubah Lokasi">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete" 
                                                data-id="<?= $item['id_lokasi'] ?>" 
                                                data-nama="<?= esc($item['nama_lokasi']) ?>" 
                                                data-total="<?= (int)$item['total_barang'] ?>"
                                                title="Hapus Lokasi">
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

<!-- Modal Tambah Lokasi -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6" id="modalTambahLabel">Tambah Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= base_url('lokasi/simpan') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label for="nama_lokasi_tambah" class="form-label small fw-semibold">Nama Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_lokasi_tambah" name="nama_lokasi" placeholder="Contoh: Lab Komputer 1, Gudang Perangkat" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Lokasi -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6" id="modalEditLabel">Ubah Data Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label for="nama_lokasi_edit" class="form-label small fw-semibold">Nama Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_lokasi_edit" name="nama_lokasi" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Perbarui Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Lokasi -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6 text-danger" id="modalHapusLabel">Konfirmasi Hapus Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2">Apakah Anda yakin ingin menghapus lokasi <strong id="hapus_nama_lokasi"></strong>?</p>
                <div id="hapus_warning" class="alert alert-warning py-2 px-3 small d-none">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Lokasi ini masih memiliki barang inventaris yang disimpan dan tidak dapat dihapus.
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusLokasi" method="post" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" id="btnKonfirmasiHapus" class="btn btn-sm btn-danger">Hapus Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const form = document.getElementById('formEdit');
            form.action = '<?= base_url('lokasi/update') ?>/' + id;
            document.getElementById('nama_lokasi_edit').value = nama;
            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
            modal.show();
        });
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const total = parseInt(this.getAttribute('data-total'), 10);
            
            document.getElementById('hapus_nama_lokasi').textContent = nama;
            const btnHapus = document.getElementById('btnKonfirmasiHapus');
            const warning = document.getElementById('hapus_warning');
            
            document.getElementById('formHapusLokasi').action = '<?= base_url('lokasi/hapus') ?>/' + id;

            if (total > 0) {
                warning.classList.remove('d-none');
                btnHapus.disabled = true;
            } else {
                warning.classList.add('d-none');
                btnHapus.disabled = false;
            }
            
            const modal = new bootstrap.Modal(document.getElementById('modalHapus'));
            modal.show();
        });
    });
</script>
<?= $this->endSection() ?>
