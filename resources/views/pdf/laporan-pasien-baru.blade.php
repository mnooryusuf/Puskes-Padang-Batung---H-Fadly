<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pasien Baru vs Lama</title>
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
        <h2>Laporan Analisa Pasien Baru vs Lama</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kategori Pasien</th>
                <th>Jumlah Kunjungan</th>
                <th>Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php $total = $data->sum('total'); @endphp
            @foreach($data as $item)
            <tr>
                <td>{{ $item->status_pasien == 'Baru' ? 'PASIEN BARU' : 'PASIEN LAMA' }}</td>
                <td>{{ $item->total }} Orang</td>
                <td>{{ $total > 0 ? round(($item->total / $total) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #eee;">
                <td>TOTAL</td>
                <td>{{ $total }} Orang</td>
                <td>100%</td>
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
