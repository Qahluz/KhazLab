<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
    <div>
        <h2 class="h5 fw-bold mb-1 text-dark">Transaksi Peminjaman &amp; Pengembalian</h2>
        <p class="text-muted small mb-0">Pantau sirkulasi aset laboratorium beserta status jatuh tempo secara otomatis.</p>
    </div>
    <a href="<?= base_url('peminjaman/tambah') ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i><span>Catat Peminjaman</span>
    </a>
</div>

<div class="stat-grid">
    <div class="stat-card" style="--stat-color:#1d4ed8; --stat-bg:#e0e9ff">
        <i class="bi bi-collection stat-icon"></i>
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value"><?= $ringkasan['total'] ?></div>
        <div class="stat-hint">Seluruh riwayat peminjaman</div>
    </div>
    <div class="stat-card" style="--stat-color:#b45309; --stat-bg:#fef3c7">
        <i class="bi bi-clock-history stat-icon"></i>
        <div class="stat-label">Sedang Dipinjam</div>
        <div class="stat-value"><?= $ringkasan['dipinjam'] ?></div>
        <div class="stat-hint">Belum dikembalikan</div>
    </div>
    <div class="stat-card" style="--stat-color:#b91c1c; --stat-bg:#fee2e2">
        <i class="bi bi-alarm stat-icon"></i>
        <div class="stat-label">Terlambat</div>
        <div class="stat-value"><?= $ringkasan['terlambat'] ?></div>
        <div class="stat-hint">Melewati tanggal jatuh tempo</div>
    </div>
    <div class="stat-card" style="--stat-color:#15803d; --stat-bg:#dcfce7">
        <i class="bi bi-check2-circle stat-icon"></i>
        <div class="stat-label">Selesai</div>
        <div class="stat-value"><?= $ringkasan['kembali'] ?></div>
        <div class="stat-hint">Barang sudah diterima lab</div>
    </div>
</div>

