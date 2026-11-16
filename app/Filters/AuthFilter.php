<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $role = (string) session()->get('role');
        if ($arguments !== null && $arguments !== [] && ! in_array($role, $arguments, true)) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Akun Anda tidak memiliki hak akses untuk halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
