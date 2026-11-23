<?php

namespace App\Controllers;

use App\Libraries\DemoResetService;
use Throwable;

class Demo extends BaseController
{
    public function reset()
    {
        try {
            $count = (new DemoResetService())->reset();

            return redirect()->to(base_url('dashboard'))
                ->with('success', "Data demo berhasil direset ke kondisi awal ({$count} perintah SQL dijalankan).");
        } catch (Throwable $e) {
            log_message('error', 'Reset data demo gagal: ' . $e->getMessage());

            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Reset data demo gagal: ' . $e->getMessage());
        }
    }
}
