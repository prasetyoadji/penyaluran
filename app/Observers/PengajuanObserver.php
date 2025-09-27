<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Pengajuan;

class PengajuanObserver
{
    /**
     * Handle the Pengajuan "created" event.
     */
    public function created(Pengajuan $pengajuan): void
    {
        // Buat transaksi dulu
        $transaksi = $pengajuan->transaksis()->create([
            'nomor_transaksi' => 'TRX-' . now()->format('YmdHis'),
            'tanggal_transaksi' => now(),
            'total_harga' => $pengajuan->grand_total ?? 0,
            'perusahaan_id' => $pengajuan->perusahaan_id,
        ]);

        // Setelah transaksi dibuat, buat invoice
        $transaksi->invoices()->create([
            'nomor_invoice' => 'INV-' . now()->format('YmdHis'),
            'status' => 'Belum Lunas',
            'tanggal_terbit' => now(),
            'perusahaan_id' => $pengajuan->perusahaan_id,
        ]);
    }

    /**
     * Handle the Pengajuan "updated" event.
     */
    public function updated(Pengajuan $pengajuan): void
    {
        // Kalau status berubah jadi "Disetujui", buat invoice
        if ($pengajuan->isDirty('status') && $pengajuan->status === 'Disetujui') {
            // Cek apakah sudah ada invoice
            if (!$pengajuan->invoice) {
                Invoice::create([
                    'pengajuan_id' => $pengajuan->id,
                    'transaksi_id' => $pengajuan->transaksis()->latest()->first()?->id, // ambil transaksi terakhir
                    'perusahaan_id' => $pengajuan->perusahaan_id,
                    'total' => $pengajuan->grand_total ?? 0,
                    'status' => 'Belum Lunas',
                    'tanggal_invoice' => now(),
                    'nomor_invoice' => 'INV-' . now()->format('YmdHis'),
                ]);
            }
        }
    }

    /**
     * Handle the Pengajuan "deleted" event.
     */
    public function deleted(Pengajuan $pengajuan): void
    {
        //
    }

    /**
     * Handle the Pengajuan "restored" event.
     */
    public function restored(Pengajuan $pengajuan): void
    {
        //
    }

    /**
     * Handle the Pengajuan "force deleted" event.
     */
    public function forceDeleted(Pengajuan $pengajuan): void
    {
        //
    }
}
