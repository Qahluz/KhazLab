<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCK_SECONDS = 60;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Menampilkan halaman formulir login.
     */
    public function login()
    {
        // Jika sudah memiliki sesi login aktif, langsung arahkan ke dashboard
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title'      => 'Login Administrator - SIM Inventaris Lab',
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ];

        return view('auth/login', $data);
    }

    /**
     * Memproses verifikasi kredensial login.
     */
    public function attemptLogin()
    {
        $lockRemaining = $this->loginLockRemaining();
        if ($lockRemaining > 0) {
            return redirect()->to(base_url('login'))->withInput()->with(
                'error',
                "Terlalu banyak percobaan login. Silakan coba lagi dalam {$lockRemaining} detik."
            );
        }

        $rules = [
            'username' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Silakan masukkan username.',
                ],
            ],
            'password' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Silakan masukkan password.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('login'))->withInput()->with('validation', $this->validator);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if (!$user) {
            $this->recordFailedLogin();
            return redirect()->to(base_url('login'))->withInput()->with('error', 'Username tidak ditemukan.');
        }

        if (!password_verify($password, $user['password'])) {
            $this->recordFailedLogin();
            return redirect()->to(base_url('login'))->withInput()->with('error', 'Password yang dimasukkan salah.');
        }

        // Bersihkan penghitung percobaan gagal sebelum membuat sesi baru.
        session()->remove(['login_attempts', 'login_last_attempt']);

        // Cegah session fixation dengan mengganti ID sesi setelah login berhasil.
        session()->regenerate();

        // Simpan sesi autentikasi pengguna
        $sessionData = [
            'id_user'   => $user['id_user'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'logged_in' => true,
        ];

        session()->set($sessionData);

        return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, ' . esc($user['username']) . '!');
    }

    /**
     * Mengakhiri sesi login (logout).
     */
    public function logout()
    {
        session()->remove([
            'id_user',
            'username',
            'role',
            'logged_in',
            'login_attempts',
            'login_last_attempt',
        ]);
        session()->regenerate(true);

        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil logout dari sistem.');
    }

    /**
     * Mengembalikan sisa waktu blokir login (detik).
     * Jika sudah lewat masa blokir, penghitung percobaan direset.
     */
    private function loginLockRemaining(): int
    {
        $attempts = (int) session()->get('login_attempts');
        $lastAttempt = (int) session()->get('login_last_attempt');

        if ($attempts < self::MAX_LOGIN_ATTEMPTS) {
            return 0;
        }

        $elapsed = time() - $lastAttempt;
        if ($elapsed >= self::LOGIN_LOCK_SECONDS) {
            session()->remove(['login_attempts', 'login_last_attempt']);
            return 0;
        }

        return self::LOGIN_LOCK_SECONDS - $elapsed;
    }

    /**
     * Mencatat satu percobaan login yang gagal.
     */
    private function recordFailedLogin(): void
    {
        session()->set([
            'login_attempts'     => (int) session()->get('login_attempts') + 1,
            'login_last_attempt' => time(),
        ]);
    }
}
