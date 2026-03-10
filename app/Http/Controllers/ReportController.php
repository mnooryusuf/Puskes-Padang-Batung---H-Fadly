<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftaran;
use App\Models\RekamMedis;
use App\Models\Obat;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function lplpo(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $obats = Obat::all()->map(function ($obat) use ($month, $year) {
            $pemakaian = DB::table('detail_resep')
                ->join('resep', 'detail_resep.resep_id', '=', 'resep.id')
                ->where('detail_resep.obat_id', $obat->id)
                ->whereMonth('resep.created_at', $month)
                ->whereYear('resep.created_at', $year)
                ->sum('detail_resep.jumlah');
            
            $obat->pemakaian_bulan_ini = $pemakaian;
            $obat->sisa_stok = $obat->stok;
            $obat->permintaan_mendatang = $pemakaian * 1.1; // Contoh estimasi 110%
            
            return $obat;
        });

        $periode = Carbon::create($year, $month)->format('F Y');
        
        $pdf = Pdf::loadView('pdf.laporan-lplpo', compact('obats', 'periode'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->stream("LPLPO-{$periode}.pdf");
    }

    public function lra(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());
        
        $data = Pembayaran::select('status_pembayaran', DB::raw('SUM(total_bayar) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status_pembayaran')
            ->get();
            
        $realisasi = [
            'BPJS' => Pembayaran::whereHas('pendaftaran', fn($q) => $q->where('jenis_pembayaran', 'BPJS'))
                ->whereBetween('created_at', [$start, $end])->sum('total_bayar'),
            'BOK' => Pembayaran::whereHas('pendaftaran', fn($q) => $q->where('jenis_pembayaran', 'BOK'))
                ->whereBetween('created_at', [$start, $end])->sum('total_bayar'),
            'Umum' => Pembayaran::whereHas('pendaftaran', fn($q) => $q->where('jenis_pembayaran', 'Umum'))
                ->whereBetween('created_at', [$start, $end])->sum('total_bayar'),
        ];

        $pdf = Pdf::loadView('pdf.laporan-lra', compact('realisasi', 'start', 'end'));
        return $pdf->stream("LRA-{$start}-to-{$end}.pdf");
    }

    public function kunjungan(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());

        $kunjungans = Pendaftaran::select('jenis_pembayaran', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal_daftar', [$start, $end])
            ->groupBy('jenis_pembayaran')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-kunjungan', compact('kunjungans', 'start', 'end'));
        return $pdf->stream("Laporan-Kunjungan.pdf");
    }

    public function lb1(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());

        $penyakits = DB::table('rekam_medis')
            ->join('penyakit', 'rekam_medis.penyakit_id', '=', 'penyakit.id')
            ->select('penyakit.nama_penyakit', 'penyakit.kode', DB::raw('COUNT(*) as total'))
            ->whereBetween('rekam_medis.created_at', [$start, $end])
            ->groupBy('rekam_medis.penyakit_id', 'penyakit.nama_penyakit', 'penyakit.kode')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-lb1', compact('penyakits', 'start', 'end'));
        return $pdf->stream("Laporan-LB1.pdf");
    }
}
