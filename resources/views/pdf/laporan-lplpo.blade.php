<!DOCTYPE html>
<html>
<head>
    <title>Laporan LPLPO - {{ $periode }}</title>
    <style>
        @page { margin: 20px; }
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
        .footer { margin-top: 30px; }
        .signature { float: right; width: 200px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LPLPO (Laporan Pemakaian dan Lembar Permintaan Obat)</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ $periode }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Obat / Sediaan</th>
                <th rowspan="2">Satuan</th>
                <th colspan="2">Stok & Pemakaian</th>
                <th rowspan="2">Estimasi Permintaan Bulan Depan</th>
            </tr>
            <tr>
                <th>Sisa Stok</th>
                <th>Pemakaian Bulan Ini</th>
            </tr>
        </thead>
        <tbody>
            @foreach($obats as $index => $obat)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $obat->nama_obat }} ({{ $obat->sediaan }})</td>
                <td>{{ $obat->satuan }}</td>
                <td>{{ $obat->stok }}</td>
                <td>{{ $obat->pemakaian_bulan_ini }}</td>
                <td>{{ round($obat->permintaan_mendatang) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Padang Batung, {{ now()->format('d F Y') }}</p>
            <p>Kepala Farmasi / Apoteker</p>
            <br><br><br>
            <p>( _______________________ )</p>
        </div>
    </div>
</body>
</html>
