<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Kartu Pasien Digital --}}
        @php
            $pasien = auth()->user()?->pasien;
        @endphp

        @if($pasien)
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="flex-shrink-0">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-500/20">
                            <x-heroicon-o-user class="h-8 w-8 text-primary-600 dark:text-primary-400" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-950 dark:text-white">
                            {{ $pasien->nama_pasien }}
                        </h3>
                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                            <span>No. RM: <strong>{{ $pasien->no_rm }}</strong></span>
                            <span>NIK: {{ $pasien->nik }}</span>
                            <span>{{ $pasien->tempat_lahir }}, {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d/m/Y') }}</span>
                            <span>{{ $pasien->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg shrink-0">
                    <p class="text-xs text-gray-500 mb-2 font-semibold uppercase tracking-wider">Kartu Digital Pasien</p>
                    @php
                        $generator = new Picqer\Barcode\BarcodeGeneratorSVG();
                        $barcode = $generator->getBarcode($pasien->no_rm, $generator::TYPE_CODE_128, 2, 40, 'black');
                    @endphp
                    <div class="bg-white p-2 rounded w-full flex justify-center overflow-hidden">
                        {!! str_replace('black', 'currentColor', $barcode) !!}
                    </div>
                    <p class="text-xs font-mono mt-1 text-gray-700 dark:text-gray-300">{{ $pasien->no_rm }}</p>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-filament-panels::page>
