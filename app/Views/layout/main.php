<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title><?= esc($title ?? 'Khazlab - Sistem Informasi Inventaris Laboratorium') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/img/logo-khazlab.svg') ?>">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/khazlab.css') ?>">
</head>
<body>
<?php
$role        = (string) (session()->get('role') ?? 'laboran');
$roleLabel   = $role === 'admin' ? 'Kepala Lab' : 'Petugas Lab';
$roleBadge   = $role === 'admin' ? 'badge-role-admin' : 'badge-role-laboran';
$roleIcon    = $role === 'admin' ? 'bi-shield-check' : 'bi-person-workspace';
?>
<aside id="sidebar" aria-label="Navigasi utama">
    <div class="brand">
        <span class="brand-mark"><img src="<?= base_url('assets/img/logo-khazlab.svg') ?>" alt="Logo Khazlab" class="brand-logo"></span>
        <span class="brand-text">
            <strong>Khazlab</strong>
            <small>Inventaris Laboratorium</small>
        </span>
    </div>

    <nav class="nav-list">
        <p class="nav-label">Menu Utama</p>
        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= url_is('dashboard') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
        <a href="<?= base_url('dashboard/analitik') ?>" class="nav-link <?= url_is('dashboard/analitik') ? 'active' : '' ?>">
            <i class="bi bi-graph-up-arrow"></i><span>Analitik Aset</span>
        </a>

        <p class="nav-label">Inventaris</p>
        <a href="<?= base_url('barang') ?>" class="nav-link <?= url_is('barang*') ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i><span>Data Barang</span>
        </a>
        <a href="<?= base_url('kategori') ?>" class="nav-link <?= url_is('kategori*') ? 'active' : '' ?>">
            <i class="bi bi-tags"></i><span>Kategori</span>
        </a>
        <a href="<?= base_url('lokasi') ?>" class="nav-link <?= url_is('lokasi*') ? 'active' : '' ?>">
            <i class="bi bi-geo-alt"></i><span>Lokasi Ruangan</span>
        </a>

        <p class="nav-label">Sirkulasi</p>
        <a href="<?= base_url('peminjaman') ?>" class="nav-link <?= url_is('peminjaman*') ? 'active' : '' ?>">
            <i class="bi bi-arrow-left-right"></i><span>Peminjaman</span>
        </a>
        <a href="<?= base_url('laporan') ?>" class="nav-link <?= url_is('laporan*') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i><span>Laporan & Cetak</span>
        </a>
    </nav>

    <div class="sidebar-foot">
        <div class="avatar"><i class="bi bi-person-fill"></i></div>
        <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-white text-truncate small"><?= esc(session()->get('username') ?? '-') ?></div>
            <span class="badge <?= $roleBadge ?> mt-1"><i class="bi <?= $roleIcon ?> me-1"></i><?= esc($roleLabel) ?></span>
        </div>
        <form action="<?= base_url('logout') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-outline-light border-0" title="Keluar" aria-label="Keluar">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>

    <div class="sidebar-drawer-actions">
        <a href="<?= base_url('peminjaman/tambah') ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center justify-content-center gap-1">
            <i class="bi bi-plus-lg"></i><span>Pinjam Barang</span>
        </a>
        <?php if ($role === 'admin') : ?>
            <form action="<?= base_url('demo/reset') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline-light w-100 d-inline-flex align-items-center justify-content-center gap-1"
                        data-confirm-reset>
                    <i class="bi bi-arrow-counterclockwise"></i><span>Reset Data Demo</span>
                </button>
            </form>
        <?php endif; ?>
        <form action="<?= base_url('logout') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-light w-100 d-inline-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-box-arrow-right"></i><span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<div id="sidebarBackdrop" class="sidebar-backdrop" aria-hidden="true"></div>

