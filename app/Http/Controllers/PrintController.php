<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\Resep;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function cetakKartuPasien(Pasien $pasien)
    {
        $pdf = Pdf::loadView('pdf.kartu-pasien', compact('pasien'))
            ->setPaper([0, 0, 400, 250], 'portrait');
            
        return $pdf->stream("Kartu-Pasien-{$pasien->no_rm}.pdf");
    }

    public function cetakKwitansi(Pembayaran $pembayaran)
    {
        $pdf = Pdf::loadView('pdf.kwitansi', compact('pembayaran'));
        
        return $pdf->stream("Kwitansi-{$pembayaran->id}.pdf");
    }

    public function cetakEtiket(Resep $resep)
    {
        $pasien = $resep->rekamMedis->pendaftaran->pasien;
        $items = $resep->detailReseps;
        
        $pdf = Pdf::loadView('pdf.etiket-obat', compact('pasien', 'items'))
            ->setPaper([0, 0, 150, 100], 'portrait');
            
        return $pdf->stream("Etiket-{$resep->id}.pdf");
    }
}
