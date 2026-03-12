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
        
        $data = Pembayaran::select(['status_pembayaran', DB::raw('SUM(total_bayar) as total')])
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
        $poliId = $request->input('poli_id');

        $query = Pendaftaran::query()->with('pasien')
            ->whereBetween('tanggal_daftar', [$start, $end]);

        if ($poliId) {
            $query->where('poli_id', $poliId);
            $kunjungans = $query->orderBy('tanggal_daftar', 'asc')->get();
            $view = 'pdf.laporan-kunjungan-poli-detail'; // We'll create this specific view
        } else {
            $kunjungans = $query->select(['jenis_pembayaran', DB::raw('COUNT(*) as total')])
                ->groupBy('jenis_pembayaran')
                ->get();
            $view = 'pdf.laporan-kunjungan';
        }

        $pdf = Pdf::loadView($view, compact('kunjungans', 'start', 'end', 'poliId'));

        if ($poliId) {
            $pdf->setPaper('a4', 'landscape');
        }

        return $pdf->stream("Laporan-Kunjungan.pdf");
    }

    public function lb1(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());
        $poliId = $request->input('poli_id');

        $penyakits = DB::table('rekam_medis')
            ->join('penyakit', 'rekam_medis.penyakit_id', '=', 'penyakit.id')
            ->join('pendaftaran', 'rekam_medis.pendaftaran_id', '=', 'pendaftaran.id')
            ->select('penyakit.nama_penyakit', 'penyakit.kode', DB::raw('COUNT(*) as total'))
            ->whereBetween('rekam_medis.created_at', [$start, $end])
            ->when($poliId, fn($q) => $q->where('pendaftaran.poli_id', $poliId))
            ->groupBy('rekam_medis.penyakit_id', 'penyakit.nama_penyakit', 'penyakit.kode')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-lb1', compact('penyakits', 'start', 'end', 'poliId'));
        return $pdf->stream("Laporan-LB1.pdf");
    }

    public function kunjunganPoli(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());

        $data = DB::table('pendaftaran')
            ->join('poli', 'pendaftaran.poli_id', '=', 'poli.id')
            ->select('poli.nama_poli as tujuan_poli', DB::raw('count(*) as total'))
            ->whereBetween('pendaftaran.created_at', [$start, $end])
            ->groupBy('poli.id', 'poli.nama_poli')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-kunjungan-poli', compact('data', 'start', 'end'));
        return $pdf->stream("Laporan-Kunjungan-Poli.pdf");
    }

    public function kunjunganPasienBaru(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());

        $data = DB::table('pendaftaran')
            ->select('jenis_kunjungan as status_pasien', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('jenis_kunjungan')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-pasien-baru', compact('data', 'start', 'end'));
        return $pdf->stream("Laporan-Kunjungan-Pasien-Baru.pdf");
    }

    public function rekapTindakan(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());
        $poliId = $request->input('poli_id');

        $data = DB::table('rekam_medis_tindakan')
            ->join('tindakans', 'rekam_medis_tindakan.tindakan_id', '=', 'tindakans.id')
            ->join('rekam_medis', 'rekam_medis_tindakan.rekam_medis_id', '=', 'rekam_medis.id')
            ->join('pendaftaran', 'rekam_medis.pendaftaran_id', '=', 'pendaftaran.id')
            ->select('tindakans.nama_tindakan', DB::raw('count(*) as total'))
            ->whereBetween('rekam_medis.created_at', [$start, $end])
            ->when($poliId, fn($q) => $q->where('pendaftaran.poli_id', $poliId))
            ->groupBy('tindakans.id', 'tindakans.nama_tindakan')
            ->orderBy('total', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-rekap-tindakan', compact('data', 'start', 'end', 'poliId'));
        return $pdf->stream("Laporan-Rekap-Tindakan.pdf");
    }

    public function statistikLab(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());
        $poliId = $request->input('poli_id');

        $data = DB::table('rekam_medis')
            ->join('pendaftaran', 'rekam_medis.pendaftaran_id', '=', 'pendaftaran.id')
            ->whereNotNull('instruksi_lab')
            ->whereBetween('rekam_medis.created_at', [$start, $end])
            ->when($poliId, fn($q) => $q->where('pendaftaran.poli_id', $poliId))
            ->select('instruksi_lab', DB::raw('count(*) as total'))
            ->groupBy('instruksi_lab')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-statistik-lab', compact('data', 'start', 'end', 'poliId'));
        return $pdf->stream("Laporan-Statistik-Lab.pdf");
    }

    public function pasienStatus(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());

        $data = DB::table('rekam_medis')
            ->select('status_pulang', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status_pulang')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-pasien-status', compact('data', 'start', 'end'));
        return $pdf->stream("Laporan-Status-Pasien.pdf");
    }

    public function obatExpired(Request $request)
    {
        $data = DB::table('obat')
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now()->addMonths(3))
            ->orderBy('expired_at', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-obat-expired', compact('data'));
        return $pdf->stream("Laporan-Obat-Expired.pdf");
    }

    public function obatAnalisa(Request $request)
    {
        // Analisa pemakaian obat 3 bulan terakhir untuk menentukan fast/slow moving
        $start = now()->subMonths(3)->startOfMonth();
        $end = now()->endOfMonth();

        $data = DB::table('obat')
            ->leftJoin('detail_resep', 'obat.id', '=', 'detail_resep.obat_id')
            ->select('obat.nama_obat', 'obat.satuan', DB::raw('SUM(detail_resep.jumlah) as total_pakai'))
            ->groupBy('obat.id', 'obat.nama_obat', 'obat.satuan')
            ->orderBy('total_pakai', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-obat-analisa', compact('data', 'start', 'end'));
        return $pdf->stream("Laporan-Analisa-Obat.pdf");
    }

    public function pendapatanHarian(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());

        $data = DB::table('pembayaran')
            ->select('metode_pembayaran', 'status_pembayaran', DB::raw('SUM(total_bayar) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('metode_pembayaran', 'status_pembayaran')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-pendapatan', compact('data', 'start', 'end'));
        return $pdf->stream("Laporan-Pendapatan.pdf");
    }

    public function distribusiPenyakit(Request $request)
    {
        $start = $request->input('start_date', now()->startOfMonth());
        $end = $request->input('end_date', now()->endOfMonth());

        $data = DB::table('rekam_medis')
            ->join('penyakit', 'rekam_medis.penyakit_id', '=', 'penyakit.id')
            ->join('pendaftaran', 'rekam_medis.pendaftaran_id', '=', 'pendaftaran.id')
            ->join('pasien', 'pendaftaran.pasien_id', '=', 'pasien.id')
            ->select(
                'penyakit.nama_penyakit',
                'pasien.jenis_kelamin',
                DB::raw('TIMESTAMPDIFF(YEAR, pasien.tanggal_lahir, rekam_medis.created_at) as umur'),
                DB::raw('count(*) as total')
            )
            ->whereBetween('rekam_medis.created_at', [$start, $end])
            ->groupBy('penyakit.id', 'penyakit.nama_penyakit', 'pasien.jenis_kelamin', 'umur')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-distribusi-penyakit', compact('data', 'start', 'end'));
        return $pdf->stream("Laporan-Distribusi-Penyakit.pdf");
    }
}
