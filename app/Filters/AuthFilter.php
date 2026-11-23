<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Argumen filter adalah role yang diizinkan, mis. ['filter' => 'auth:admin'].
     * Tanpa argumen: semua user yang login boleh lewat.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session('logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        if (empty($arguments)) {
            return;
        }

        $role = (string) session('role');

        if (in_array($role, $arguments, true)) {
            return;
        }

        $message = $role === 'laboran'
            ? 'Akses ditolak: akun Petugas Lab tidak berwenang mengubah data master. Hanya Kepala Lab yang dapat menambah, mengubah, atau menghapus data.'
            : 'Akses ditolak: role akun Anda (' . ($role === '' ? 'tidak dikenal' : $role) . ') tidak berwenang melakukan aksi ini.';

        return redirect()
            ->to(previous_url() ?: base_url('dashboard'))
            ->with('error', $message);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
