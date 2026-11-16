<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'SIM Inventaris Lab Komputer') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #2563eb;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
            --header-bg: #ffffff;
            --primary: #2563eb;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--body-bg);
            color: #1e293b;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        #sidebar .brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        #sidebar .nav-list {
            padding: 1rem 0.75rem;
            flex-grow: 1;
        }

        #sidebar .nav-item {
            margin-bottom: 0.25rem;
        }

        #sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 0.7rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        #sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
        }

        #sidebar .nav-link.active {
            background-color: var(--sidebar-active);
            color: var(--sidebar-text-active);
            font-weight: 600;
        }

        #sidebar .user-info {
            padding: 1rem 1.25rem;
            border-top: 1px solid #1e293b;
            background-color: #0b1120;
        }

        /* Main Content Styling */
        #main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Top Header */
        .top-header {
            background-color: var(--header-bg);
            border-bottom: 1px solid var(--card-border);
            padding: 0.9rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .page-content {
            padding: 1.75rem;
            flex-grow: 1;
        }

        /* Cards & Components */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 10px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .card-custom .card-header {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: #0f172a;
        }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            border-bottom: 1px solid var(--card-border);
            padding: 0.85rem 1rem;
        }

        .table-custom tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            color: #334155;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--card-border);
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge-kondisi-baik {
            display: inline-block;
            white-space: nowrap;
            background-color: #dcfce7;
            color: #15803d;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-kondisi-rusak-ringan {
            display: inline-block;
            white-space: nowrap;
            background-color: #fef9c3;
            color: #a16207;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-kondisi-rusak-berat {
            display: inline-block;
            white-space: nowrap;
            background-color: #fee2e2;
            color: #b91c1c;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-status-dipinjam {
            display: inline-block;
            white-space: nowrap;
            background-color: #dbeafe;
            color: #1d4ed8;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        .badge-status-kembali {
            display: inline-block;
            white-space: nowrap;
            background-color: #dcfce7;
            color: #15803d;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }

        /* Responsive Mobile Layout */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: -260px;
            }
            #sidebar.show {
                margin-left: 0;
            }
            #main-wrapper {
                margin-left: 0;
            }
            .page-content {
                padding: 1rem;
            }
        }

        /* Print Mode Optimization */
        @media print {
            #sidebar, .top-header, .no-print {
                display: none !important;
            }
            #main-wrapper {
                margin-left: 0 !important;
            }
            .card-custom {
                border: none !important;
                box-shadow: none !important;
            }
            .page-content {
                padding: 0 !important;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar Navigasi -->
<aside id="sidebar" aria-label="Sidebar Navigasi">
    <div class="brand">
        <i class="bi bi-cpu-fill text-primary"></i>
        <span>SIM-LAB</span>
    </div>

    <ul class="nav-list list-unstyled">
        <li class="nav-item">
            <a href="<?= base_url('dashboard') ?>" class="nav-link <?= (url_is('dashboard*')) ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('barang') ?>" class="nav-link <?= (url_is('barang*')) ? 'active' : '' ?>">
                <i class="bi bi-box-seam"></i>
                <span>Data Barang</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('kategori') ?>" class="nav-link <?= (url_is('kategori*')) ? 'active' : '' ?>">
                <i class="bi bi-tags"></i>
                <span>Kategori</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('lokasi') ?>" class="nav-link <?= (url_is('lokasi*')) ? 'active' : '' ?>">
                <i class="bi bi-geo-alt"></i>
                <span>Lokasi Penyimpanan</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('peminjaman') ?>" class="nav-link <?= (url_is('peminjaman*')) ? 'active' : '' ?>">
                <i class="bi bi-arrow-left-right"></i>
                <span>Peminjaman & Kembali</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= base_url('laporan') ?>" class="nav-link <?= (url_is('laporan*')) ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>Laporan Inventaris</span>
            </a>
        </li>
    </ul>

    <div class="user-info d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 overflow-hidden">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; flex-shrink: 0;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="text-truncate">
                <div class="text-white small fw-semibold text-truncate"><?= esc(session()->get('username') ?? 'Admin') ?></div>
                <div class="text-white-50" style="font-size: 0.75rem;">Administrator</div>
            </div>
        </div>
        <form action="<?= base_url('logout') ?>" method="post" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-outline-danger" title="Keluar dari sistem">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</aside>

<!-- Konten Utama -->
<div id="main-wrapper">
    <!-- Top Header -->
    <header class="top-header no-print">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-lg-none border" id="sidebarToggle" type="button" aria-label="Toggle Navigation">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h2 class="h5 mb-0 fw-bold text-dark"><?= esc($title ?? 'Dashboard') ?></h2>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
            </span>
            <form action="<?= base_url('logout') ?>" method="post" class="d-none d-sm-inline-flex align-items-center">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Konten Halaman -->
    <main class="page-content" role="main">
        <!-- Notifikasi Sukses -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 no-print" role="alert">
                <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                <div class="small fw-medium"><?= esc(session()->getFlashdata('success')) ?></div>
                <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Error -->
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 no-print" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                <div class="small fw-medium"><?= esc(session()->getFlashdata('error')) ?></div>
                <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Responsive Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
