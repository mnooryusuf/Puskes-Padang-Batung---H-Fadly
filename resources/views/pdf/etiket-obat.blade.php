<!DOCTYPE html>
<html>
<head>
    <title>Etiket Obat</title>
    <style>
        @page { margin: 0; }
        body { font-family: sans-serif; margin: 0; padding: 0; width: 50mm; height: 30mm; overflow: hidden; }
        .label-container { padding: 4px; box-sizing: border-box; width: 50mm; height: 30mm; border: 0.1mm solid transparent; }
        .header { border-bottom: 0.5pt solid #000; font-weight: bold; text-align: center; font-size: 8pt; margin-bottom: 2pt; padding-bottom: 1pt; line-height: 1; }
        .content { font-size: 7.5pt; line-height: 1.2; }
        .obat { font-weight: bold; font-size: 8.5pt; margin-top: 2pt; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dosis { font-size: 11pt; font-weight: bold; text-align: center; margin: 3pt 0; border: 0.5pt dashed #000; padding: 2pt; line-height: 1; }
        .footer { font-size: 7pt; text-align: center; margin-top: 1pt; font-style: italic; }
    </style>
</head>
<body>
    @foreach($items as $detail)
    <div class="label-container" style="{{ !$loop->last ? 'page-break-after: always;' : '' }}">
        <div class="header">
            PKM PADANG BATUNG
        </div>
        <div class="content">
            <div style="font-size: 7pt;">Pasien: <strong>{{ $pasien->nama_pasien }}</strong></div>
            <div style="font-size: 7pt; margin-bottom: 1pt;">RM: {{ $pasien->no_rm }}</div>
            <div class="obat">{{ $detail->obat->nama_obat }}</div>
            <div class="dosis">{{ $detail->dosis }}</div>
            <div class="footer">Semoga Lekas Sembuh</div>
        </div>
    </div>
    @endforeach
</body>
</html>
