<?php

namespace App\Controllers;

use App\Libraries\DemoResetService;
use App\Models\UserModel;
use Throwable;

class Auth extends BaseController
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCK_SECONDS = 60;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        $demoReset = new DemoResetService();

        try {
            $demoReset->resetIfDue();
        } catch (Throwable $e) {
            log_message('error', 'Auto-reset data demo dilewati: ' . $e->getMessage());
        }

        $data = [
            'title'          => 'Login - Khazlab',
            'validation'     => session()->getFlashdata('validation') ?? \Config\Services::validation(),
            'demoLastReset'  => $demoReset->lastResetLabel(),
            'demoNextReset'  => $demoReset->nextResetLabel(),
        ];

        return view('auth/login', $data);
    }

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
                'errors' => ['required' => 'Silakan masukkan username.'],
            ],
            'password' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Silakan masukkan password.'],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(base_url('login'))->withInput()->with('validation', $this->validator);
        }

        $username = (string) $this->request->getPost('username');
        $password = (string) $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if (! $user) {
            $this->recordFailedLogin($username);

            return redirect()->to(base_url('login'))->withInput()->with('error', 'Username tidak ditemukan.');
        }

        if (! password_verify($password, $user['password'])) {
            $this->recordFailedLogin($username);

            return redirect()->to(base_url('login'))->withInput()->with('error', 'Password yang dimasukkan salah.');
        }

        $this->clearLoginAttempts();

        session()->regenerate();

        session()->set([
            'id_user'   => $user['id_user'],
            'username'  => $user['username'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, ' . esc($user['username']) . '!');
    }

    public function logout()
    {
        session()->remove(['id_user', 'username', 'role', 'logged_in']);
        session()->regenerate(true);

        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil logout dari sistem.');
    }

    private function loginLockRemaining(): int
    {
        $row = $this->attemptsTable()
            ->select('MAX(attempted_at) AS terakhir, COUNT(*) AS jumlah')
            ->where('ip_address', $this->clientIp())
            ->where('attempted_at >=', date('Y-m-d H:i:s', time() - self::LOGIN_LOCK_SECONDS))
            ->get()->getRowArray();

        if (! $row || (int) $row['jumlah'] < self::MAX_LOGIN_ATTEMPTS) {
            return 0;
        }

        $elapsed = time() - strtotime($row['terakhir']);
        $sisa    = self::LOGIN_LOCK_SECONDS - $elapsed;

        return $sisa > 0 ? $sisa : 0;
    }

    private function recordFailedLogin(string $username): void
    {
        $this->attemptsTable()->insert([
            'ip_address'   => $this->clientIp(),
            'username'     => mb_substr($username, 0, 100),
            'attempted_at' => date('Y-m-d H:i:s'),
        ]);

        $this->attemptsTable()
            ->where('attempted_at <', date('Y-m-d H:i:s', time() - (self::LOGIN_LOCK_SECONDS * 10)))
            ->delete();
    }

    private function clearLoginAttempts(): void
    {
        $this->attemptsTable()->where('ip_address', $this->clientIp())->delete();
    }

    private function clientIp(): string
    {
        return (string) $this->request->getIPAddress();
    }

    private function attemptsTable(): \CodeIgniter\Database\BaseBuilder
    {
        return \Config\Database::connect()->table('login_attempts');
    }
}
