<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php $isAdmin = session('role') === 'admin'; ?>
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h5 fw-bold mb-1 text-dark">Daftar Kategori Barang</h2>
        <p class="text-muted small mb-0">Kelola kelompok inventaris perangkat laboratorium komputer.</p>
    </div>
    <?php if ($isAdmin) : ?>
    <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Kategori</span>
    </button>
    <?php endif; ?>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 70px;" class="text-center">No</th>
                        <th>Nama Kategori</th>
                        <th style="width: 200px;" class="text-center">Jumlah Barang</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategori)) : ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-tags"></i></div>
                                <h6>Belum ada kategori</h6>
                                <p>Tambahkan kategori pertama untuk mengelompokkan perangkat laboratorium.</p>
                            </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($kategori as $item) : ?>
                            <tr>
                                <td class="text-center fw-medium"><?= $no++ ?></td>
                                <td class="fw-semibold text-dark"><?= esc($item['nama_kategori']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <i class="bi bi-box-seam me-1"></i><?= (int)$item['total_unit'] ?> Unit
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($isAdmin) : ?>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-edit" 
                                                data-id="<?= $item['id_kategori'] ?>" 
                                                data-nama="<?= esc($item['nama_kategori']) ?>" 
                                                title="Ubah Kategori">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-delete" 
                                                data-id="<?= $item['id_kategori'] ?>" 
                                                data-nama="<?= esc($item['nama_kategori']) ?>" 
                                                data-total="<?= (int)$item['total_unit'] ?>"
                                                title="Hapus Kategori">
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
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6" id="modalTambahLabel">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= base_url('kategori/simpan') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label for="nama_kategori_tambah" class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kategori_tambah" name="nama_kategori" placeholder="Contoh: Hardware, Perangkat Jaringan" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6" id="modalEditLabel">Ubah Data Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label for="nama_kategori_edit" class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kategori_edit" name="nama_kategori" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Kategori -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6 text-danger" id="modalHapusLabel">Konfirmasi Hapus Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2">Apakah Anda yakin ingin menghapus kategori <strong id="hapus_nama_kategori"></strong>?</p>
                <div id="hapus_warning" class="alert alert-warning py-2 px-3 small d-none">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Kategori ini masih memiliki barang inventaris terkait dan tidak dapat dihapus.
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusKategori" method="post" class="d-inline">
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
            form.action = '<?= base_url('kategori/update') ?>/' + id;
            document.getElementById('nama_kategori_edit').value = nama;
            const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
            modal.show();
        });
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const total = parseInt(this.getAttribute('data-total'), 10);
            
            document.getElementById('hapus_nama_kategori').textContent = nama;
            const btnHapus = document.getElementById('btnKonfirmasiHapus');
            const warning = document.getElementById('hapus_warning');
            
            document.getElementById('formHapusKategori').action = '<?= base_url('kategori/hapus') ?>/' + id;

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
