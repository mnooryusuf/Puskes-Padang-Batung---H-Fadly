<div class="space-y-3">
    @php
        $poliId = $getState(); // We will bind poli_id to this component's state
        $tanggalDaftar = now()->format('Y-m-d'); // Default to today
        
        // Try to get form context if possible, otherwise use default date
        try {
            $livewire = $this->getLivewire();
            if ($livewire && isset($livewire->data['tanggal_daftar'])) {
                $tanggalDaftar = $livewire->data['tanggal_daftar'];
            }
        } catch (\Exception $e) {}

        if (!$poliId) {
            echo '<div class="text-xs text-gray-400 italic flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                    💡 Silakan pilih Poli untuk melihat jadwal dokter.
                  </div>';
        } else {
            $date = \Carbon\Carbon::parse($tanggalDaftar);
            $days = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
            $currentDay = $days[$date->dayOfWeek];

            $jadwals = \App\Models\JadwalDokter::query()
                ->where('hari', $currentDay)
                ->where('is_active', true)
                ->whereHas('dokter', fn ($query) => $query->where('poli_id', $poliId))
                ->with('dokter')
                ->get();

            if ($jadwals->isEmpty()) {
                echo '<div class="p-4 bg-red-50 text-red-700 rounded-xl text-sm border border-red-200">
                        <p class="font-bold">🚫 Tidak Ada Jadwal</p>
                        <p class="text-xs mt-1">Tidak ada dokter bertugas di poli ini pada hari '.$currentDay.'.</p>
                      </div>';
            } else {
                $todayRegistrationsCount = \App\Models\Pendaftaran::where('poli_id', $poliId)
                    ->whereDate('tanggal_daftar', $tanggalDaftar)
                    ->count();

                foreach ($jadwals as $j) {
                    $jamMulai = \Carbon\Carbon::parse($j->jam_mulai)->format('H:i');
                    $jamSelesai = \Carbon\Carbon::parse($j->jam_selesai)->format('H:i');
                    $sisaKuota = max(0, $j->kuota - $todayRegistrationsCount);
                    $kuotaColor = $sisaKuota <= 5 ? 'text-red-600' : 'text-green-600';
                    $bg = $sisaKuota <= 5 ? 'bg-red-50' : 'bg-blue-50';
                    $border = $sisaKuota <= 5 ? 'border-red-200' : 'border-blue-200';
                    
                    echo "<div class='overflow-hidden rounded-xl border {$border} shadow-sm bg-white'>
                            <div class='{$bg} p-3 border-b {$border} flex justify-between items-center'>
                                <div>
                                    <p class='font-bold text-gray-900'>{$j->dokter->nama_dokter}</p>
                                    <p class='text-[10px] uppercase text-gray-500'>Dokter Bertugas</p>
                                </div>
                                <div class='text-right'>
                                    <p class='text-xs font-bold'>{$jamMulai} - {$jamSelesai}</p>
                                    <p class='text-[9px] uppercase text-gray-500'>Praktek</p>
                                </div>
                            </div>
                            <div class='p-3 flex justify-between items-center'>
                                <div>
                                    <span class='text-[10px] text-gray-400 uppercase block'>Total Kuota</span>
                                    <span class='text-xs font-bold'>{$j->kuota} Pasien</span>
                                </div>
                                <div class='text-right'>
                                    <span class='text-[10px] text-gray-400 uppercase block'>Sisa Kuota</span>
                                    <span class='font-black text-xl {$kuotaColor}'>{$sisaKuota}</span>
                                </div>
                            </div>
                          </div>";
                }
            }
        }
    @endphp
</div>
