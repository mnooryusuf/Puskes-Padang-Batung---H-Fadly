<div class="py-4">
    @if($resep && $resep->detailReseps->count() > 0)
    <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-full">Nama Obat</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Jumlah</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aturan Pakai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                @foreach($resep->detailReseps as $detail)
                <tr>
                    <td class="px-4 py-3 text-sm">
                        <span class="font-bold text-gray-900 dark:text-white block">{{ $detail->obatAktual->nama_obat }}</span>
                        <span class="text-xs text-gray-500">{{ $detail->obatAktual->sediaan }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-center whitespace-nowrap">
                        {{ $detail->jumlahAktual }} {{ $detail->obatAktual->satuan }}
                    </td>
                    <td class="px-4 py-3 text-sm font-bold text-primary-600 dark:text-primary-400 text-right whitespace-nowrap">
                        {{ $detail->dosis }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($resep->catatan_farmasi)
    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm text-blue-800 dark:text-blue-300">
        <strong>Catatan Farmasi:</strong> {{ $resep->catatan_farmasi }}
    </div>
    @endif
    @else
    <p class="text-center text-gray-500 py-4 italic">Resep belum diinput atau tidak ada obat yang diberikan.</p>
    @endif
</div>
