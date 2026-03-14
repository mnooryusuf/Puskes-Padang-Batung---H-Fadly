<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kunjungan Pasien</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Statistik Kunjungan Pasien</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->translatedFormat('d F Y') }} s/d {{ Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kategori / Cara Bayar</th>
                <th>Jumlah Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($kunjungans as $index => $kunjungan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">
                    @if($kunjungan->jenis_pembayaran == 'BOK') Bantuan Operasional Kesehatan (BOK)
                    @elseif($kunjungan->jenis_pembayaran == 'BPJS') Jaminan Kesehatan (BPJS/JKN)
                    @elseif($kunjungan->jenis_pembayaran == 'Umum') Pasien Umum / Mandiri
                    @else {{ $kunjungan->jenis_pembayaran }}
                    @endif
                </td>
                <td>{{ $kunjungan->total }} Pasien</td>
            </tr>
            @php $total += $kunjungan->total; @endphp
            @endforeach
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="2">TOTAL KUNJUNGAN</td>
                <td>{{ $total }} Pasien</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <div style="float: right; width: 250px; text-align: center;">
            <p>Padang Batung, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Petugas Pendaftaran / Rekam Medis</p>
            <br><br><br><br>
            <p>( _______________________ )</p>
        </div>
    </div>
</body>
</html>
