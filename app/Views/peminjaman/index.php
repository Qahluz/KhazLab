<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
    <div>
        <h1 class="h4 fw-bold mb-1">Transaksi Peminjaman & Pengembalian</h1>
        <p class="text-muted small mb-0">Catat dan pantau sirkulasi peminjaman aset laboratorium secara real-time</p>
    </div>
    <a href="<?= base_url('peminjaman/tambah') ?>" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i>
        <span>Catat Peminjaman Baru</span>
    </a>
</div>

<!-- Filter Status Tab -->
<div class="card-custom mb-3">
    <div class="card-body p-2 d-flex flex-wrap align-items-center gap-2">
        <span class="small fw-semibold text-muted ms-2 me-1">Filter Status:</span>
        <a href="<?= base_url('peminjaman') ?>" class="btn btn-sm <?= empty($selectedStatus) ? 'btn-dark' : 'btn-outline-secondary' ?>">
            Semua Transaksi
        </a>
        <a href="<?= base_url('peminjaman?status=Dipinjam') ?>" class="btn btn-sm <?= ($selectedStatus === 'Dipinjam') ? 'btn-primary' : 'btn-outline-primary' ?>">
            <i class="bi bi-clock-history me-1"></i> Sedang Dipinjam
        </a>
        <a href="<?= base_url('peminjaman?status=Kembali') ?>" class="btn btn-sm <?= ($selectedStatus === 'Kembali') ? 'btn-success' : 'btn-outline-success' ?>">
            <i class="bi bi-check2-circle me-1"></i> Sudah Kembali
        </a>
    </div>
</div>

<!-- Tabel Transaksi Peminjaman -->
<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>Nama Peminjam</th>
                        <th>Barang yang Dipinjam</th>
                        <th style="width: 110px;" class="text-center">Jumlah</th>
                        <th style="width: 120px;" class="text-center">Tgl Pinjam</th>
                        <th style="width: 120px;" class="text-center">Tgl Kembali</th>
                        <th style="width: 120px;" class="text-center">Status</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($peminjaman)) : ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-arrow-left-right fs-2 d-block mb-2 text-secondary"></i>
                                Belum ada data transaksi peminjaman tercatat.
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php $no = 1; foreach ($peminjaman as $row) : ?>
                            <tr>
                                <td class="text-center fw-medium"><?= $no++ ?></td>
                                <td>
                                    <div class="fw-semibold text-dark">
                                        <i class="bi bi-person me-1 text-secondary"></i>
                                        <?= esc($row['nama_peminjam']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($row['nama_barang']) ?></div>
                                    <div class="small text-muted font-monospace">
                                        <?= esc($row['kode_barang']) ?> &bull; <?= esc($row['nama_lokasi']) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                        <?= (int)$row['jumlah_pinjam'] ?> Unit
                                    </span>
                                </td>
                                <td class="text-center small text-muted">
                                    <?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?>
                                </td>
                                <td class="text-center small">
                                    <?php if ($row['tanggal_kembali']) : ?>
                                        <span class="text-success fw-medium">
                                            <?= date('d/m/Y', strtotime($row['tanggal_kembali'])) ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="text-muted fst-italic">Belum kembali</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] === 'Dipinjam') : ?>
                                        <span class="badge-status-dipinjam small">Dipinjam</span>
                                    <?php else : ?>
                                        <span class="badge-status-kembali small">Kembali</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] === 'Dipinjam') : ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success btn-kembalikan" 
                                                data-id="<?= $row['id_peminjaman'] ?>" 
                                                data-nama="<?= esc($row['nama_peminjam']) ?>" 
                                                data-barang="<?= esc($row['nama_barang']) ?>"
                                                data-jumlah="<?= (int)$row['jumlah_pinjam'] ?>"
                                                title="Proses Pengembalian Barang">
                                            <i class="bi bi-check-lg me-1"></i> Kembalikan
                                        </button>
                                    <?php else : ?>
                                        <span class="badge bg-light text-success border small">
                                            <i class="bi bi-check-circle-fill me-1"></i> Tuntas
                                        </span>
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

<!-- Modal Konfirmasi Pengembalian Barang -->
<div class="modal fade" id="modalKembalikan" tabindex="-1" aria-labelledby="modalKembalikanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold fs-6 text-success" id="modalKembalikanLabel">
                    <i class="bi bi-arrow-return-left me-1"></i> Konfirmasi Pengembalian Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formKembalikan" method="post">
                <?= csrf_field() ?>
                <div class="modal-body py-3">
                    <p class="mb-2">Pastikan fisik barang telah dicek dan diterima kembali oleh laboran:</p>
                    
                    <div class="p-3 bg-light rounded border mb-3 small">
                        <div><strong>Peminjam:</strong> <span id="kembaliPeminjam"></span></div>
                        <div><strong>Barang:</strong> <span id="kembaliBarang"></span></div>
                        <div><strong>Jumlah:</strong> <span id="kembaliJumlah"></span> Unit</div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_kembali" class="form-label small fw-semibold">Tanggal Pengembalian Aktual <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="tanggal_kembali" name="tanggal_kembali" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-0">
                        <i class="bi bi-info-circle me-1"></i> Stok barang di sistem akan otomatis ditambahkan kembali sesuai jumlah yang dipinjam.
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-check2-circle me-1"></i> Konfirmasi Barang Diterima
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.querySelectorAll('.btn-kembalikan').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const peminjam = this.getAttribute('data-nama');
            const barang = this.getAttribute('data-barang');
            const jumlah = this.getAttribute('data-jumlah');
            
            document.getElementById('kembaliPeminjam').textContent = peminjam;
            document.getElementById('kembaliBarang').textContent = barang;
            document.getElementById('kembaliJumlah').textContent = jumlah;
            
            const form = document.getElementById('formKembalikan');
            form.action = '<?= base_url('peminjaman/kembalikan') ?>/' + id;
            
            const modal = new bootstrap.Modal(document.getElementById('modalKembalikan'));
            modal.show();
        });
    });
</script>
<?= $this->endSection() ?>