<div class="card-custom">
    <div class="card-header">
        <span><i class="bi bi-funnel me-1 text-primary"></i>Filter Transaksi</span>
        <?php if ($keyword !== '' || ! empty($selectedStatus)) : ?>
            <a href="<?= base_url('peminjaman') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Reset Filter</a>
        <?php endif; ?>
    </div>
    <div class="p-3">
        <form method="get" action="<?= base_url('peminjaman') ?>" class="filter-bar">
            <div>
                <label class="form-label" for="keyword">Kata Kunci</label>
                <input type="search" class="form-control form-control-sm" id="keyword" name="keyword" value="<?= esc($keyword) ?>" placeholder="Nama peminjam / barang / kode">
            </div>
            <div>
                <label class="form-label" for="status">Status</label>
                <select class="form-select form-select-sm" id="status" name="status">
                    <option value="">Semua status</option>
                    <?php foreach (['Dipinjam', 'Terlambat', 'Kembali'] as $opt) : ?>
                        <option value="<?= $opt ?>" <?= $selectedStatus === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header">
        <span><i class="bi bi-table me-1 text-primary"></i>Daftar Peminjaman</span>
        <span class="text-muted small">Menampilkan <?= count($peminjaman) ?> dari <?= $total ?> transaksi</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width:56px">No</th>
                    <th>Peminjam</th>
                    <th>Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Tgl Pinjam</th>
                    <th class="text-center">Jatuh Tempo</th>
                    <th class="text-center">Tgl Kembali</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($peminjaman)) : ?>
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-arrow-left-right"></i></div>
                            <h6>Tidak ada transaksi yang cocok</h6>
                            <p>Ubah kata kunci atau filter status, atau catat peminjaman baru untuk memulai.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= base_url('peminjaman') ?>" class="btn btn-sm btn-outline-secondary">Reset Filter</a>
                                <a href="<?= base_url('peminjaman/tambah') ?>" class="btn btn-sm btn-primary">Catat Peminjaman</a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php else : ?>
                <?php $no = ($pager ? ($pager->getCurrentPage('peminjaman') - 1) * 10 : 0) + 1; ?>
                <?php foreach ($peminjaman as $row) : ?>
                    <?php
                    $label = \App\Libraries\PeminjamanStatus::label($row);
                    $telat = \App\Libraries\PeminjamanStatus::hariTerlambat($row);
                    ?>
                    <tr>
                        <td class="text-center text-muted"><?= $no++ ?></td>
                        <td class="fw-semibold text-dark"><?= esc($row['nama_peminjam']) ?></td>
                        <td>
                            <div class="fw-semibold text-dark"><?= esc($row['nama_barang']) ?></div>
                            <div class="mono text-muted"><?= esc($row['kode_barang']) ?> &middot; <?= esc($row['nama_lokasi']) ?></div>
                        </td>
                        <td class="text-center"><?= (int) $row['jumlah_pinjam'] ?> unit</td>
                        <td class="text-center small text-muted col-tanggal"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                        <td class="text-center small col-tanggal">
                            <?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?>
                            <?php if ($telat > 0) : ?>
                                <div class="text-danger" style="font-size:.72rem">+<?= $telat ?> hari</div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center small col-tanggal">
                            <?= $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '<span class="text-muted fst-italic">-</span>' ?>
                        </td>
                        <td class="text-center">
                            <span class="<?= \App\Libraries\PeminjamanStatus::badgeClass($label) ?>"><?= $label ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($row['status'] === 'Dipinjam') : ?>
                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                  <a href="<?= esc(\App\Libraries\PeminjamanStatus::buildWhatsappReminderUrl($row), 'attr') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success" title="Kirim pengingat jatuh tempo via WhatsApp"><i class="bi bi-whatsapp"></i> Ingatkan</a>
                                  <button type="button" class="btn btn-sm btn-outline-success btn-kembalikan"
                                          data-id="<?= $row['id_peminjaman'] ?>"
                                          data-nama="<?= esc($row['nama_peminjam']) ?>"
                                          data-barang="<?= esc($row['nama_barang']) ?>"
                                          data-jumlah="<?= (int) $row['jumlah_pinjam'] ?>"
                                          data-tempo="<?= date('d/m/Y', strtotime($row['tanggal_jatuh_tempo'])) ?>">
                                      <i class="bi bi-check-lg me-1"></i>Kembalikan
                                  </button>
                                </div>
                            <?php else : ?>
                                <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Tuntas</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (! empty($peminjaman) && $pager && $pager->getPageCount('peminjaman') > 1) : ?>
        <div class="p-3 border-top">
            <?= $pager->only(['keyword', 'status'])->links('peminjaman', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalKembalikan" tabindex="-1" aria-labelledby="modalKembalikanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formKembalikan" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="confirm-icon" style="background:#dcfce7;color:#15803d"><i class="bi bi-arrow-return-left"></i></span>
                        <h5 class="modal-title fs-6 fw-bold" id="modalKembalikanLabel">Konfirmasi Pengembalian</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body small">
                    <p class="mb-2">Pastikan fisik perangkat telah diperiksa dan diterima kembali oleh laboran.</p>
                    <div class="p-3 bg-light rounded border mb-3">
                        <div><strong>Peminjam:</strong> <span id="kembaliPeminjam"></span></div>
                        <div><strong>Barang:</strong> <span id="kembaliBarang"></span></div>
                        <div><strong>Jumlah:</strong> <span id="kembaliJumlah"></span> unit</div>
                        <div><strong>Jatuh tempo:</strong> <span id="kembaliTempo"></span></div>
                    </div>
                    <label for="tanggal_kembali" class="form-label small fw-semibold">Tanggal Pengembalian Aktual <span class="text-danger">*</span></label>
                    <input type="date" class="form-control form-control-sm" id="tanggal_kembali" name="tanggal_kembali" value="<?= date('Y-m-d') ?>" required>
                    <div class="alert alert-primary py-2 px-3 mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>Stok akan otomatis dikembalikan ke gudang setelah konfirmasi.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2-circle me-1"></i>Konfirmasi Diterima</button>
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
            document.getElementById('kembaliPeminjam').textContent = this.dataset.nama;
            document.getElementById('kembaliBarang').textContent = this.dataset.barang;
            document.getElementById('kembaliJumlah').textContent = this.dataset.jumlah;
            document.getElementById('kembaliTempo').textContent = this.dataset.tempo;
            document.getElementById('formKembalikan').action = '<?= base_url('peminjaman/kembalikan') ?>/' + this.dataset.id;
            new bootstrap.Modal(document.getElementById('modalKembalikan')).show();
        });
    });
</script>
<?= $this->endSection() ?>
