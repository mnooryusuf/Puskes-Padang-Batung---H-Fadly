<div class="px-4 py-3 bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 rounded-xl space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Resep untuk Kunjungan: {{ $record->tanggal_daftar }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pasien: {{ $record->pasien->nama_pasien ?? '' }} | No. Rekam Medis: {{ $record->pasien->no_rm ?? '' }}</p>
        </div>
        <div>
            <span class="inline-flex items-center rounded-md bg-info-50 px-2 py-1 text-xs font-medium text-info-700 ring-1 ring-inset ring-info-600/20 dark:bg-info-500/10 dark:text-info-400 dark:ring-info-500/20">
                Poli {{ $record->poli->nama_poli }}
            </span>
        </div>
    </div>

    @if($record->rekamMedis && $record->rekamMedis->resep && $record->rekamMedis->resep->detailReseps?->count() > 0)
        <div class="rounded-lg bg-emerald-50 p-4 dark:bg-emerald-900/20 ring-1 ring-emerald-100 dark:ring-emerald-800/30">
            <div class="flex items-center gap-2 mb-3">
                <x-heroicon-m-beaker class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                <h4 class="text-sm font-semibold text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">Daftar Obat (E-Resep)</h4>
            </div>
            
            <ul class="space-y-2 mt-2">
                @foreach($record->rekamMedis->resep->detailReseps as $detail)
                    <li class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-md shadow-sm border border-emerald-100 dark:border-emerald-800">
                        <div>
                            <span class="block text-sm font-bold text-gray-900 dark:text-emerald-100">{{ $detail->obat->nama_obat }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">Aturan Pakai: {{ $detail->dosis }}</span>
                            @if($detail->obat_pengganti_id)
                                <span class="block text-xs text-amber-600 dark:text-amber-400 mt-1">
                                    🔄 Diganti: <strong>{{ $detail->obatPengganti->nama_obat }}</strong>
                                </span>
                            @endif
                            @if($detail->jumlah_diserahkan !== null && $detail->jumlah_diserahkan != $detail->jumlah)
                                <span class="block text-xs text-blue-600 dark:text-blue-400 mt-1">
                                    📝 Jumlah disesuaikan: {{ $detail->jumlah }} → {{ $detail->jumlah_diserahkan }}
                                </span>
                            @endif
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="px-2 py-1 rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-100 text-xs font-medium">Satuan: {{ $detail->obat->satuan ?? '-' }}</span>
                            <span class="mt-1 text-sm font-bold text-emerald-700 dark:text-emerald-400">Jml: {{ $detail->jumlah_diserahkan ?? $detail->jumlah }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Catatan Farmasi --}}
        @if($record->rekamMedis->resep->catatan_farmasi)
        <div class="mt-3 text-sm p-3 bg-amber-50 dark:bg-amber-900/20 rounded border border-amber-200 dark:border-amber-700">
            <strong class="text-amber-700 dark:text-amber-300">💊 Catatan Farmasi:</strong>
            <p class="text-amber-600 dark:text-amber-400 mt-1">{{ $record->rekamMedis->resep->catatan_farmasi }}</p>
        </div>
        @endif

        {{-- Keterangan Resep --}}
        @if($record->rekamMedis->resep->keterangan)
        <div class="mt-3 text-sm p-3 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700">
            <strong class="text-gray-700 dark:text-gray-300">Catatan Tambahan:</strong>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $record->rekamMedis->resep->keterangan }}</p>
        </div>
        @endif
    @else
        <div class="mt-4 p-6 rounded-lg bg-gray-50 dark:bg-gray-800/50 text-center border-2 border-dashed border-gray-300 dark:border-gray-700">
            <x-heroicon-o-document-text class="w-8 h-8 mx-auto text-gray-400 mb-2"/>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum Ada E-Resep</p>
            <p class="text-xs text-gray-500 mt-1">Dokter belum memasukkan data resep untuk pasien ini, atau resep kosong.</p>
        </div>
    @endif
</div>
