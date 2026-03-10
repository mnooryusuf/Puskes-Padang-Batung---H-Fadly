<!DOCTYPE html>
<html>
<head>
    <title>Laporan Analisa Perputaran Obat</title>
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
        <h2>Laporan Analisa Perputaran Obat (Fast/Slow Moving)</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Analisa berdasarkan pemakaian resep 3 bulan terakhir.</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Obat</th>
                <th>Satuan</th>
                <th>Total Terpakai (3 Bln)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $avg = $data->avg('total_pakai'); @endphp
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->nama_obat }}</td>
                <td>{{ $item->satuan }}</td>
                <td>{{ $item->total_pakai ?: 0 }}</td>
                <td>
                    @if($item->total_pakai > $avg)
                        <span style="color: green; font-weight: bold;">Fast Moving</span>
                    @elseif($item->total_pakai > 0)
                        <span>Medium</span>
                    @else
                        <span style="color: gray;">Slow/Dead Moving</span>
                    @endif
                </td>
            </tr>
            @endforeach
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
