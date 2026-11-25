<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php $isAdmin = session('role') === 'admin'; ?>
<div class="qr-print-scope">
<style>
    .sticker-sheet { display: flex; justify-content: center; background: #f1f5f9; border-radius: 10px; padding: 1.25rem; }
    .sticker {
        width: 260px; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px;
        padding: .6rem .7rem .75rem; font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; color: #0f172a;
    }
    .sticker-head {
        font-size: .5rem; letter-spacing: .05em; text-transform: uppercase; font-weight: 700;
        color: #1e3a8a; border-bottom: 1px solid #e2e8f0; padding-bottom: .3rem; margin-bottom: .45rem;
    }
    .sticker-body { display: flex; gap: .55rem; align-items: flex-start; }
    .sticker-qr { flex: 0 0 auto; line-height: 0; }
    .sticker-qr img, .sticker-qr canvas { display: block; }
    .sticker-info { min-width: 0; font-size: .58rem; line-height: 1.45; }
    .sticker-code { font-family: ui-monospace, 'Cascadia Mono', Consolas, monospace; font-size: .68rem; font-weight: 700; letter-spacing: .02em; }
    .sticker-name { font-weight: 600; margin: .1rem 0 .3rem; overflow-wrap: anywhere; }
    .sticker-info dl { margin: 0; }
    .sticker-info dt { font-weight: 500; color: #64748b; font-size: .5rem; text-transform: uppercase; letter-spacing: .04em; }
    .sticker-info dd { margin: 0 0 .18rem; font-weight: 600; }

    /* Hanya aktif saat QR sticker dicetak: body diberi kelas qr-printing oleh tombol Cetak Stiker.
       Tidak menyentuh aturan @media print layout (sidebar/topbar/no-print) maupun laporan cetak. */
    @media print {
        body.qr-printing #sidebar,
        body.qr-printing .topbar,
        body.qr-printing .qr-print-scope,
        body.qr-printing .modal-backdrop { display: none !important; }
        body.qr-printing #main-wrapper { margin-left: 0 !important; }
        body.qr-printing .page-content { padding: 0 !important; }
        body.qr-printing .modal {
            position: static !important; display: block !important; overflow: visible !important;
            background: #fff !important;
        }
        body.qr-printing .modal-dialog { margin: 0 !important; max-width: none !important; }
        body.qr-printing .modal-content { border: 0 !important; box-shadow: none !important; }
        body.qr-printing .modal-header,
        body.qr-printing .modal-footer { display: none !important; }
        body.qr-printing .modal-body { padding: 0 !important; }
        body.qr-printing .sticker-sheet { background: #fff !important; padding: 0 !important; display: block !important; }
        body.qr-printing .sticker {
            border: 1px solid #000 !important; border-radius: 0 !important;
            width: 62mm !important; padding: 2mm !important;
            break-inside: avoid; page-break-inside: avoid;
        }
        body.qr-printing .sticker-head { color: #000 !important; border-bottom-color: #000 !important; }
        body.qr-printing .sticker-code,
        body.qr-printing .sticker-info dt,
        body.qr-printing .sticker-info dd { color: #000 !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
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
                        <td class="mono col-kode"><?= esc($item['kode_barang']) ?></td>
                        <td class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></td>
                        <td class="small text-muted"><?= esc($item['nama_kategori']) ?></td>
                        <td class="small text-muted"><?= esc($item['nama_lokasi']) ?></td>
                        <td class="text-center fw-semibold <?= (int) $item['jumlah'] <= 3 ? 'text-danger' : 'text-dark' ?>"><?= (int) $item['jumlah'] ?></td>
                        <td class="text-center"><span class="<?= $kelas ?>"><?= esc($item['kondisi']) ?></span></td>
                        <td class="text-center small text-muted col-tanggal"><?= date('d/m/Y', strtotime($item['tanggal_masuk'])) ?></td>
                        <td class="text-center">
                            <?php if ($isAdmin) : ?>
                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                <a href="<?= base_url('barang/edit/' . $item['id_barang']) ?>" class="btn btn-sm btn-outline-primary" title="Ubah data">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-qr"
                                        data-id="<?= (int) $item['id_barang'] ?>"
                                        data-kode="<?= esc($item['kode_barang'], 'attr') ?>"
                                        data-nama="<?= esc($item['nama_barang'], 'attr') ?>"
                                        data-kategori="<?= esc($item['nama_kategori'], 'attr') ?>"
                                        data-lokasi="<?= esc($item['nama_lokasi'], 'attr') ?>"
                                        title="Label stiker QR">
                                    <i class="bi bi-qr-code"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus"
                                        data-id="<?= $item['id_barang'] ?>"
                                        data-nama="<?= esc($item['nama_barang']) ?>"
                                        title="Hapus data">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <?php else : ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-qr"
                                    data-id="<?= (int) $item['id_barang'] ?>"
                                    data-kode="<?= esc($item['kode_barang'], 'attr') ?>"
                                    data-nama="<?= esc($item['nama_barang'], 'attr') ?>"
                                    data-kategori="<?= esc($item['nama_kategori'], 'attr') ?>"
                                    data-lokasi="<?= esc($item['nama_lokasi'], 'attr') ?>"
                                    title="Label stiker QR (baca saja)">
                                <i class="bi bi-qr-code"></i>
                            </button>
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

</div>

<form id="formHapusBarang" method="post" class="d-none"><?= csrf_field() ?></form>

<div class="modal fade" id="modalQr" tabindex="-1" aria-labelledby="modalQrLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fs-6 fw-bold" id="modalQrLabel"><i class="bi bi-qr-code me-1"></i>Label Stiker Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="sticker-sheet">
                    <div class="sticker" id="stickerCard">
                        <div class="sticker-head">Inventaris Lab Komputer - Khazlab</div>
                        <div class="sticker-body">
                            <div class="sticker-qr" id="stickerQr"></div>
                            <div class="sticker-info">
                                <div class="sticker-code" id="stickerKode"></div>
                                <div class="sticker-name" id="stickerNama"></div>
                                <dl>
                                    <dt>Kategori</dt>
                                    <dd id="stickerKategori"></dd>
                                    <dt>Lokasi</dt>
                                    <dd id="stickerLokasi"></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-muted small mt-3 mb-0">
                    Stiker berukuran &plusmn; 62 mm. Memindai QR membuka hasil pencarian perangkat di Khazlab.
                </p>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-sm btn-primary" id="btnCetakStiker"><i class="bi bi-printer me-1"></i>Cetak Stiker</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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

    (() => {
        const modalEl = document.getElementById('modalQr');
        if (!modalEl) {
            return;
        }

        const qrBox = document.getElementById('stickerQr');
        const modal = new bootstrap.Modal(modalEl);
        const qrBase = '<?= base_url('barang') ?>?keyword=';

        document.querySelectorAll('.btn-qr').forEach(button => {
            button.addEventListener('click', function () {
                const data = this.dataset;
                document.getElementById('stickerKode').textContent = data.kode;
                document.getElementById('stickerNama').textContent = data.nama;
                document.getElementById('stickerKategori').textContent = data.kategori;
                document.getElementById('stickerLokasi').textContent = data.lokasi;

                qrBox.innerHTML = '';
                new QRCode(qrBox, {
                    text: qrBase + encodeURIComponent(data.kode),
                    width: 96,
                    height: 96,
                    correctLevel: QRCode.CorrectLevel.M
                });

                modal.show();
            });
        });

        document.getElementById('btnCetakStiker').addEventListener('click', () => {
            document.body.classList.add('qr-printing');
            window.print();
            document.body.classList.remove('qr-printing');
        });
    })();
</script>
<?= $this->endSection() ?>
