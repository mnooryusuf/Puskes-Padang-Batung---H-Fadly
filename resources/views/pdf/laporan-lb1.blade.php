<!DOCTYPE html>
<html>
<head>
    <title>Laporan LB1 (10 Besar Penyakit)</title>
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
        <h2>Laporan 10 Besar Penyakit (LB1)</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>Kode ICD-10</th>
                <th>Nama Penyakit / Diagnosa</th>
                <th>Jumlah Kasus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penyakits as $index => $penyakit)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $penyakit->kode }}</td>
                <td class="text-left">{{ $penyakit->nama_penyakit }}</td>
                <td>{{ $penyakit->total }} Kasus</td>
            </tr>
            @endforeach
            @if($penyakits->isEmpty())
            <tr>
                <td colspan="4">Tidak ada data diagnosa pada periode ini.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <div style="float: right; width: 250px; text-align: center;">
            <p>Padang Batung, {{ now()->format('d F Y') }}</p>
            <p>Kepala Puskesmas</p>
            <br><br><br><br>
            <p>( _______________________ )</p>
        </div>
    </div>
</body>
</html>
