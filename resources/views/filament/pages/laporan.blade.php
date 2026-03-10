<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        {{-- Card LPLPO --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-600 dark:text-blue-300">
                    <x-heroicon-o-beaker class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">LPLPO (Farmasi)</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Laporan Pemakaian dan Lembar Permintaan Obat bulanan.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_lplpo') }}
            </div>
        </div>

        {{-- Card LRA --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg text-green-600 dark:text-green-300">
                    <x-heroicon-o-banknotes class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">LRA (Keuangan)</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Laporan Realisasi Anggaran JKN, BOK, dan Umum.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_lra') }}
            </div>
        </div>

        {{-- Card Kunjungan --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg text-indigo-600 dark:text-indigo-300">
                    <x-heroicon-o-user-group class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Statistik Kunjungan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Analisa jumlah kunjungan berdasarkan kategori bayar.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_kunjungan') }}
            </div>
        </div>

        {{-- Card LB1 --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-lg text-amber-600 dark:text-amber-300">
                    <x-heroicon-o-clipboard-document-check class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">LB1 (10 Besar Penyakit)</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Laporan diagnosa penyakit terbanyak per periode.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_lb1') }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
