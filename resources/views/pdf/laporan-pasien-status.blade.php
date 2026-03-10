<!DOCTYPE html>
<html>
<head>
    <title>Laporan Status Kepulangan Pasien</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Status Kepulangan Pasien (Meninggal/Rujuk)</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Status Kepulangan</th>
                <th>Jumlah Pasien</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ strtoupper($item->status_pulang ?: 'Belum Ditentukan') }}</td>
                <td>{{ $item->total }} Orang</td>
            </tr>
            @endforeach
            @if($data->isEmpty())
            <tr>
                <td colspan="2">Tidak ada data rekam medis pada periode ini.</td>
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
