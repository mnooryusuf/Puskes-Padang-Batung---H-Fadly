<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AntrianApotekResource\Pages;
use App\Models\Antrian;
use App\Models\Obat;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class AntrianApotekResource extends Resource
{
    protected static ?string $model = Antrian::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Pelayanan';
    protected static ?string $modelLabel = 'Antrian Apotek';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('admin') || $user?->hasRole('apoteker');
    }
    protected static ?string $slug = 'antrian-apotek';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('kategori', 'Obat')->where('status', 'Menunggu')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('kategori', 'Obat')->whereNotIn('status', ['Selesai', 'Ditolak']))
            ->columns([
                TextColumn::make('nomor_antrian')
                    ->label('No. Antrian')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('pendaftaran.pasien.no_rm')
                    ->label('No. RM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pendaftaran.pasien.nama_pasien')
                    ->label('Nama Pasien')
                    ->searchable(),
                TextColumn::make('pendaftaran.rekamMedis.resep.id')
                    ->label('Resep')
                    ->formatStateUsing(fn ($state) => $state ? "Resep #$state" : 'Tidak Ada'),
                TextColumn::make('pendaftaran.rekamMedis.resep.status_pengambilan')
                    ->label('Status Resep')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'gray',
                        'Diproses' => 'warning',
                        'Siap Diambil' => 'info',
                        'Sudah Diserahkan' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Waktu Antri')
                    ->time(),
            ])
            ->actions([
                // 1. Panggil
                Action::make('panggil')
                    ->label('Panggil')
                    ->icon('heroicon-o-megaphone')
                    ->color('info')
                    ->extraAttributes(fn (Antrian $record): array => [
                        'onclick' => new HtmlString("window.speechSynthesis.cancel(); setTimeout(function(){ var msg = new SpeechSynthesisUtterance('Nomor antrian " . $record->nomor_antrian . ", silakan menuju ke Apotek'); msg.lang = 'id-ID'; msg.rate = 0.9; window.speechSynthesis.speak(msg); }, 100);")
                    ]),

                // 2. Proses (Slide-over: Edit jumlah, Substitusi, Catatan)
                Action::make('proses')
                    ->label('Proses Resep')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->slideOver()
                    ->modalHeading('Kelola Resep & Obat')
                    ->modalDescription('Sesuaikan jumlah obat, ganti obat jika perlu, dan tambahkan catatan farmasi.')
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Simpan & Proses')
                    ->fillForm(function (Antrian $record): array {
                        $resep = $record->pendaftaran->rekamMedis?->resep;
                        if (!$resep) return [];

                        $items = [];
                        foreach ($resep->detailReseps as $detail) {
                            $items[] = [
                                'detail_resep_id' => $detail->id,
                                'obat_id' => $detail->obat_id,
                                'nama_obat_resep' => $detail->obat?->nama_obat, // For display only
                                'stok_tersedia' => $detail->obat?->stok ?? 0,
                                'dosis' => $detail->dosis,
                                'jumlah_resep' => $detail->jumlah,
                                'jumlah_diserahkan' => $detail->jumlah_diserahkan ?? $detail->jumlah,
                                'obat_pengganti_id' => $detail->obat_pengganti_id,
                            ];
                        }

                        return [
                            'obat_items' => $items,
                            'catatan_farmasi' => $resep->catatan_farmasi ?? '',
                        ];
                    })
                    ->form([
                        Repeater::make('obat_items')
                            ->label('Daftar Obat Resep')
                            ->schema([
                                \Filament\Forms\Components\Hidden::make('detail_resep_id'),
                                Grid::make(3)->schema([
                                    Select::make('obat_id')
                                        ->label('Nama Obat')
                                        ->options(Obat::pluck('nama_obat', 'id'))
                                        ->searchable()
                                        ->required()
                                        ->disabled(fn ($get) => filled($get('detail_resep_id')))
                                        ->dehydrated()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if ($state) {
                                                $obat = Obat::find($state);
                                                $set('stok_tersedia', $obat?->stok ?? 0);
                                            }
                                        })
                                        ->live(),
                                    TextInput::make('dosis')
                                        ->label('Aturan Pakai')
                                        ->required()
                                        ->disabled(fn ($get) => filled($get('detail_resep_id')))
                                        ->dehydrated(),
                                    TextInput::make('stok_tersedia')
                                        ->label('Stok Tersedia')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->suffixIcon(fn ($state) => (int) $state < 10 ? 'heroicon-o-exclamation-triangle' : null),
                                ]),
                                Grid::make(3)->schema([
                                    TextInput::make('jumlah_resep')
                                        ->label('Jml Resep')
                                        ->numeric()
                                        ->disabled(fn ($get) => filled($get('detail_resep_id')))
                                        ->dehydrated()
                                        ->default(1),
                                    TextInput::make('jumlah_diserahkan')
                                        ->label('Jml Diserahkan')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0)
                                        ->helperText('Sesuaikan jika stok tidak cukup'),
                                    Select::make('obat_pengganti_id')
                                        ->label('Ganti Obat (Substitusi)')
                                        ->options(Obat::pluck('nama_obat', 'id'))
                                        ->searchable()
                                        ->placeholder('Obat asli')
                                        ->helperText('Kosongkan jika tidak diganti'),
                                ]),
                            ])
                            ->deletable(true)
                            ->addable(true)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                $obat = Obat::find($state['obat_id'] ?? null);
                                return $obat?->nama_obat ?? 'Obat Baru';
                            }),
                        Textarea::make('catatan_farmasi')
                            ->label('Catatan Farmasi')
                            ->placeholder('Tuliskan catatan untuk pasien atau staf (opsional)')
                            ->rows(3),
                    ])
                    ->action(function (array $data, Antrian $record) {
                        $resep = $record->pendaftaran->rekamMedis?->resep;
                        if (!$resep) return;

                        $submittedIds = collect($data['obat_items'])->pluck('detail_resep_id')->filter()->toArray();
                        
                        // 1. Delete items that were removed in the UI
                        $resep->detailReseps()->whereNotIn('id', $submittedIds)->delete();

                        // 2. Update existing or Create new items
                        foreach ($data['obat_items'] as $item) {
                            if (filled($item['detail_resep_id'])) {
                                // Update existing
                                \App\Models\DetailResep::find($item['detail_resep_id'])?->update([
                                    'jumlah_diserahkan' => $item['jumlah_diserahkan'],
                                    'obat_pengganti_id' => $item['obat_pengganti_id'] ?: null,
                                ]);
                            } else {
                                // Create new
                                $resep->detailReseps()->create([
                                    'obat_id' => $item['obat_id'],
                                    'dosis' => $item['dosis'],
                                    'jumlah' => $item['jumlah_resep'] ?? 1,
                                    'jumlah_diserahkan' => $item['jumlah_diserahkan'],
                                    'obat_pengganti_id' => $item['obat_pengganti_id'] ?: null,
                                ]);
                            }
                        }

                        // Update catatan farmasi & status
                        $resep->update([
                            'catatan_farmasi' => $data['catatan_farmasi'] ?? null,
                            'status_pengambilan' => 'Diproses',
                        ]);

                        Notification::make()
                            ->title('Resep berhasil diproses!')
                            ->body('Data obat telah diperbarui dan resep ditandai sebagai "Diproses".')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Antrian $record) => in_array(
                        $record->pendaftaran->rekamMedis?->resep?->status_pengambilan,
                        ['Menunggu', 'Diproses']
                    )),

                // 3. Lihat Resep
                Action::make('lihat_resep')
                    ->label('Lihat Resep')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->modalHeading('Rincian E-Resep Pasien')
                    ->modalContent(fn (Antrian $record) => view('filament.resources.antrian-apotek-resource.lihat-resep-modal', ['record' => $record->pendaftaran]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->visible(fn (Antrian $record) => $record->pendaftaran->rekamMedis?->resep()->exists()),

                // 4. Cetak Etiket
                Action::make('cetak_etiket')
                    ->label('Etiket')
                    ->icon('heroicon-o-tag')
                    ->color('gray')
                    ->url(fn (Antrian $record): ?string => $record->pendaftaran->rekamMedis?->resep ? route('resep.cetak-etiket', $record->pendaftaran->rekamMedis->resep) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (Antrian $record) => $record->pendaftaran->rekamMedis?->resep !== null),

                // 5. Siap Diambil
                Action::make('siap')
                    ->label('Siap Diambil')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->action(fn (Antrian $record) => $record->pendaftaran->rekamMedis->resep->update(['status_pengambilan' => 'Siap Diambil']))
                    ->visible(fn (Antrian $record) => $record->pendaftaran->rekamMedis?->resep?->status_pengambilan === 'Diproses'),

                // 6. Serahkan Obat (menggunakan jumlah_diserahkan & obat_pengganti)
                Action::make('serahkan_obat')
                    ->label('Serahkan Obat')
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penyerahan Obat')
                    ->modalDescription('Pastikan obat sudah diserahkan kepada pasien. Stok akan dikurangi otomatis dan pasien diarahkan ke Kasir.')
                    ->action(function (Antrian $record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            $resep = $record->pendaftaran->rekamMedis?->resep;
                            if ($resep) {
                                // Update Status Resep
                                $resep->update(['status_pengambilan' => 'Sudah Diserahkan']);

                                // Kurangi Stok berdasarkan jumlah_diserahkan & obat aktual
                                foreach ($resep->detailReseps as $detail) {
                                    $obat = $detail->obat_aktual; // uses accessor (substituted or original)
                                    $jumlah = $detail->jumlah_aktual; // uses accessor
                                    if ($obat && $jumlah > 0) {
                                        $obat->decrement('stok', $jumlah);
                                    }
                                }
                            }

                            // Update Status Antrian
                            $record->update(['status' => 'Selesai']);
                            
                            // Update global pendaftaran status
                            $record->pendaftaran->update(['status' => 'Menunggu Pembayaran']);
                            
                            // Ensure there is a cashier queue
                            Antrian::firstOrCreate(
                                [
                                    'pendaftaran_id' => $record->pendaftaran_id,
                                    'kategori' => 'Kasir',
                                ],
                                [
                                    'nomor_antrian' => Antrian::generateNomor('Kasir'),
                                    'status' => 'Menunggu',
                                ]
                            );
                        });

                        Notification::make()
                            ->title('Obat berhasil diserahkan!')
                            ->body('Stok dikurangi, pasien diarahkan ke Kasir.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Antrian $record) => $record->pendaftaran->rekamMedis?->resep?->status_pengambilan === 'Siap Diambil'),

                // 7. Tolak Resep (Fitur 4)
                Action::make('tolak_resep')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Resep')
                    ->modalDescription('Apakah Anda yakin ingin menolak resep ini? Tuliskan alasan penolakan.')
                    ->form([
                        Textarea::make('alasan_tolak')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Contoh: Obat X tidak tersedia, perlu konsultasi ulang dengan dokter.')
                            ->rows(3),
                    ])
                    ->action(function (array $data, Antrian $record) {
                        $resep = $record->pendaftaran->rekamMedis?->resep;
                        if ($resep) {
                            $resep->update([
                                'status_pengambilan' => 'Ditolak',
                                'catatan_farmasi' => 'DITOLAK: ' . $data['alasan_tolak'],
                            ]);
                        }

                        $record->update(['status' => 'Ditolak']);

                        Notification::make()
                            ->title('Resep ditolak')
                            ->body('Resep telah ditolak. Pasien perlu dikonsultasikan ulang dengan dokter.')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (Antrian $record) => in_array(
                        $record->pendaftaran->rekamMedis?->resep?->status_pengambilan,
                        ['Menunggu', 'Diproses']
                    )),
            ])
            ->poll('10s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAntrianApoteks::route('/'),
        ];
    }
}
