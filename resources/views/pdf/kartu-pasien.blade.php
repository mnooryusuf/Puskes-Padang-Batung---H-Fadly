<!DOCTYPE html>
<html>
<head>
    <title>Kartu Pasien - {{ $pasien->no_rm }}</title>
    <style>
        @page { margin: 0; }
        html, body { 
            margin: 0; 
            padding: 0; 
            width: 100%;
            height: 100%;
        }
        body { font-family: sans-serif; }
        .card { 
            width: 360px; 
            height: 210px;
            border: 2px solid #00796B; 
            padding: 15px; 
            border-radius: 10px;
            margin: 10px auto;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }
        .header { text-align: center; border-bottom: 2px solid #00796B; padding-bottom: 8px; margin-bottom: 8px; }
        .header h2 { margin: 0; color: #00796B; font-size: 16px; text-transform: uppercase; }
        .info div { margin-bottom: 5px; font-size: 11px; line-height: 1.2; }
        .label { font-weight: bold; color: #666; width: 70px; display: inline-block; }
        .no-rm { font-size: 18px; font-weight: bold; color: #00796B; text-align: center; margin-top: 8px; }
        .footer { text-align: center; font-size: 9px; color: #666; margin-top: 10px; }
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
