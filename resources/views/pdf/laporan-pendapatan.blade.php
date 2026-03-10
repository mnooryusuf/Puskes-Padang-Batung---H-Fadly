<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi Pendapatan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Rekapitulasi Pendapatan Kasir</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th>Status / Kategori</th>
                <th>Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($data as $item)
            <tr>
                <td>{{ strtoupper($item->metode_pembayaran) }}</td>
                <td>{{ $item->status_pembayaran }}</td>
                <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @php $grandTotal += $item->total; @endphp
            @endforeach
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="2">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
            @if($data->isEmpty())
            <tr>
                <td colspan="3">Tidak ada data transaksi pada periode ini.</td>
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
