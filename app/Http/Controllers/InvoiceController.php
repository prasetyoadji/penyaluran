<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function generatePdf(Pengajuan $pengajuan, Request $request)
    {
        // Hitung total dari pengajuanDetails (fallback jika grand_total tidak disimpan)
        $grandTotal = $pengajuan->pengajuanDetails->sum('subtotal');

        // Pastikan ada transaksi untuk pengajuan — buat jika belum ada
        $transaksi = $pengajuan->transaksis()->latest()->first();
        if (!$transaksi) {
            $transaksi = $pengajuan->transaksis()->create([
                'nomor_transaksi' => 'TRX-' . now()->format('YmdHis'),
                'tanggal_transaksi' => now(),
                'total_harga' => $grandTotal,
                'perusahaan_id' => $pengajuan->perusahaan_id,
            ]);
        }

        // Pastikan ada invoice untuk transaksi — buat jika belum ada
        $invoice = $transaksi->invoices()->latest()->first();
        if (!$invoice) {
            $invoice = $transaksi->invoices()->create([
                'nomor_invoice' => 'INV-' . now()->format('YmdHis'),
                'tanggal_terbit' => now(),
                'status' => 'Belum Lunas',
                'perusahaan_id' => $pengajuan->perusahaan_id,
            ]);
        }

        // Load relations needed in view
        $pengajuan->load('pembeli', 'perusahaan', 'pengajuanDetails.pupuk');
        $transaksi->load('pembayarans');

        // Data untuk blade
        $data = [
            'pengajuan' => $pengajuan,
            'transaksi' => $transaksi,
            'invoice' => $invoice,
            'grandTotal' => $grandTotal,
        ];

        $pdf = Pdf::loadView('invoices.show', $data)
            ->setPaper('a4', 'portrait');

        // Jika mau download: ?download=1
        // if ($request->query('download')) {
        //     return $pdf->download("invoice-{$invoice->nomor_invoice}.pdf");
        // }

        // Stream ke browser
        return $pdf->stream("invoice-{$invoice->nomor_invoice}.pdf");
    }
}