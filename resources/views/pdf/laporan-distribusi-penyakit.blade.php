<!DOCTYPE html>
<html>
<head>
    <title>Laporan Distribusi Penyakit (Demografi)</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Distribusi Penyakit (Berdasarkan Demografi Pasien)</h2>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div>Periode: {{ Carbon\Carbon::parse($start)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Penyakit</th>
                <th colspan="2">Jenis Kelamin</th>
                <th colspan="5">Kelompok Umur (Tahun)</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>L</th>
                <th>P</th>
                <th>0-5</th>
                <th>6-15</th>
                <th>16-45</th>
                <th>46-60</th>
                <th>60+</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $result = [];
                foreach($data as $item) {
                    $name = $item->nama_penyakit;
                    if(!isset($result[$name])) {
                        $result[$name] = ['L'=>0,'P'=>0,'u1'=>0,'u2'=>0,'u3'=>0,'u4'=>0,'u5'=>0,'total'=>0];
                    }
                    $result[$name][$item->jenis_kelamin == 'Laki-laki' ? 'L' : 'P'] += $item->total;
                    if($item->umur <= 5) $result[$name]['u1'] += $item->total;
                    elseif($item->umur <= 15) $result[$name]['u2'] += $item->total;
                    elseif($item->umur <= 45) $result[$name]['u3'] += $item->total;
                    elseif($item->umur <= 60) $result[$name]['u4'] += $item->total;
                    else $result[$name]['u5'] += $item->total;
                    $result[$name]['total'] += $item->total;
                }
                $i = 1;
            @endphp
            @foreach($result as $name => $val)
            <tr>
                <td>{{ $i++ }}</td>
                <td class="text-left">{{ $name }}</td>
                <td>{{ $val['L'] }}</td>
                <td>{{ $val['P'] }}</td>
                <td>{{ $val['u1'] }}</td>
                <td>{{ $val['u2'] }}</td>
                <td>{{ $val['u3'] }}</td>
                <td>{{ $val['u4'] }}</td>
                <td>{{ $val['u5'] }}</td>
                <td style="font-weight: bold;">{{ $val['total'] }}</td>
            </tr>
            @endforeach
            @if(empty($result))
            <tr>
                <td colspan="10">Tidak ada data diagnosa pada periode ini.</td>
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
