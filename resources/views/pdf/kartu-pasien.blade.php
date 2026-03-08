<!DOCTYPE html>
<html>
<head>
    <title>Kartu Pasien - {{ $pasien->no_rm }}</title>
    <style>
        body { font-family: sans-serif; }
        .card { width: 350px; border: 2px solid #00796B; padding: 20px; border-radius: 10px; }
        .header { text-align: center; border-bottom: 2px solid #00796B; padding-bottom: 10px; margin-bottom: 10px; }
        .header h2 { margin: 0; color: #00796B; font-size: 18px; }
        .info div { margin-bottom: 8px; font-size: 12px; }
        .label { font-weight: bold; color: #666; width: 80px; display: inline-block; }
        .no-rm { font-size: 20px; font-weight: bold; color: #00796B; text-align: center; margin-top: 10px; }
        .footer { text-align: center; font-size: 10px; color: #666; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>PUSKESMAS PADANG BATUNG</h2>
            <div style="font-size: 10px;">KARTU BEROBAT PASIEN</div>
        </div>
        <div class="info">
            <div><span class="label">NIK:</span> {{ $pasien->nik }}</div>
            <div><span class="label">Nama:</span> {{ $pasien->nama_pasien }}</div>
            <div><span class="label">Tgl Lahir:</span> {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d-m-Y') }}</div>
            <div><span class="label">Alamat:</span> {{ $pasien->alamat }}</div>
        </div>
        <div class="no-rm">{{ $pasien->no_rm }}</div>
        <div class="footer">Bawa kartu ini setiap kali berobat</div>
    </div>
</body>
</html>
