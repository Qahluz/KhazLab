<?php

namespace App\Libraries;

class PeminjamanStatus
{
    public const MASA_PINJAM_HARI = 7;

    public const AMBANG_STOK_MENIPIS = 3;

    public static function defaultJatuhTempo(string $tanggalPinjam): string
    {
        return date('Y-m-d', strtotime($tanggalPinjam . ' +' . self::MASA_PINJAM_HARI . ' days'));
    }

    public static function label(array $row, ?string $today = null): string
    {
        if (($row['status'] ?? '') === 'Kembali') {
            return 'Kembali';
        }

        if (! empty($row['tanggal_jatuh_tempo']) && ($today ?? date('Y-m-d')) > $row['tanggal_jatuh_tempo']) {
            return 'Terlambat';
        }

        return 'Dipinjam';
    }

    public static function hariTerlambat(array $row, ?string $today = null): int
    {
        if (self::label($row, $today) !== 'Terlambat') {
            return 0;
        }

        return (int) floor((strtotime($today ?? date('Y-m-d')) - strtotime($row['tanggal_jatuh_tempo'])) / 86400);
    }

    public static function badgeClass(string $label): string
    {
        return match ($label) {
            'Kembali'   => 'badge-status-kembali',
            'Terlambat' => 'badge-status-terlambat',
            default     => 'badge-status-dipinjam',
        };
    }
}
