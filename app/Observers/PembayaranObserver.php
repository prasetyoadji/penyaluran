<?php

namespace App\Observers;

use App\Models\Pembayaran;
use App\Models\Invoice;

class PembayaranObserver
{
    public function created(Pembayaran $pembayaran): void
    {
        $transaksi = $pembayaran->transaksi;

        if (!$transaksi) {
            return;
        }

        // Kalau transaksi belum punya invoice → buat otomatis
        if (!$transaksi->invoice) {
            Invoice::create([
                'transaksi_id' => $transaksi->id,
                'nomor_invoice' => 'INV-' . now()->format('YmdHis'),
                'tanggal_terbit' => now(),
                'catatan' => 'Invoice otomatis dari pembayaran pertama.',
                'status' => 'Diterbitkan',
                'perusahaan_id' => $transaksi->perusahaan_id,
            ]);
        }
    }

    /**
     * Handle the Pembayaran "updated" event.
     */
    public function updated(Pembayaran $pembayaran): void
    {
        $transaksi = $pembayaran->transaksi;

        if ($transaksi && $transaksi->invoice) {
            // Jika total pembayaran >= total transaksi, update status invoice ke Lunas
            $totalBayar = $transaksi->pembayarans()->sum('jumlah_bayar');

            if ($totalBayar >= $transaksi->total_harga) {
                $transaksi->invoice->update([
                    'status' => 'Lunas',
                ]);
            }
        }
    }

    /**
     * Handle the Pembayaran "deleted" event.
     */
    public function deleted(Pembayaran $pembayaran): void
    {
        //
    }

    /**
     * Handle the Pembayaran "restored" event.
     */
    public function restored(Pembayaran $pembayaran): void
    {
        //
    }

    /**
     * Handle the Pembayaran "force deleted" event.
     */
    public function forceDeleted(Pembayaran $pembayaran): void
    {
        //
    }
}