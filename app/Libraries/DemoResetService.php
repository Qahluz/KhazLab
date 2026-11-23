<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Throwable;

/**
 * Menjalankan ulang app/Database/demo_seed.sql lewat koneksi database CodeIgniter
 * (bukan exec/shell, yang diblokir shared hosting). Penanda waktu ada di
 * writable/demo_last_reset.txt.
 */
class DemoResetService
{
    public const INTERVAL = 86400;
    public const ROLES    = ['admin', 'laboran'];

    private string $seedPath;
    private string $markerPath;

    public function __construct(?string $seedPath = null, ?string $markerPath = null)
    {
        $this->seedPath   = $seedPath ?? APPPATH . 'Database/demo_seed.sql';
        $this->markerPath = $markerPath ?? WRITEPATH . 'demo_last_reset.txt';
    }

    public function resetIfDue(): bool
    {
        $last = $this->lastResetAt();

        if ($last !== null && (time() - $last) < self::INTERVAL) {
            return false;
        }

        if (! is_file($this->seedPath)) {
            throw new \RuntimeException('Berkas seed demo tidak ditemukan: ' . $this->seedPath);
        }

        $this->reset();

        return true;
    }

    public function reset(): int
    {
        $sql     = (string) file_get_contents($this->seedPath);
        $db      = Database::connect();
        $executed = 0;

        foreach ($this->splitStatements($sql) as $statement) {
            $db->query($statement);
            $executed++;
        }

        $this->touch();

        return $executed;
    }

    public function lastResetAt(): ?int
    {
        if (! is_file($this->markerPath)) {
            return null;
        }

        $raw = trim((string) @file_get_contents($this->markerPath));

        return is_numeric($raw) ? (int) $raw : null;
    }

    public function lastResetLabel(): string
    {
        $last = $this->lastResetAt();

        return $last === null
            ? 'Belum pernah direset — data akan direset pada pengecekan berikutnya.'
            : date('d/m/Y H:i', $last) . ' WIB';
    }

    public function nextResetLabel(): string
    {
        $last = $this->lastResetAt();

        return date('d/m/Y H:i', ($last ?? time()) + self::INTERVAL) . ' WIB';
    }

    /**
     * Statement dipisah pada ';' di luar string literal, dan komentar dibuang,
     * supaya isi data yang memuat ';' tidak memotong query.
     *
     * @return list<string>
     */
    private function splitStatements(string $sql): array
    {
        $statements = [];
        $current    = '';
        $length     = strlen($sql);
        $quote      = null;

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];

            if ($quote !== null) {
                $current .= $char;

                if ($char === '\\' && $i + 1 < $length) {
                    $current .= $sql[++$i];
                } elseif ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($char === "'" || $char === '"' || $char === '`') {
                $quote    = $char;
                $current .= $char;

                continue;
            }

            if ($char === '#' || ($char === '-' && substr($sql, $i, 3) === '-- ')) {
                $newline = strpos($sql, "\n", $i);
                $i       = $newline === false ? $length : $newline;

                continue;
            }

            if ($char === ';') {
                $this->push($statements, $current);
                $current = '';

                continue;
            }

            $current .= $char;
        }

        $this->push($statements, $current);

        return $statements;
    }

    private function push(array &$statements, string $candidate): void
    {
        $candidate = trim($candidate);

        if ($candidate !== '') {
            $statements[] = $candidate;
        }
    }

    private function touch(): void
    {
        $written = @file_put_contents($this->markerPath, (string) time(), LOCK_EX);

        if ($written === false) {
            log_message('error', 'DemoResetService: gagal menulis penanda reset di {path}', ['path' => $this->markerPath]);
        }
    }
}
