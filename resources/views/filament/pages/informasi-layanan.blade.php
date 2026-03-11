<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach($polis as $poli)
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-4 rounded-xl shadow-sm">
            <h3 class="text-lg font-bold text-primary-600 dark:text-primary-400 mb-2">{{ $poli->nama_poli }}</h3>
            <div class="space-y-1">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Pendaftaran:</span>
                    <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($poli->biaya_registrasi, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Konsultasi:</span>
                    <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($poli->biaya_konsultasi, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-4">Daftar Tarif Tindakan & Penunjang</h2>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
