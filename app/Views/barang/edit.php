<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-1">
            <li class="breadcrumb-item"><a href="<?= base_url('barang') ?>" class="text-decoration-none">Data Barang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ubah Barang</li>
        </ol>
    </nav>
    <h1 class="h4 fw-bold mb-1">Ubah Data Barang Inventaris</h1>
    <p class="text-muted small mb-0">Perbarui rincian, kondisi, atau lokasi penyimpanan aset laboratorium</p>
</div>

<div class="card-custom">
    <div class="card-body p-4">
        <form action="<?= base_url('barang/update/' . $barang['id_barang']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <!-- Kode Barang -->
                <div class="col-md-6">
                    <label for="kode_barang" class="form-label small fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control form-control-sm <?= (isset($validation) && $validation->hasError('kode_barang')) ? 'is-invalid' : '' ?>" 
                           id="kode_barang" 
                           name="kode_barang" 
                           value="<?= old('kode_barang', $barang['kode_barang']) ?>" 
                           required>
                    <?php if (isset($validation) && $validation->hasError('kode_barang')) : ?>
                        <div class="invalid-feedback"><?= esc($validation->getError('kode_barang')) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Nama Barang -->
                <div class="col-md-6">
                    <label for="nama_barang" class="form-label small fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control form-control-sm <?= (isset($validation) && $validation->hasError('nama_barang')) ? 'is-invalid' : '' ?>" 
                           id="nama_barang" 
                           name="nama_barang" 
                           value="<?= old('nama_barang', $barang['nama_barang']) ?>" 
                           required>
                    <?php if (isset($validation) && $validation->hasError('nama_barang')) : ?>
                        <div class="invalid-feedback"><?= esc($validation->getError('nama_barang')) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Kategori -->
                <div class="col-md-6">
                    <label for="id_kategori" class="form-label small fw-semibold">Kategori Barang <span class="text-danger">*</span></label>
                    <select name="id_kategori" id="id_kategori" class="form-select form-select-sm <?= (isset($validation) && $validation->hasError('id_kategori')) ? 'is-invalid' : '' ?>" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategori as $kat) : ?>
                            <option value="<?= $kat['id_kategori'] ?>" <?= (old('id_kategori', $barang['id_kategori']) == $kat['id_kategori']) ? 'selected' : '' ?>>
                                <?= esc($kat['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($validation) && $validation->hasError('id_kategori')) : ?>
                        <div class="invalid-feedback"><?= esc($validation->getError('id_kategori')) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Lokasi -->
                <div class="col-md-6">
                    <label for="id_lokasi" class="form-label small fw-semibold">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                    <select name="id_lokasi" id="id_lokasi" class="form-select form-select-sm <?= (isset($validation) && $validation->hasError('id_lokasi')) ? 'is-invalid' : '' ?>" required>
                        <option value="">-- Pilih Lokasi Ruangan --</option>
                        <?php foreach ($lokasi as $lok) : ?>
                            <option value="<?= $lok['id_lokasi'] ?>" <?= (old('id_lokasi', $barang['id_lokasi']) == $lok['id_lokasi']) ? 'selected' : '' ?>>
                                <?= esc($lok['nama_lokasi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($validation) && $validation->hasError('id_lokasi')) : ?>
                        <div class="invalid-feedback"><?= esc($validation->getError('id_lokasi')) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Jumlah Stok -->
                <div class="col-md-4">
                    <label for="jumlah" class="form-label small fw-semibold">Jumlah Stok (Unit) <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control form-control-sm <?= (isset($validation) && $validation->hasError('jumlah')) ? 'is-invalid' : '' ?>" 
                           id="jumlah" 
                           name="jumlah" 
                           value="<?= old('jumlah', $barang['jumlah']) ?>" 
                           min="0" 
                           required>
                    <?php if (isset($validation) && $validation->hasError('jumlah')) : ?>
                        <div class="invalid-feedback"><?= esc($validation->getError('jumlah')) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Kondisi Barang -->
                <div class="col-md-4">
                    <label for="kondisi" class="form-label small fw-semibold">Kondisi Barang <span class="text-danger">*</span></label>
                    <select name="kondisi" id="kondisi" class="form-select form-select-sm <?= (isset($validation) && $validation->hasError('kondisi')) ? 'is-invalid' : '' ?>" required>
                        <option value="Baik" <?= (old('kondisi', $barang['kondisi']) === 'Baik') ? 'selected' : '' ?>>Baik</option>
                        <option value="Rusak Ringan" <?= (old('kondisi', $barang['kondisi']) === 'Rusak Ringan') ? 'selected' : '' ?>>Rusak Ringan</option>
                        <option value="Rusak Berat" <?= (old('kondisi', $barang['kondisi']) === 'Rusak Berat') ? 'selected' : '' ?>>Rusak Berat</option>
                    </select>
                    <?php if (isset($validation) && $validation->hasError('kondisi')) : ?>
                        <div class="invalid-feedback"><?= esc($validation->getError('kondisi')) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Tanggal Masuk -->
                <div class="col-md-4">
                    <label for="tanggal_masuk" class="form-label small fw-semibold">Tanggal Masuk Inventaris <span class="text-danger">*</span></label>
                    <input type="date" 
                           class="form-control form-control-sm <?= (isset($validation) && $validation->hasError('tanggal_masuk')) ? 'is-invalid' : '' ?>" 
                           id="tanggal_masuk" 
                           name="tanggal_masuk" 
                           value="<?= old('tanggal_masuk', $barang['tanggal_masuk']) ?>" 
                           required>
                    <?php if (isset($validation) && $validation->hasError('tanggal_masuk')) : ?>
                        <div class="invalid-feedback"><?= esc($validation->getError('tanggal_masuk')) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex align-items-center justify-content-end gap-2">
                <a href="<?= base_url('barang') ?>" class="btn btn-sm btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-save me-1"></i> Perbarui Data Barang
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
