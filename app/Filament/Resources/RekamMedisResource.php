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
                    ->searchable()
                    ->preload()
                    ->required(),
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
                            $history = RekamMedis::whereHas('pendaftaran', fn ($q) => $q->where('pasien_id', $pasienId))
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
                                $tindakan = $item->tindakan;
                                $status = $item->status_pulang;
                                $url = \App\Filament\Resources\RekamMedisResource::getUrl('view', ['record' => $item]);

                                    $html .= '<tr class="border border-gray-200 dark:border-gray-700">
                                    <td class="p-2">' . $date . '</td>
                                    <td class="p-2">' . $diagnosis . '</td>
                                    <td class="p-2">' . $tindakan . '</td>
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
                        ->label('Nadi')
                        ->numeric()
                        ->suffix('x/menit'),
                    TextInput::make('respirasi')
                        ->label('Respirasi')
                        ->numeric()
                        ->suffix('x/menit'),
                ]),
            ]),

            Section::make('4. Diagnosis ( ICD-10 )')->schema([
                Select::make('penyakit_id')
                    ->label('Diagnosis (ICD-10)')
                    ->relationship('penyakit', 'nama_penyakit')
                    ->getOptionLabelFromRecordUsing(fn($record) => "({$record->kode}) {$record->nama_penyakit}")
                    ->searchable(['kode', 'nama_penyakit'])
                    ->preload()
                    ->required(),
                Select::make('tipe_diagnosis')
                    ->options([
                        'Primer' => 'Primer (Utama)',
                        'Sekunder' => 'Sekunder (Penyerta)',
                    ])
                    ->required()
                    ->default('Primer'),
                Textarea::make('diagnosa')
                    ->label('Keterangan Diagnosis Tambahan')
                    ->rows(2),
            ])->columns(2),

            Section::make('5. Rencana Terapi (Resep & Tindakan)')->schema([
                Textarea::make('tindakan')
                    ->label('Tindakan Medis')
                    ->placeholder('Contoh: Hecting 3 jahitan, Injeksi, dll')
                    ->rows(2),
                Textarea::make('instruksi_lab')
                    ->label('Instruksi Laboratorium')
                    ->placeholder('Permintaan pemeriksaan darah, dll')
                    ->rows(2),
                
                \Filament\Forms\Components\HasManyRepeater::make('resep')
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
                                TextInput::make('jumlah')->numeric()->required(),
                            ])->columns(4)->required(),
                    ])->disableItemCreation(),
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
