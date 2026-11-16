<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-1">
            <li class="breadcrumb-item"><a href="<?= base_url('peminjaman') ?>" class="text-decoration-none">Peminjaman</a></li>
            <li class="breadcrumb-item active" aria-current="page">Catat Baru</li>
        </ol>
    </nav>
    <h2 class="h5 fw-bold mb-1 text-dark">Catat Peminjaman Barang Baru</h2>
    <p class="text-muted small mb-0">Stok berkurang otomatis dan transaksi dicatat dalam satu transaksi database yang atomik.</p>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header"><span><i class="bi bi-pencil-square me-1 text-primary"></i>Formulir Peminjaman</span></div>
            <div class="p-3">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger py-2 px-3 small"><i class="bi bi-exclamation-triangle-fill me-1"></i><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <form action="<?= base_url('peminjaman/simpan') ?>" method="post" id="formPinjam">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="id_barang" class="form-label small fw-semibold">Perangkat yang Dipinjam <span class="text-danger">*</span></label>
                        <select name="id_barang" id="id_barang" class="form-select form-select-sm <?= $validation->hasError('id_barang') ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Pilih perangkat laboratorium --</option>
                            <?php foreach ($barang as $item) : ?>
                                <option value="<?= $item['id_barang'] ?>"
                                        data-stok="<?= (int) $item['jumlah'] ?>"
                                        data-nama="<?= esc($item['nama_barang']) ?>"
                                        data-lokasi="<?= esc($item['nama_lokasi']) ?>"
                                        <?= old('id_barang') == $item['id_barang'] ? 'selected' : '' ?>>
                                    <?= esc($item['kode_barang']) ?> - <?= esc($item['nama_barang']) ?> (stok: <?= (int) $item['jumlah'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($validation->hasError('id_barang')) : ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('id_barang')) ?></div>
                        <?php endif; ?>
                        <?php if (empty($barang)) : ?>
                            <div class="form-text text-danger">Tidak ada perangkat dengan stok tersedia. Tambah stok di menu Data Barang terlebih dahulu.</div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="nama_peminjam" class="form-label small fw-semibold">Nama Peminjam <span class="text-danger">*</span></label>
                            <input type="text" name="nama_peminjam" id="nama_peminjam"
                                   class="form-control form-control-sm <?= $validation->hasError('nama_peminjam') ? 'is-invalid' : '' ?>"
                                   value="<?= old('nama_peminjam') ?>" placeholder="cth: Rio Wirawan (Dosen) / Praktikum Kelas 2A" required>
                            <?php if ($validation->hasError('nama_peminjam')) : ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('nama_peminjam')) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="jumlah_pinjam" class="form-label small fw-semibold">Jumlah Pinjam <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="jumlah_pinjam" id="jumlah_pinjam"
                                       class="form-control <?= $validation->hasError('jumlah_pinjam') ? 'is-invalid' : '' ?>"
                                       value="<?= old('jumlah_pinjam', 1) ?>" min="1" required>
                                <span class="input-group-text">unit</span>
                                <?php if ($validation->hasError('jumlah_pinjam')) : ?>
                                    <div class="invalid-feedback"><?= esc($validation->getError('jumlah_pinjam')) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_pinjam" class="form-label small fw-semibold">Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                                   class="form-control form-control-sm <?= $validation->hasError('tanggal_pinjam') ? 'is-invalid' : '' ?>"
                                   value="<?= old('tanggal_pinjam', date('Y-m-d')) ?>" required>
                            <?php if ($validation->hasError('tanggal_pinjam')) : ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('tanggal_pinjam')) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_jatuh_tempo" class="form-label small fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo"
                                   class="form-control form-control-sm <?= $validation->hasError('tanggal_jatuh_tempo') ? 'is-invalid' : '' ?>"
                                   value="<?= old('tanggal_jatuh_tempo', $jatuhTempo) ?>" required>
                            <?php if ($validation->hasError('tanggal_jatuh_tempo')) : ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('tanggal_jatuh_tempo')) ?></div>
                            <?php endif; ?>
                            <div class="form-text small">Default 7 hari setelah tanggal pinjam. Status "Terlambat" muncul otomatis bila melewati tanggal ini.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('peminjaman') ?>" class="btn btn-sm btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save me-1"></i>Simpan Peminjaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header"><span><i class="bi bi-info-circle me-1 text-primary"></i>Ringkasan Pilihan</span></div>
            <div class="p-3 small" id="panelRingkasan">
                <p class="text-muted mb-0">Pilih perangkat untuk melihat sisa stok setelah peminjaman.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const pilih = document.getElementById('id_barang');
    const jumlah = document.getElementById('jumlah_pinjam');
    const tglPinjam = document.getElementById('tanggal_pinjam');
    const tglTempo = document.getElementById('tanggal_jatuh_tempo');
    const panel = document.getElementById('panelRingkasan');

    const render = () => {
        const opt = pilih.selectedOptions[0];
        const stok = Number(opt?.dataset.stok || 0);
        const diminta = Number(jumlah.value || 0);
        if (!opt?.value) {
            panel.innerHTML = '<p class="text-muted mb-0">Pilih perangkat untuk melihat sisa stok setelah peminjaman.</p>';
            return;
        }
        const sisa = stok - diminta;
        panel.innerHTML = `
            <div class="mb-1"><span class="text-muted">Perangkat:</span> <strong>${opt.dataset.nama}</strong></div>
            <div class="mb-1"><span class="text-muted">Lokasi:</span> ${opt.dataset.lokasi}</div>
            <div class="mb-1"><span class="text-muted">Stok saat ini:</span> ${stok} unit</div>
            <div class="mb-1"><span class="text-muted">Diminta:</span> ${diminta} unit</div>
            <div class="pt-2 border-top ${sisa < 0 ? 'text-danger fw-semibold' : 'text-success fw-semibold'}">
                Sisa stok setelah dicatat: ${sisa} unit ${sisa < 0 ? '(melebihi stok!)' : ''}
            </div>`;
    };

    pilih?.addEventListener('change', render);
    jumlah?.addEventListener('input', render);

    tglPinjam?.addEventListener('change', () => {
        const d = new Date(tglPinjam.value);
        if (isNaN(d)) return;
        d.setDate(d.getDate() + 7);
        tglTempo.value = d.toISOString().slice(0, 10);
    });

    render();

    document.getElementById('formPinjam')?.addEventListener('submit', function (e) {
        const opt = pilih.selectedOptions[0];
        const stok = Number(opt?.dataset.stok || 0);
        const diminta = Number(jumlah.value || 0);
        if (diminta > stok) {
            e.preventDefault();
            khazlabConfirm(`Jumlah pinjam (${diminta} unit) melebihi stok tersedia (${stok} unit). Sistem akan menolak transaksi ini.`, () => {}, 'Mengerti');
        }
    });
</script>
<?= $this->endSection() ?>
