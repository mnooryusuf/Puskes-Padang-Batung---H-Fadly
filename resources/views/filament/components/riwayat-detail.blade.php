<div class="space-y-6 py-4">
    {{-- Header Info --}}
    <div class="flex items-center justify-between border-b pb-4 border-gray-100 dark:border-gray-800">
        <div>
            <h4 class="text-xs font-semibold text-gray-500 uppercase">Dokter Pemeriksa</h4>
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $record->dokter?->nama_dokter ?: 'N/A' }}</p>
        </div>
        <div class="text-right">
            <h4 class="text-xs font-semibold text-gray-500 uppercase">Status Pulang</h4>
            <p class="text-sm font-medium {{ $record->status_pulang == 'Rujuk' ? 'text-amber-500' : 'text-green-600' }} uppercase">
                {{ $record->status_pulang ?: 'Selesai' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Tanda Vital --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                 <x-heroicon-m-heart class="w-4 h-4 text-rose-500" />
                 Tanda Vital
            </h4>
            <div class="grid grid-cols-2 gap-y-3 text-sm">
                <span class="text-gray-600 dark:text-gray-400">Tekanan Darah:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $record->tekanan_darah ?: '-' }}</span>
                
                <span class="text-gray-600 dark:text-gray-400">Berat Badan:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $record->berat_badan ? $record->berat_badan . ' kg' : '-' }}</span>
                
                <span class="text-gray-600 dark:text-gray-400">Suhu:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $record->suhu_tubuh ? $record->suhu_tubuh . ' °C' : '-' }}</span>
                
                <span class="text-gray-600 dark:text-gray-400">Nadi:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $record->nadi ? $record->nadi . ' x/menit' : '-' }}</span>

                <span class="text-gray-600 dark:text-gray-400">Respirasi:</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ $record->respirasi ? $record->respirasi . ' x/menit' : '-' }}</span>
            </div>
        </div>

        {{-- Pemeriksaan & Riwayat --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                 <x-heroicon-m-document-text class="w-4 h-4 text-blue-500" />
                 Detail Pemeriksaan
            </h4>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-xs text-gray-500">Keluhan Utama:</span>
                    <p class="text-gray-900 dark:text-white">{{ $record->keluhan_utama ?: '-' }}</p>
                </div>
                @if($record->riwayat_penyakit_sekarang)
                <div>
                    <span class="block text-xs text-gray-500">Riwayat Penyakit Sekarang:</span>
                    <p class="text-gray-900 dark:text-white">{{ $record->riwayat_penyakit_sekarang }}</p>
                </div>
                @endif
                @if($record->riwayat_alergi)
                <div class="text-rose-600 dark:text-rose-400">
                    <span class="block text-xs opacity-75">Riwayat Alergi:</span>
                    <p class="font-medium underline decoration-rose-300">{{ $record->riwayat_alergi }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Diagnosa & Tindakan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-4 border border-blue-100 dark:border-blue-900/30 rounded-xl">
            <h4 class="text-sm font-bold text-blue-700 dark:text-blue-400 mb-3">Diagnosis ICD-10</h4>
            <div class="text-sm">
                <p class="font-bold text-gray-900 dark:text-white">
                    {{ $record->penyakit ? $record->penyakit->kode . ' - ' . $record->penyakit->nama_penyakit : '-' }}
                </p>
                @if($record->diagnosa)
                <p class="mt-2 text-gray-600 dark:text-gray-400 italic">"{{ $record->diagnosa }}"</p>
                @endif
                <span class="inline-block mt-2 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 text-[10px] rounded uppercase">
                    {{ $record->tipe_diagnosis ?: 'Primer' }}
                </span>
            </div>
        </div>

        <div class="p-4 border border-purple-100 dark:border-purple-900/30 rounded-xl">
            <h4 class="text-sm font-bold text-purple-700 dark:text-purple-400 mb-3">Tindakan Medis</h4>
            <div class="text-sm">
                @if($record->tindakans->count() > 0)
                    <ul class="list-disc list-inside space-y-1 text-gray-900 dark:text-white">
                        @foreach($record->tindakans as $t)
                            <li>{{ $t->nama_tindakan }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 italic">Tidak ada tindakan medis spesifik</p>
                @endif

                @if($record->tindakan)
                    <p class="mt-2 text-xs text-gray-500">Catatan: {{ $record->tindakan }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Laboratorium --}}
    @if($record->instruksi_lab)
    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/50">
        <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300 flex items-center gap-2 mb-2">
            <x-heroicon-m-beaker class="w-4 h-4" />
            Pemeriksaan Penunjang (Lab)
        </h4>
        <p class="text-sm text-amber-900 dark:text-amber-200 whitespace-pre-line leading-relaxed">
            {{ $record->instruksi_lab }}
        </p>
    </div>
    @endif

    {{-- Rujukan --}}
    @if($record->status_pulang == 'Rujuk')
    <div class="p-4 bg-rose-50 dark:bg-rose-900/20 rounded-xl border border-rose-100 dark:border-rose-800/50">
        <h4 class="text-sm font-bold text-rose-800 dark:text-rose-300 flex items-center gap-2 mb-2">
            <x-heroicon-m-arrow-top-right-on-square class="w-4 h-4" />
            Informasi Rujukan
        </h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="block text-xs text-rose-500 uppercase font-semibold">RS Tujuan:</span>
                <p class="text-rose-900 dark:text-rose-200 font-medium">{{ $record->rs_tujuan ?: '-' }}</p>
            </div>
            <div>
                <span class="block text-xs text-rose-500 uppercase font-semibold">Alasan Rujuk:</span>
                <p class="text-rose-900 dark:text-rose-200 font-medium">{{ $record->alasan_rujuk ?: '-' }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
