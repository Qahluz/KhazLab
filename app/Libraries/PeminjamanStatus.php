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
        $nama       = $row['nama_peminjam'];
        $barang     = $row['nama_barang'] . ' (' . $row['kode_barang'] . ')';
        $jumlah     = (int) $row['jumlah_pinjam'];
        $tglPinjam  = date('d/m/Y', strtotime($row['tanggal_pinjam']));
        $tglTempo   = date('d/m/Y', strtotime($row['tanggal_jatuh_tempo']));

        if (self::label($row, $today) === 'Terlambat') {
            $pesan = "*PENGINGAT KETERLAMBATAN PENGEMBALIAN*\n"
                . "*Laboratorium Komputer — Khazlab*\n"
                . "\n"
                . "Halo *{$nama}*,\n"
                . "Peminjaman inventaris laboratorium berikut telah *MELEWATI* tanggal jatuh tempo:\n"
                . "\n"
                . "• *Perangkat:* {$barang}\n"
                . "• *Jumlah:* {$jumlah} unit\n"
                . "• *Tgl Pinjam:* {$tglPinjam}\n"
                . "• *Jatuh Tempo:* {$tglTempo}\n"
                . "\n"
                . 'Mohon segera mengembalikan perangkat tersebut ke ruang laboratorium. Terima kasih.';
        } else {
            $pesan = "*PENGINGAT JATUH TEMPO PEMINJAMAN*\n"
                . "*Laboratorium Komputer — Khazlab*\n"
                . "\n"
                . "Halo *{$nama}*,\n"
                . "Berikut pengingat jadwal pengembalian inventaris laboratorium yang sedang Anda pinjam:\n"
                . "\n"
                . "• *Perangkat:* {$barang}\n"
                . "• *Jumlah:* {$jumlah} unit\n"
                . "• *Tgl Pinjam:* {$tglPinjam}\n"
                . "• *Jatuh Tempo:* {$tglTempo}\n"
                . "\n"
                . 'Mohon mengembalikan perangkat tepat waktu sesuai jadwal. Terima kasih.';
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
