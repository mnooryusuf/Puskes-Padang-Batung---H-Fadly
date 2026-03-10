<!DOCTYPE html>
<html>
<head>
    <title>Laporan Realisasi Anggaran (LRA)</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 10px; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total { font-weight: bold; background-color: #eee; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Realisasi Anggaran (LRA)</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Sumber Anggaran / Kategori</th>
                <th>Total Realisasi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Dana JKN (BPJS)</td>
                <td class="text-right">Rp {{ number_format($realisasi['BPJS'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Dana BOK (Bantuan Operasional Kesehatan)</td>
                <td class="text-right">Rp {{ number_format($realisasi['BOK'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Pendapatan Umum / Mandiri</td>
                <td class="text-right">Rp {{ number_format($realisasi['Umum'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total">
                <td colspan="2" style="text-align: center;">TOTAL REALISASI</td>
                <td class="text-right">Rp {{ number_format(array_sum($realisasi), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <div style="float: right; width: 250px; text-align: center;">
            <p>Padang Batung, {{ now()->format('d F Y') }}</p>
            <p>Bendahara Penerimaan / JKN</p>
            <br><br><br><br>
            <p>( _______________________ )</p>
        </div>
    </div>
</body>
</html>
