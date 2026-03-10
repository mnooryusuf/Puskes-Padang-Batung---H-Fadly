<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kunjungan per Poli</title>
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
        <h2>Laporan Kunjungan per Poli</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Poli / Unit Layanan</th>
                <th>Jumlah Kunjungan</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->tujuan_poli }}</td>
                <td>{{ $item->total }} Orang</td>
            </tr>
            @php $total += $item->total; @endphp
            @endforeach
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="2">TOTAL KUNJUNGAN</td>
                <td>{{ $total }} Orang</td>
            </tr>
            @if($data->isEmpty())
            <tr>
                <td colspan="3">Tidak ada data pendaftaran pada periode ini.</td>
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
