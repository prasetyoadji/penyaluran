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
            'status' => 'Belum Bayar',
            'tanggal_terbit' => now(),
            'perusahaan_id' => $pengajuan->perusahaan_id,
        ]);
    }

    /**
     * Handle the Pengajuan "updated" event.
     */
    public function updated(Pengajuan $pengajuan): void
    {
        if ($pengajuan->isDirty('status') && $pengajuan->status === 'Disetujui') {
            // Cek apakah sudah ada invoice
            $transaksi = $pengajuan->transaksis()->latest()->first();

            if ($transaksi && $transaksi->invoices()->count() === 0) {
                $transaksi->invoices()->create([
                    'nomor_invoice' => 'INV-' . now()->format('YmdHis'),
                    'status' => 'Selesai',
                    'tanggal_terbit' => now(), // ✅ pakai tanggal_terbit
                    'perusahaan_id' => $pengajuan->perusahaan_id,
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