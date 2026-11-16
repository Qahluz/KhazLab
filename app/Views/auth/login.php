<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login Administrator - SIM Inventaris Lab') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-color: #1d4ed8;
            --primary-hover: #1e40af;
            --bg-body: #f5f7fb;
            --card-border: #e2e8f0;
            --text-heading: #0f172a;
            --text-muted: #64748b;
        }

        body {
            background: linear-gradient(160deg, #0f172a 0%, #14213d 45%, #1d4ed8 160%);
            font-family: "Segoe UI", system-ui, -apple-system, Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
            color: #334155;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            max-width: 420px;
            width: 100%;
            padding: 2.25rem 2rem;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            background-color: var(--primary-color);
            color: #ffffff;
            border-radius: 12px;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 6px 16px rgba(29, 78, 216, .35);
        }

        .login-title {
            color: var(--text-heading);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1.75rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 0.4rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0.65rem 0.9rem;
            font-size: 0.95rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            outline: 0;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border: 1px solid var(--primary-color);
            color: #ffffff;
            font-weight: 600;
            padding: 0.7rem 1.25rem;
            border-radius: 8px;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #ffffff;
        }

        .btn-primary-custom:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.35);
        }

        .demo-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.825rem;
            color: #475569;
            margin-top: 1.5rem;
        }

        .demo-box code {
            color: var(--primary-color);
            font-weight: 600;
            background: #e2e8f0;
            padding: 0.1rem 0.3rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<main class="login-card" role="main">
    <div class="text-center">
        <div class="brand-badge">
            <i class="bi bi-box-seam"></i>
        </div>
        <h1 class="login-title">SIM-LAB</h1>
        <p class="login-subtitle">Sistem Informasi Inventaris Laboratorium Komputer</p>
    </div>

    <!-- Alert Notifikasi Error -->
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
            <div class="small"><?= esc(session()->getFlashdata('error')) ?></div>
            <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Alert Notifikasi Sukses -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
            <div class="small"><?= esc(session()->getFlashdata('success')) ?></div>
            <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endif; ?>

    <!-- Formulir Login -->
    <form action="<?= base_url('login/auth') ?>" method="post" novalidate>
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                <input type="text" 
                       class="form-control border-start-0 ps-1 <?= (isset($validation) && $validation->hasError('username')) ? 'is-invalid' : '' ?>" 
                       id="username" 
                       name="username" 
                       value="<?= old('username') ?>" 
                       placeholder="Masukkan username" 
                       autocomplete="username" 
                       required>
                <?php if (isset($validation) && $validation->hasError('username')) : ?>
                    <div class="invalid-feedback d-block">
                        <?= esc($validation->getError('username')) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                <input type="password" 
                       class="form-control border-start-0 ps-1 <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>" 
                       id="password" 
                       name="password" 
                       placeholder="Masukkan password" 
                       autocomplete="current-password" 
                       required>
                <?php if (isset($validation) && $validation->hasError('password')) : ?>
                    <div class="invalid-feedback d-block">
                        <?= esc($validation->getError('password')) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
        </button>
    </form>

    <div class="demo-box">
        <div class="fw-semibold mb-1"><i class="bi bi-key me-1"></i>Akun demo</div>
        <div>Username: <code>admin</code> &middot; Password: <code>admin123</code></div>
        <div class="mt-1 text-muted">Percobaan login salah dibatasi 5 kali per 60 detik.</div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
