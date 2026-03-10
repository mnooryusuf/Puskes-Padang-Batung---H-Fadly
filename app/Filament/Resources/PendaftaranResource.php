<?php

namespace App\Filament\Resources;

use App\Models\Pendaftaran;
use App\Models\Pasien;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Pendaftaran';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return !auth()->user()?->hasRole('pasien');
    }
    protected static ?string $pluralModelLabel = 'Pendaftaran';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('pasien_id')->relationship('pasien', 'nama_pasien')
                ->getOptionLabelFromRecordUsing(fn ($record) => "[{$record->no_rm}] {$record->nama_pasien}")
                ->searchable()->preload()->createOptionForm([
                TextInput::make('no_rm')
                    ->label('No. RM')
                    ->default(fn () => Pasien::generateNoRm())
                    ->readonly()
                    ->required(),
                TextInput::make('nama_pasien')->required(),
                DatePicker::make('tanggal_lahir')->required(),
                Select::make('jenis_kelamin')->options(['L'=>'L','P'=>'P'])->required(),
                TextInput::make('no_hp')->required(),
                TextInput::make('alamat')->required(),
            ])
            ->required()
            ->live()
            ->afterStateUpdated(function ($state, callable $set) {
                if ($state) {
                    $hasHistory = Pendaftaran::where('pasien_id', $state)->exists();
                    $set('jenis_kunjungan', $hasHistory ? 'Lama' : 'Baru');
                }
            }),
            Select::make('jenis_kunjungan')
                ->options([
                    'Baru' => 'Pasien Baru',
                    'Lama' => 'Pasien Lama',
                ])
                ->required()
                ->label('Jenis Kunjungan'),
            \Filament\Forms\Components\DatePicker::make('tanggal_daftar')
                ->default(now())
                ->required()
                ->live(), // Added live to trigger updates
            Select::make('poli_id')
                ->relationship('poli', 'nama_poli')
                ->required()
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn ($state, callable $set) => $set('no_antrian', $state ? Pendaftaran::generateNoAntrian($state) : null))
                ->label('Poli')
                ->helperText(function (callable $get) {
                    $poliId = $get('poli_id');
                    $tanggal = $get('tanggal_daftar');
                    
                    if (!$poliId || !$tanggal) return null;
                    
                    $hari = \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd');
                    
                    $jadwals = \App\Models\JadwalDokter::with('dokter')
                        ->whereHas('dokter', fn($q) => $q->where('poli_id', $poliId))
                        ->where('hari', $hari)
                        ->where('is_active', true)
                        ->get();
                        
                    if ($jadwals->isEmpty()) {
                        return \Illuminate\Support\HtmlString::fromHtml('<span class="text-danger-500 font-medium">⚠️ Tidak ada jadwal dokter untuk poli ini pada tanggal terpilih.</span>');
                    }
                    
                    $totalKuota = $jadwals->sum('kuota');
                    $terdaftar = \App\Models\Pendaftaran::whereDate('tanggal_daftar', $tanggal)
                        ->where('poli_id', $poliId)
                        ->count();
                    $sisa = max(0, $totalKuota - $terdaftar);
                    
                    $listDokter = $jadwals->map(function($j) {
                        return "• {$j->dokter->nama_dokter} ({$j->jam_mulai} - {$j->jam_selesai})";
                    })->implode('<br>');
                    
                    $colorClass = $sisa > 0 ? 'text-success-600' : 'text-danger-600';
                    $statusQuota = $sisa > 0 ? "Sisa Kuota: {$sisa} dari {$totalKuota}" : "⚠️ Kuota Penuh ({$totalKuota})";
                    
                    return \Illuminate\Support\HtmlString::fromHtml("
                        <div class='mt-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700'>
                            <div class='font-medium {$colorClass} mb-1'>{$statusQuota}</div>
                            <div class='text-sm text-gray-600 dark:text-gray-400'>
                                <strong>Dokter Bertugas ({$hari}):</strong><br>
                                {$listDokter}
                            </div>
                        </div>
                    ");
                }),
            Select::make('jenis_pembayaran')
                ->options([
                    'Umum' => 'Umum',
                    'BPJS' => 'BPJS',
                    'Lainnya' => 'Lainnya',
                ])
                ->default('Umum')
                ->required()
                ->label('Jenis Pembayaran'),
            TextInput::make('no_antrian')
                ->numeric()
                ->required()
                ->readonly()
                ->label('No. Antrian'),
            Select::make('status')
                ->options([
                    'Menunggu Poli' => 'Menunggu Poli',
                    'Pemeriksaan' => 'Pemeriksaan',
                    'Menunggu Obat' => 'Menunggu Obat',
                    'Menunggu Pembayaran' => 'Menunggu Pembayaran',
                    'Selesai' => 'Selesai',
                ])
                ->default('Menunggu Poli')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('no_antrian')->sortable(),
                TextColumn::make('tanggal_daftar')->date()->sortable(),
                TextColumn::make('pasien.no_rm')->label('No. RM')->searchable()->sortable(),
                TextColumn::make('pasien.nama_pasien')->label('Nama Pasien')->searchable()->sortable(),
                TextColumn::make('jenis_kunjungan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'info',
                        'Lama' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('poli.nama_poli')->label('Poli')->sortable(),
                TextColumn::make('jenis_pembayaran')->badge()->color(fn (string $state): string => match ($state) {
                    'BPJS' => 'success',
                    'Umum' => 'info',
                    default => 'gray',
                }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu Poli' => 'warning',
                        'Pemeriksaan' => 'info',
                        'Menunggu Obat' => 'warning',
                        'Menunggu Pembayaran' => 'success',
                        'Selesai' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->whereDate('tanggal_daftar', now()))
                    ->default(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ])
            ->poll('10s');
    }

    public static function getPages(): array
    {
        return [
            'index' => PendaftaranResource\Pages\ListPendaftarans::route('/'),
            'create' => PendaftaranResource\Pages\CreatePendaftaran::route('/create'),
            'edit' => PendaftaranResource\Pages\EditPendaftaran::route('/{record}/edit'),
        ];
    }
}
