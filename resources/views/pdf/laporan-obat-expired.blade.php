<!DOCTYPE html>
<html>
<head>
    <title>Laporan Obat Kadaluwarsa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
        .warning { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Obat Kadaluwarsa (Next 3 Months)</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Sediaan</th>
                <th>Tanggal Kadaluwarsa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            @php 
                $days = now()->diffInDays(Carbon\Carbon::parse($item->expired_at), false);
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->nama_obat }}</td>
                <td>{{ $item->sediaan }}</td>
                <td>{{ Carbon\Carbon::parse($item->expired_at)->format('d/m/Y') }}</td>
                <td class="{{ $days < 0 ? 'warning' : '' }}">
                    {{ $days < 0 ? 'EXPIRED' : ($days == 0 ? 'HARI INI' : $days . ' Hari Lagi') }}
                </td>
            </tr>
            @endforeach
            @if($data->isEmpty())
            <tr>
                <td colspan="5">Tidak ada obat yang akan kadaluwarsa dalam 3 bulan ke depan.</td>
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
