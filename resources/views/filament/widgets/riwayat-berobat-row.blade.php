<div class="px-4 py-3 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 rounded-xl">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Kunjungan: {{ $record->tanggal_daftar }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $record->poli->nama_poli }} - No. Antrian: {{ $record->no_antrian }}</p>
        </div>
        <div>
            @if($record->status === 'Selesai')
                <span class="inline-flex items-center rounded-md bg-success-50 px-2 py-1 text-xs font-medium text-success-700 ring-1 ring-inset ring-success-600/20 dark:bg-success-500/10 dark:text-success-400 dark:ring-success-500/20">Selesai</span>
            @else
                <span class="inline-flex items-center rounded-md bg-warning-50 px-2 py-1 text-xs font-medium text-warning-700 ring-1 ring-inset ring-warning-600/20 dark:bg-warning-500/10 dark:text-warning-400 dark:ring-warning-500/20">{{ $record->status }}</span>
            @endif
        </div>
    </div>

    @if($record->rekamMedis)
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800/50">
                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Diagnosis & Tindakan</h4>
                <p class="text-sm text-gray-900 dark:text-white mb-1">
                    <strong>Diagnosis:</strong> {{ $record->rekamMedis->penyakit?->nama_penyakit ?? 'Belum ada diagnosis' }}
                </p>
                <p class="text-sm text-gray-900 dark:text-white">
                    <strong>Tindakan:</strong> {{ $record->rekamMedis->tindakan ?? '-' }}
                </p>
            </div>

            <div class="rounded-lg bg-emerald-50 p-4 dark:bg-emerald-900/20 ring-1 ring-emerald-100 dark:ring-emerald-800/30">
                <div class="flex items-center gap-2 mb-2">
                    <x-heroicon-m-beaker class="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                    <h4 class="text-xs font-medium text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">E-Resep Saya</h4>
                </div>
                
                @if($record->rekamMedis->resep && $record->rekamMedis->resep->detailResep?->count() > 0)
                    <ul class="space-y-2 mt-2">
                        @foreach($record->rekamMedis->resep->detailResep as $detail)
                            <li class="text-sm flex justify-between border-b border-emerald-200/50 dark:border-emerald-700/50 pb-1 last:border-0">
                                <span class="text-gray-900 dark:text-emerald-100">{{ $detail->obat->nama_obat }}</span>
                                <span class="text-emerald-700 dark:text-emerald-300 font-medium">{{ $detail->aturan_pakai }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-emerald-600 dark:text-emerald-500 italic mt-2">Tidak ada resep obat untuk kunjungan ini.</p>
                @endif
            </div>
        </div>
    @else
        <div class="mt-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50 text-center">
            <p class="text-sm text-gray-500">Pasien belum diperiksa oleh dokter.</p>
        </div>
    @endif
</div>

