<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice {{ $invoice->nomor_invoice }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#222; }
    .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;}
    .company { text-align:left; font-size: 12px; }
    .company img { max-height:60px; margin-bottom:5px; }
    .invoice-title { text-align:right; }
    table { width:100%; border-collapse: collapse; margin-top:10px; }
    th, td { border:1px solid #ddd; padding:8px; text-align:left; }
    th { background:#f6f6f6; }
    .text-right { text-align:right; }
    .totals td { border: none; }
    .signature { margin-top:40px; display:flex; justify-content:space-between; }
  </style>
</head>
<body>
  <div class="header">
    <div class="company">
      <h3>CV SIMA GEMILANG</h3>
      <div>Jl. Durian 3 | No. 100B, Pisangan</div>
      <div>Kel. Gunung Elai, Bontang - Kalimantan Timur</div>
      <div>Telp: 085389618665</div>
      <div>Email: simagemilang02@gmail.com</div>
    </div>

    <div class="invoice-title">
      <h2>INVOICE</h2>
      <div><strong>No:</strong> {{ $invoice->nomor_invoice }}</div>
      <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($invoice->tanggal_terbit)->format('d M Y') }}</div>
      <div><strong>Status:</strong> {{ $invoice->status }}</div>
    </div>
  </div>

  <div style="margin-bottom:20px;">
    <strong>Diterbitkan Untuk:</strong><br>
    <div><strong>{{ $pengajuan->perusahaan->nama }}</strong></div>
    <div>{{ $pengajuan->perusahaan->alamat }}</div>
    <div>Tel: {{ $pengajuan->perusahaan->telepon }} | {{ $pengajuan->perusahaan->email }}</div>
  </div>

  <div style="margin-bottom:20px;">
    <strong>Tagihan Kepada:</strong><br>
    {{ $pengajuan->pembeli->name }} <br>
    @if($pengajuan->pembeli->pembeliDetail?->alamat)
      {{ $pengajuan->pembeli->pembeliDetail->alamat }} <br>
    @endif
    {{ $pengajuan->pembeli->email }}<br>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Nama Pupuk</th>
        <th>Qty</th>
        <th>Harga Satuan</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pengajuan->pengajuanDetails as $i => $detail)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $detail->pupuk->nama ?? '-' }}</td>
          <td class="text-right">{{ number_format($detail->jumlah) }} {{ $detail->pupuk?->satuan?->nama ?? '' }}</td>
          <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
          <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4" class="text-right"><strong>Total</strong></td>
        <td class="text-right"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
      </tr>
    </tfoot>
  </table>

  <div class="signature">
    <div>
      <p>Catatan: {{ $invoice->catatan ?? '-' }}</p>
    </div>
    <div style="text-align:center;">
      <div>Hormat Kami,</div>
      <div style="margin-top:60px;">( CV SIMA GEMILANG )</div>
    </div>
  </div>
</body>
</html>
