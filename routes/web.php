<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/pengajuan/{pengajuan}/invoice.pdf', [InvoiceController::class, 'generatePdf'])->name('pengajuan.invoice.pdf');
