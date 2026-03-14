<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi Tindakan Medis</title>
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
        <h2>Laporan Rekapitulasi Tindakan Medis</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->translatedFormat('d F Y') }} s/d {{ Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Tindakan</th>
                <th>Frekuensi Pelaksanaan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->nama_tindakan }}</td>
                <td>{{ $item->total }} Kali</td>
            </tr>
            @endforeach
            @if($data->isEmpty())
            <tr>
                <td colspan="3">Tidak ada data tindakan pada periode ini.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 50px;">
        <div style="float: right; width: 250px; text-align: center;">
            <p>Padang Batung, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Puskesmas</p>
            <br><br><br><br>
            <p>( _______________________ )</p>
        </div>
    </div>
</body>
</html>
