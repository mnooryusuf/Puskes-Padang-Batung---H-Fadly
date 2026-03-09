<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold tracking-tight text-gray-950 dark:text-white">
                Pantau Antrian Real-Time
            </h2>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="text-xs text-gray-500 font-medium">Auto Update</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" wire:poll.{{ $pollingInterval }}>
            @foreach($polis as $poli)
                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 flex flex-col items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-primary-600"></div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $poli->nama_poli }}</h3>
                    <div class="text-4xl font-bold text-gray-900 dark:text-white my-2">
                        {{ $antrianSaatIni[$poli->id] }}
                    </div>
                    <p class="text-xs text-gray-400">Sedang dipanggil</p>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
