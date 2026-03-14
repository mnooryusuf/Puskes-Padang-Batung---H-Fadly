<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi Pembayaran - {{ $pembayaran->pendaftaran->pasien->nama_pasien }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .container { width: 100%; max-width: 600px; margin: auto; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border-bottom: 1px solid #eee; padding: 10px; text-align: left; }
        .total { font-size: 16px; font-weight: bold; text-align: right; margin-top: 20px; }
        .footer { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">KWITANSI PEMBAYARAN</div>
            <div>PUSKESMAS PADANG BATUNG</div>
        </div>
        
        <table style="width: 100%">
            <tr>
                <td><strong>No. Transaksi:</strong> {{ $pembayaran->id }}</td>
                <td style="text-align: right"><strong>Tanggal:</strong> {{ $pembayaran->created_at->translatedFormat('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Nama Pasien:</strong> {{ $pembayaran->pendaftaran->pasien->nama_pasien }}</td>
                <td style="text-align: right"><strong>No. RM:</strong> {{ $pembayaran->pendaftaran->pasien->no_rm }}</td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Deskripsi Layanan</th>
                    <th style="text-align: right">Biaya</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Biaya Pendaftaran</td>
                    <td style="text-align: right">Rp {{ number_format($pembayaran->biaya_pendaftaran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Konsultasi</td>
                    <td style="text-align: right">Rp {{ number_format($pembayaran->biaya_konsultasi, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Obat</td>
                    <td style="text-align: right">Rp {{ number_format($pembayaran->biaya_obat, 0, ',', '.') }}</td>
                </tr>
                @if($pembayaran->biaya_tindakan > 0)
                <tr>
                    <td>Biaya Tindakan</td>
                    <td style="text-align: right">Rp {{ number_format($pembayaran->biaya_tindakan, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($pembayaran->biaya_tambahan > 0)
                <tr>
                    <td>Biaya Tambahan</td>
                    <td style="text-align: right">Rp {{ number_format($pembayaran->biaya_tambahan, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="total">TOTAL BAYAR: Rp {{ number_format($pembayaran->total_bayar, 0, ',', '.') }}</div>

        <div class="footer">
            <p>Admin Kasir,</p>
            <br><br>
            <p>( ........................ )</p>
        </div>
    </div>
</body>
</html>
