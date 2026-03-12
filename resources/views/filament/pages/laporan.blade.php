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

        @if(!auth()->user()->hasRole('apoteker'))
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
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-600 dark:text-blue-300">
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
        @endif

        {{-- Card Kunjungan Per Poli --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-600 dark:text-blue-300">
                    <x-heroicon-o-building-office-2 class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Kunjungan per Poli</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Statistik jumlah pasien per masing-masing poli.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_kunjungan_poli') }}
            </div>
        </div>

        @if(!auth()->user()->hasRole('apoteker'))
        {{-- Card Pasien Baru vs Lama --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-600 dark:text-blue-300">
                    <x-heroicon-o-user-plus class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Pasien Baru vs Lama</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Analisa pertumbuhan jumlah pasien baru.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_pasien_baru') }}
            </div>
        </div>

        {{-- Card Rekap Tindakan --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-600 dark:text-blue-300">
                    <x-heroicon-o-document-magnifying-glass class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Rekap Tindakan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Daftar tindakan medis yang paling sering dilakukan.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_rekap_tindakan') }}
            </div>
        </div>

        {{-- Card Statistik Lab --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-600 dark:text-blue-300">
                    <x-heroicon-o-beaker class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Statistik Lab</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Analisa jumlah permintaan pemeriksaan laboratorium.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_statistik_lab') }}
            </div>
        </div>

        {{-- Card Status Pulang --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-600 dark:text-blue-300">
                    <x-heroicon-o-truck class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Status Pulang/Rujuk</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rekapitulasi jumlah pasien dirujuk atau meninggal.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_pasien_status') }}
            </div>
        </div>
        @endif

        {{-- Card Obat Expired --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg text-red-600 dark:text-red-300">
                    <x-heroicon-o-calendar-days class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Obat Kadaluwarsa</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Daftar obat yang akan expired dalam 3 bulan ke depan.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_obat_expired') }}
            </div>
        </div>

        {{-- Card Analisa Stok --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg text-orange-600 dark:text-orange-300">
                    <x-heroicon-o-chart-pie class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Analisa Fast/Slow Moving</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Analisa perputaran obat berdasarkan pemakaian resep.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_obat_analisa') }}
            </div>
        </div>

        @if(!auth()->user()->hasRole('apoteker'))
        {{-- Card Pendapatan Kasir --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg text-green-600 dark:text-green-300">
                    <x-heroicon-o-currency-dollar class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Rekap Pendapatan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rekapitulasi uang masuk di kasir per periode.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_pendapatan') }}
            </div>
        </div>

        {{-- Card Distribusi Penyakit --}}
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900 rounded-lg text-amber-600 dark:text-amber-300">
                    <x-heroicon-o-chart-bar-square class="w-8 h-8"/>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Distribusi Penyakit</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Analisa tren penyakit berdasarkan umur dan jenis kelamin.</p>
                </div>
            </div>
            <div class="mt-6">
                {{ $this->getAction('cetak_distribusi_penyakit') }}
            </div>
        </div>
        @endif

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

    <x-filament-actions::modals />
</x-filament-panels::page>