<div id="main-wrapper">    <header class="topbar">
        <div class="topbar-main">
            <button class="btn btn-light border topbar-toggle" id="sidebarToggle" type="button"
                    aria-label="Buka navigasi" aria-controls="sidebar" aria-expanded="false">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="topbar-heading">
                <h1 class="topbar-title"><?= esc($title ?? 'Dashboard') ?></h1>
                <div class="topbar-sub text-muted">
                    <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?> &middot; Sistem Informasi Inventaris Laboratorium Komputer
                </div>
            </div>
        </div>
        <div class="topbar-actions">
            <span class="badge <?= $roleBadge ?> topbar-role-badge align-items-center"><i class="bi <?= $roleIcon ?> me-1"></i><?= esc($roleLabel) ?></span>
            <?php if ($role === 'admin') : ?>
                <form action="<?= base_url('demo/reset') ?>" method="post" class="topbar-desktop-only">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1"
                            data-confirm-reset>
                        <i class="bi bi-arrow-counterclockwise"></i><span>Reset Data Demo</span>
                    </button>
                </form>
            <?php endif; ?>
            <a href="<?= base_url('peminjaman/tambah') ?>" class="btn btn-sm btn-primary topbar-desktop-only align-items-center gap-1">
                <i class="bi bi-plus-lg"></i><span>Pinjam Barang</span>
            </a>
            <form action="<?= base_url('logout') ?>" method="post" class="topbar-desktop-only">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-right"></i><span>Logout</span>
                </button>
            </form>
        </div>
        <div class="topbar-mobile-actions">
            <a href="<?= base_url('peminjaman/tambah') ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-plus-lg"></i><span>Pinjam Barang</span>
            </a>
            <?php if ($role === 'admin') : ?>
                <form action="<?= base_url('demo/reset') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-warning d-inline-flex align-items-center gap-1"
                            data-confirm-reset>
                        <i class="bi bi-arrow-counterclockwise"></i><span>Reset Data Demo</span>
                    </button>
                </form>
            <?php endif; ?>
            <form action="<?= base_url('logout') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-right"></i><span>Logout</span>
                </button>
            </form>
        </div>
    </header>

    <main class="page-content" role="main">
        <?php foreach (['success' => 'check-circle-fill', 'error' => 'exclamation-triangle-fill', 'info' => 'info-circle-fill'] as $key => $icon) : ?>
            <?php if (session()->getFlashdata($key)) : ?>
                <div class="alert alert-<?= $key === 'info' ? 'primary' : $key ?> alert-dismissible fade show d-flex align-items-start gap-2 mb-3 no-print" role="alert">
                    <i class="bi bi-<?= $icon ?> mt-1"></i>
                    <div class="flex-grow-1 small"><?= esc(session()->getFlashdata($key)) ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?= $this->renderSection('content') ?>
    </main>
</div>

<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="confirm-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <h5 class="modal-title fs-6 fw-bold" id="modalKonfirmasiLabel">Konfirmasi Tindakan</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body small" id="modalKonfirmasiBody"></div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-danger" id="modalKonfirmasiOk">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (() => {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const backdrop = document.getElementById('sidebarBackdrop');

        const setOpen = (open) => {
            sidebar.classList.toggle('show', open);
            backdrop.classList.toggle('show', open);
            document.body.classList.toggle('sidebar-open', open);
            toggle?.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        toggle?.addEventListener('click', () => setOpen(!sidebar.classList.contains('show')));
        backdrop?.addEventListener('click', () => setOpen(false));
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setOpen(false);
        });
        sidebar.querySelectorAll('.nav-link').forEach((link) => link.addEventListener('click', () => setOpen(false)));
    })();

    window.khazlabConfirm = (message, onConfirm, label = 'Ya, Lanjutkan') => {
        const modalEl = document.getElementById('modalKonfirmasi');
        document.getElementById('modalKonfirmasiBody').innerHTML = message;
        document.getElementById('modalKonfirmasiOk').textContent = label;
        const modal = new bootstrap.Modal(modalEl);
        const ok = document.getElementById('modalKonfirmasiOk');

        const handler = () => {
            ok.removeEventListener('click', handler);
            modal.hide();
            onConfirm();
        };
        ok.addEventListener('click', handler);
        modal.show();
    };

    document.querySelectorAll('[data-confirm-reset]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            khazlabConfirm(
                'Reset seluruh data demo ke kondisi awal? Semua perubahan (barang, kategori, lokasi, peminjaman) akan dihapus dan diganti data seed.',
                () => button.form.submit(),
                'Ya, Reset Data'
            );
        });
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
