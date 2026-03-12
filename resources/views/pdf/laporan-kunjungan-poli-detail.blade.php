<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kunjungan Poli (Detail)</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
        .text-bold { font-weight: bold; }
        footer { margin-top: 30px; }
    </style>
</head>
<body>
    @php
        $poli = \App\Models\Poli::find($poliId);
        $namaPoli = $poli ? $poli->nama_poli : 'Poli';
    @endphp

    <div class="header">
        <h2 style="margin-bottom: 5px;">LAPORAN KUNJUNGAN PASIEN</h2>
        <h3 style="margin-top: 0; margin-bottom: 5px;">UNTT LAYANAN: {{ strtoupper($namaPoli) }}</h3>
        <div>PUSKESMAS PADANG BATUNG</div>
        <div style="margin-top: 5px;">Periode: {{ Carbon\Carbon::parse($start)->format('d/m/Y') }} s/d {{ Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Tanggal</th>
                <th width="40">Antrian</th>
                <th width="80">No. RM</th>
                <th>Nama Pasien</th>
                <th width="80">Jenis Pasien</th>
                <th width="80">Penjamin / Cara Bayar</th>
                <th width="100">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kunjungans as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_daftar)->format('d/m/Y') }}</td>
                <td>{{ $item->no_antrian }}</td>
                <td>{{ $item->pasien->no_rm }}</td>
                <td class="text-left font-bold">{{ $item->pasien->nama_pasien }}</td>
                <td>{{ $item->jenis_kunjungan }}</td>
                <td>{{ $item->jenis_pembayaran }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
            @if($kunjungans->isEmpty())
            <tr>
                <td colspan="8">Tidak ada data pendaftaran pada poli ini untuk periode tersebut.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 10px; font-style: italic;">
        Total Pasien: {{ $kunjungans->count() }} Orang
    </div>

    <div style="margin-top: 50px;">
        <div style="float: right; width: 250px; text-align: center;">
            <p>Padang Batung, {{ now()->format('d F Y') }}</p>
            <p>Dokter Pemeriksa,</p>
            <br><br><br><br>
            <p class="text-bold">( {{ auth()->user()->nama_lengkap }} )</p>
        </div>
    </div>
</body>
</html>
