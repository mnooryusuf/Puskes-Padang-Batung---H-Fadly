<?php

namespace App\Filament\Resources;

use App\Models\RekamMedis;
use App\Models\Pendaftaran;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\RekamMedisResource\Pages;
use App\Filament\Resources\RekamMedisResource\RelationManagers\HistoryRekamMedisRelationManager;

class RekamMedisResource extends Resource
{
    protected static ?string $model = RekamMedis::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Rekam Medis';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') || auth()->user()?->hasRole('dokter');
    }
    protected static ?string $pluralModelLabel = 'Rekam Medis';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('1. Data Pasien & Antrian')->schema([
                Select::make('pendaftaran_id')
                    ->relationship('pendaftaran', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => "Antrian #{$record->no_antrian} - [{$record->pasien->no_rm}] {$record->pasien->nama_pasien}")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),
                Select::make('dokter_id')
                    ->relationship('dokter', 'nama_dokter')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nama_dokter} - (" . ($record->poli?->nama_poli ?? 'Tanpa Poli') . ")")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => auth()->user()->dokter?->id),
            ])->columns(2),

            Section::make('Riwayat Medis Pasien (Sebelumnya)')
                ->description('Melihat riwayat diagnosis dan terapi pasien pada kunjungan-kunjungan lalu.')
                ->collapsible()
                ->schema([
                    \Filament\Forms\Components\Placeholder::make('history_placeholder')
                        ->label('')
                        ->content(function ($get) {
                            $pendaftaranId = $get('pendaftaran_id');
                            if (!$pendaftaranId) return 'Silakan pilih pasien terlebih dahulu.';

                            $pendaftaran = Pendaftaran::find($pendaftaranId);
                            if (!$pendaftaran) return 'Data pendaftaran tidak ditemukan.';

                            $pasienId = $pendaftaran->pasien_id;
                            $history = RekamMedis::with(['penyakit', 'tindakans', 'resep.detailReseps.obat'])
                                ->whereHas('pendaftaran', fn ($q) => $q->where('pasien_id', $pasienId))
                                ->orderBy('created_at', 'desc')
                                ->get();

                            if ($history->isEmpty()) return 'Pasien ini belum memiliki riwayat rekam medis.';

                            $html = '<table class="w-full text-sm text-left border-collapse border border-gray-200 dark:border-gray-700">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                        <th class="p-2 border border-gray-200 dark:border-gray-700">Tanggal</th>
                                        <th class="p-2 border border-gray-200 dark:border-gray-700">Diagnosis (ICD-10)</th>
                                        <th class="p-2 border border-gray-200 dark:border-gray-700">Tindakan/Resep</th>
                                        <th class="p-2 border border-gray-200 dark:border-gray-700">Kondisi</th>
                                        <th class="p-2 border border-gray-200 dark:border-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';

                            foreach ($history as $item) {
                                $date = $item->created_at->format('d/m/Y H:i');
                                $diagnosis = "({$item->penyakit?->kode}) {$item->penyakit?->nama_penyakit}";
                                
                                // Gabungkan daftar tindakan pivot
                                $tindakanList = "";
                                if ($item->tindakans->isNotEmpty()) {
                                    $tindakanList = collect($item->tindakans)->map(fn($t) => "• {$t->nama_tindakan} ({$t->pivot->jumlah}x)")->implode('<br>');
                                }
                                
                                // Gabungkan daftar obat resep
                                $resepList = "";
                                if ($item->resep && $item->resep->detailReseps->isNotEmpty()) {
                                    $resepList = collect($item->resep->detailReseps)->map(fn($d) => "💊 {$d->obat->nama_obat} ({$d->jumlah}) - {$d->dosis}")->implode('<br>');
                                }

                                $gabunganTerapi = $tindakanList . ($tindakanList && $resepList ? '<br><hr class="my-1 border-gray-300 dark:border-gray-600">' : '') . $resepList;
                                $gabunganTerapi = $gabunganTerapi ?: '-';

                                $status = $item->status_pulang;
                                $url = \App\Filament\Resources\RekamMedisResource::getUrl('view', ['record' => $item]);

                                    $html .= '<tr class="border border-gray-200 dark:border-gray-700">
                                    <td class="p-2">' . $date . '</td>
                                    <td class="p-2">' . $diagnosis . '</td>
                                    <td class="p-2">' . $gabunganTerapi . '</td>
                                    <td class="p-2">' . $status . '</td>
                                    <td class="p-2">
                                        <button type="button" 
                                            x-on:click="$wire.mountAction(\'viewHistory\', { recordId: ' . $item->id . ' })"
                                            class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-primary-600 rounded-md hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-600">
                                            Detail
                                        </button>
                                    </td>
                                </tr>';
                            }

                            $html .= '</tbody></table>';

                            return new \Illuminate\Support\HtmlString($html);
                        }),
                ]),


            Section::make('2. Anamnesa (Keluhan & Riwayat)')->schema([
                Textarea::make('keluhan_utama')
                    ->label('Keluhan Utama')
                    ->placeholder('Apa yang dirasakan pasien?')
                    ->required()
                    ->rows(3),
                Textarea::make('riwayat_penyakit_sekarang')
                    ->label('Riwayat Penyakit Sekarang')
                    ->placeholder('Sudah berapa lama? Faktor apa yang memperberat?')
                    ->rows(3),
                TextInput::make('riwayat_alergi')
                    ->label('Riwayat Alergi')
                    ->placeholder('Isi "Tidak Ada" jika tidak ada')
                    ->default('Tidak Ada')
                    ->required(),
            ]),

            Section::make('3. Pemeriksaan Fisik (Tanda Vital)')->schema([
                \Filament\Forms\Components\Grid::make(3)->schema([
                    TextInput::make('berat_badan')
                        ->label('Berat Badan (Kg)')
                        ->numeric()
                        ->suffix('kg'),
                    TextInput::make('tekanan_darah')
                        ->label('Tekanan Darah')
                        ->placeholder('Contoh: 120/80')
                        ->suffix('mmHg'),
                    TextInput::make('suhu_tubuh')
                        ->label('Suhu Tubuh')
                        ->numeric()
                        ->suffix('°C'),
                    TextInput::make('nadi')
                        ->label('Denyut Nadi')
                        ->numeric()
                        ->suffix('x/mnt'),
                    TextInput::make('respirasi')
                        ->label('Respirasi')
                        ->numeric()
                        ->suffix('x/mnt'),
                ]),
            ]),

            Section::make('4. Diagnosis & Tindakan')->schema([
                Select::make('penyakit_id')
                    ->relationship('penyakit', 'nama_penyakit')
                    ->getOptionLabelFromRecordUsing(fn($record) => "({$record->kode}) {$record->nama_penyakit}")
                    ->searchable(['kode', 'nama_penyakit'])
                    ->preload()
                    ->required()
                    ->label('Diagnosis (ICD-10)'),
                Select::make('tipe_diagnosis')
                    ->options([
                        'Primer' => 'Diagnosis Primer',
                        'Sekunder' => 'Diagnosis Sekunder',
                    ])
                    ->required()
                    ->default('Primer'),
                
                // Tambahan: Multi-select Tindakan / BHP (yang ada harganya)
                Repeater::make('tindakans')
                    ->relationship('tindakans') // Kembali menggunakan BelongsToMany (tindakans), BUKAN Model pivot
                    ->label('Input Tagihan Tindakan / Pemeriksaan / BHP')
                    ->schema([
                        Select::make('tindakan_id')
                            ->label('Nama Tindakan / Layanan')
                            ->options(\App\Models\Tindakan::where('is_active', true)
                                ->where('kategori', '!=', 'Penunjang') // Filter agar tidak duplikat dengan section lab
                                ->pluck('nama_tindakan', 'id'))
                            ->searchable()
                            ->preload()
                            ->disableOptionWhen(function ($value, $state, \Filament\Forms\Get $get) {
                                return collect($get('../../tindakans'))->pluck('tindakan_id')->contains($value);
                            })
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $tindakan = \App\Models\Tindakan::find($state);
                                    if ($tindakan) {
                                        $set('harga_snapshot', $tindakan->harga);
                                    }
                                }
                            })
                            ->required(),
                        TextInput::make('jumlah')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1),
                    ])
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                        // Tidak ada yang dilakukan di sini karena kita matikan dehydration, sync logic manual akan kita jalankan setelah create parent
                        return $data;
                    })
                    ->saveRelationshipsUsing(function (\Illuminate\Database\Eloquent\Model $record, array $state, \Filament\Forms\Get $get) {
                        $syncData = [];
                        
                        // 1. Ambil data dari repeater tindakan umum
                        foreach ($state as $item) {
                            if (!empty($item['tindakan_id'])) {
                                $syncData[$item['tindakan_id']] = [
                                    'jumlah' => $item['jumlah'] ?? 1,
                                    'harga_snapshot' => $item['harga_snapshot'] ?? 0,
                                ];
                            }
                        }

                        // 2. Ambil data dari pilihan laboratorium terstruktur
                        $labItems = $get('lab_layanan_ids') ?? [];
                        foreach ($labItems as $labId) {
                            $tindakanLab = \App\Models\Tindakan::find($labId);
                            if ($tindakanLab) {
                                $syncData[$labId] = [
                                    'jumlah' => 1,
                                    'harga_snapshot' => $tindakanLab->harga,
                                ];
                            }
                        }

                        $record->tindakans()->sync($syncData);
                    })
                    ->dehydrated(false)
                    ->columns(2)
                    ->addActionLabel('Tambah Tindakan'),
            ]),

            Section::make('5. Permintaan Laboratorium & Terapi')->schema([
                Select::make('lab_layanan_ids')
                    ->label('Pilih Pemeriksaan Laboratorium (Pemeriksaan Penunjang)')
                    ->options(\App\Models\Tindakan::where('kategori', 'Penunjang')->where('is_active', true)->pluck('nama_tindakan', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->default([])
                    ->dehydrated(false)
                    ->afterStateHydrated(function ($set, $record) {
                        if ($record) {
                            $set('lab_layanan_ids', $record->tindakans()->where('kategori', 'Penunjang')->pluck('tindakan_id')->toArray());
                        }
                    }),
                
                Textarea::make('instruksi_lab')
                    ->label('Catatan Tambahan untuk Laboratorium')
                    ->placeholder('Contoh: Puasa dari jam 10 malam, dll')
                    ->rows(2),
                
                \Filament\Forms\Components\Placeholder::make('divider')
                    ->label('')
                    ->content(new \Illuminate\Support\HtmlString('<hr class="border-gray-300 dark:border-gray-600">')),
                
                Repeater::make('resep')
                    ->label('E-Resep / Obat')
                    ->relationship('resep')
                    ->schema([
                        Repeater::make('detailReseps')
                            ->relationship()
                            ->schema([
                                Select::make('obat_id')
                                    ->relationship('obat', 'nama_obat')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $obat = \App\Models\Obat::find($state);
                                        $set('satuan_view', $obat?->satuan);
                                        $set('stok_info', $obat ? "Stok: {$obat->stok}" : null);
                                    }),
                                TextInput::make('satuan_view')
                                    ->label('Satuan')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function ($set, $get) {
                                        $obatId = $get('obat_id');
                                        if ($obatId) {
                                            $obat = \App\Models\Obat::find($obatId);
                                            $set('satuan_view', $obat?->satuan);
                                        }
                                    }),
                                TextInput::make('dosis')->required(),
                                TextInput::make('jumlah')
                                    ->numeric()
                                    ->required()
                                    ->placeholder(function (callable $get) {
                                        $obatId = $get('obat_id');
                                        if ($obatId) {
                                            $obat = \App\Models\Obat::find($obatId);
                                            return $obat ? "Stok: {$obat->stok}" : null;
                                        }
                                        return 'Pilih obat dulu';
                                    })
                                    ->helperText(function (callable $get) {
                                        $obatId = $get('obat_id');
                                        if ($obatId) {
                                            $obat = \App\Models\Obat::find($obatId);
                                            if ($obat && $obat->stok < 10) {
                                                return "⚠️ Stok menipis! ({$obat->stok})";
                                            }
                                            return $obat ? "Tersedia: {$obat->stok}" : null;
                                        }
                                        return null;
                                    }),
                            ])->columns(4)->required(),
                    ])->addable(false),
            ]),

            Section::make('6. Kondisi Akhir & Pemulangan')->schema([
                Select::make('status_pulang')
                    ->options([
                        'Sembuh' => 'Sembuh',
                        'Kontrol' => 'Perlu Kontrol Kembali',
                        'Rujuk' => 'Dirujuk ke Rumah Sakit',
                        'Meninggal' => 'Meninggal Dunia',
                    ])
                    ->required()
                    ->live(),
                \Filament\Forms\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('rs_tujuan')
                            ->label('Rumah Sakit Tujuan')
                            ->required()
                            ->hidden(fn ($get) => $get('status_pulang') !== 'Rujuk'),
                        Textarea::make('alasan_rujuk')
                            ->label('Alasan Rujuk')
                            ->required()
                            ->hidden(fn ($get) => $get('status_pulang') !== 'Rujuk'),
                    ])->visible(fn ($get) => $get('status_pulang') === 'Rujuk'),
            ]),
        ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            HistoryRekamMedisRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
            TextColumn::make('pendaftaran.pasien.no_rm')->label('No. RM')->searchable()->sortable(),
            TextColumn::make('pendaftaran.pasien.nama_pasien')->label('Pasien')->searchable(),
            TextColumn::make('penyakit.kode')->label('ICD-10')->sortable(),
            TextColumn::make('penyakit.nama_penyakit')->label('Diagnosis')->limit(30),
            TextColumn::make('status_pulang')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Sembuh' => 'success',
                    'Kontrol' => 'warning',
                    'Rujuk' => 'danger',
                    default => 'gray',
                }),
        ])->actions([
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make(),
        ])->groupedBulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListRekamMedisPasiens::route('/'),
            'by-pasien' => Pages\ListRekamMedisByPasien::route('/pasien/{pasienId}'),
            'create'    => Pages\CreateRekamMedis::route('/create'),
            'view'      => Pages\ViewRekamMedis::route('/{record}'),
            'edit'      => Pages\EditRekamMedis::route('/{record}/edit'),
        ];
    }
}
