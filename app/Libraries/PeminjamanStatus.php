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

    public static function buildWhatsappReminderUrl(array $row, ?string $today = null): string
    {
        $tglPinjam = date('d/m/Y', strtotime($row['tanggal_pinjam']));
        $tglTempo  = date('d/m/Y', strtotime($row['tanggal_jatuh_tempo']));
        $aset      = $row['nama_barang'] . ' [' . $row['kode_barang'] . ']';

        if (self::label($row, $today) === 'Terlambat') {
            $pesan = 'Halo ' . $row['nama_peminjam'] . ', mengingatkan bahwa peminjaman aset Laboratorium Komputer Khazlab ('
                . $aset . ' sebanyak ' . (int) $row['jumlah_pinjam'] . ' unit, tgl pinjam ' . $tglPinjam
                . ') telah MELEWATI jatuh tempo pada ' . $tglTempo
                . '. Mohon segera dikembalikan ke laboratorium. Terima kasih.';
        } else {
            $pesan = 'Halo ' . $row['nama_peminjam'] . ', pengingat peminjaman aset Laboratorium Komputer Khazlab: '
                . $aset . ' sebanyak ' . (int) $row['jumlah_pinjam'] . ' unit (tgl pinjam ' . $tglPinjam
                . ') memiliki jadwal jatuh tempo pengembalian pada ' . $tglTempo . '. Terima kasih.';
        }

        return 'https://wa.me/?text=' . rawurlencode($pesan);
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
