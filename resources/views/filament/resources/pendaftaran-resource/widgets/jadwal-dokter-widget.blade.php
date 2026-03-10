<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold">Jadwal Dokter Hari Ini</h2>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $this->getJadwalHariIni()['hari'] }}, {{ $this->getJadwalHariIni()['tanggal'] }}
            </div>
        </div>

        @php
            $dataInfo = $this->getJadwalHariIni();
        @endphp

        @if(count($dataInfo['polis']) === 0)
            <div class="p-4 text-center text-gray-500 bg-gray-50 rounded-xl dark:bg-gray-800 dark:text-gray-400">
                Tidak ada jadwal dokter untuk hari ini.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($dataInfo['polis'] as $poli)
                    @php
                        $sisaKuota = max(0, $poli['total_kuota'] - $poli['terdaftar']);
                        $isPenuh = $sisaKuota === 0;
                    @endphp
                    <div class="p-4 pl-6 border rounded-xl dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm relative overflow-hidden">
                        <!-- Indikator Warna Kiri -->
                        <div class="absolute left-0 top-0 bottom-0 w-2 {{ $isPenuh ? 'bg-danger-500' : 'bg-primary-500' }}"></div>
                        
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800 dark:text-gray-200">{{ $poli['nama_poli'] }}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $isPenuh ? 'bg-danger-100 text-danger-700 dark:bg-danger-500/10' : 'bg-primary-100 text-primary-700 dark:bg-primary-500/10' }}">
                                {{ $isPenuh ? 'Penuh' : "Sisa Kuota: {$sisaKuota}/{$poli['total_kuota']}" }}
                            </span>
                        </div>
                        
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 space-y-2">
                            @foreach($poli['dokters'] as $dokter)
                                <div class="flex items-start text-sm">
                                    <x-heroicon-m-user class="w-4 h-4 mr-2 text-gray-400 mt-0.5" />
                                    <div>
                                        <div class="font-medium text-gray-700 dark:text-gray-300">{{ $dokter['nama'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $dokter['jam'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
