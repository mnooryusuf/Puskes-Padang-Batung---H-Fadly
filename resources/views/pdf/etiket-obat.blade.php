<!DOCTYPE html>
<html>
<head>
    <title>Etiket Obat</title>
    <style>
        @page { size: 5cm 3cm; margin: 0; }
        body { font-family: sans-serif; padding: 5px; font-size: 10px; }
        .header { border-bottom: 1px solid #000; font-weight: bold; text-align: center; margin-bottom: 3px; }
        .content { margin-top: 5px; }
        .obat { font-weight: bold; font-size: 11px; margin-bottom: 2px; }
        .dosis { font-size: 14px; font-weight: bold; text-align: center; margin: 5px 0; border: 1px dashed #666; padding: 3px; }
        .footer { font-size: 8px; text-align: center; margin-top: 5px; }
    </style>
</head>
<body>
    @foreach($items as $detail)
    <div style="page-break-after: always;">
        <div class="header">
            APOTEK PUSKESMAS PADANG BATUNG
        </div>
        <div class="content">
            <div>Pasien: <strong>{{ $pasien->nama_pasien }}</strong> ({{ $pasien->no_rm }})</div>
            <div class="obat">{{ $detail->obat->nama_obat }}</div>
            <div class="dosis">{{ $detail->dosis }}</div>
            <div class="footer">Semoga Lekas Sembuh</div>
        </div>
    </div>
    @endforeach
</body>
</html>
