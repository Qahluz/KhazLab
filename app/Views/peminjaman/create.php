<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-1">
            <li class="breadcrumb-item"><a href="<?= base_url('peminjaman') ?>" class="text-decoration-none">Peminjaman</a></li>
            <li class="breadcrumb-item active" aria-current="page">Catat Peminjaman</li>
        </ol>
    </nav>
    <h1 class="h4 fw-bold mb-1">Formulir Peminjaman Barang</h1>
    <p class="text-muted small mb-0">Catat transaksi peminjaman perangkat laboratorium untuk mahasiswa atau dosen</p>
</div>

<div class="card-custom">
    <div class="card-body p-4">
        <?php if (empty($barang)) : ?>
            <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Tidak Ada Stok Barang yang Tersedia</h6>
                    <p class="small mb-0">Seluruh barang di laboratorium saat ini sedang dipinjam atau stoknya 0. Silakan periksa kembali data barang atau tunggu pengembalian.</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="<?= base_url('peminjaman') ?>" class="btn btn-sm btn-outline-secondary">Kembali ke Daftar Peminjaman</a>
            </div>
        <?php else : ?>
            <form action="<?= base_url('peminjaman/simpan') ?>" method="post">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <!-- Pilih Barang -->
                    <div class="col-md-6">
                        <label for="id_barang" class="form-label small fw-semibold">Pilih Barang yang Dipinjam <span class="text-danger">*</span></label>
                        <select name="id_barang" id="id_barang" class="form-select form-select-sm <?= (isset($validation) && $validation->hasError('id_barang')) ? 'is-invalid' : '' ?>" required>
                            <option value="" data-stok="0">-- Pilih Barang Tersedia --</option>
                            <?php foreach ($barang as $b) : ?>
                                <option value="<?= $b['id_barang'] ?>" 
                                        data-stok="<?= (int)$b['jumlah'] ?>"
                                        data-kode="<?= esc($b['kode_barang']) ?>"
                                        <?= (old('id_barang') == $b['id_barang']) ? 'selected' : '' ?>>
                                    <?= esc($b['nama_barang']) ?> (<?= esc($b['kode_barang']) ?>) - Sisa Stok: <?= (int)$b['jumlah'] ?> Unit
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="stokHelp" class="form-text small text-primary fw-medium d-none">
                            <i class="bi bi-info-circle me-1"></i> Stok yang dapat dipinjam: <span id="stokDisplay">0</span> unit.
                        </div>
                        <?php if (isset($validation) && $validation->hasError('id_barang')) : ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('id_barang')) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Nama Peminjam -->
                    <div class="col-md-6">
                        <label for="nama_peminjam" class="form-label small fw-semibold">Nama Peminjam <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-sm <?= (isset($validation) && $validation->hasError('nama_peminjam')) ? 'is-invalid' : '' ?>" 
                               id="nama_peminjam" 
                               name="nama_peminjam" 
                               value="<?= old('nama_peminjam') ?>" 
                               placeholder="Nama Mahasiswa / Dosen / Laboran" 
                               required>
                        <?php if (isset($validation) && $validation->hasError('nama_peminjam')) : ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('nama_peminjam')) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Jumlah Pinjam -->
                    <div class="col-md-6">
                        <label for="jumlah_pinjam" class="form-label small fw-semibold">Jumlah yang Dipinjam <span class="text-danger">*</span></label>
                        <input type="number" 
                               class="form-control form-control-sm <?= (isset($validation) && $validation->hasError('jumlah_pinjam')) ? 'is-invalid' : '' ?>" 
                               id="jumlah_pinjam" 
                               name="jumlah_pinjam" 
                               value="<?= old('jumlah_pinjam', 1) ?>" 
                               min="1" 
                               required>
                        <div class="form-text small text-muted">Jumlah tidak boleh melebihi sisa stok yang ada di laboratorium.</div>
                        <?php if (isset($validation) && $validation->hasError('jumlah_pinjam')) : ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('jumlah_pinjam')) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Tanggal Pinjam -->
                    <div class="col-md-6">
                        <label for="tanggal_pinjam" class="form-label small fw-semibold">Tanggal Peminjaman <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control form-control-sm <?= (isset($validation) && $validation->hasError('tanggal_pinjam')) ? 'is-invalid' : '' ?>" 
                               id="tanggal_pinjam" 
                               name="tanggal_pinjam" 
                               value="<?= old('tanggal_pinjam', date('Y-m-d')) ?>" 
                               required>
                        <?php if (isset($validation) && $validation->hasError('tanggal_pinjam')) : ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('tanggal_pinjam')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a href="<?= base_url('peminjaman') ?>" class="btn btn-sm btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Transaksi Peminjaman
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const selectBarang = document.getElementById('id_barang');
    const inputJumlah = document.getElementById('jumlah_pinjam');
    const stokHelp = document.getElementById('stokHelp');
    const stokDisplay = document.getElementById('stokDisplay');

    function updateStokInfo() {
        if (!selectBarang) return;
        const selected = selectBarang.options[selectBarang.selectedIndex];
        const stok = parseInt(selected.getAttribute('data-stok') || 0, 10);
        
        if (stok > 0) {
            stokHelp.classList.remove('d-none');
            stokDisplay.textContent = stok;
            inputJumlah.max = stok;
        } else {
            stokHelp.classList.add('d-none');
            inputJumlah.removeAttribute('max');
        }
    }

    if (selectBarang) {
        selectBarang.addEventListener('change', updateStokInfo);
        updateStokInfo();
    }
</script>
<?= $this->endSection() ?>
