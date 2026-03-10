<?php

namespace App\Filament\Resources;

use App\Models\Pasien;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Pasien';

    public static function canAccess(): bool
    {
        return !auth()->user()?->hasRole('pasien');
    }
    protected static ?string $pluralModelLabel = 'Pasien';

    public static function getFormSchema(): array
    {
        return [
            Section::make('Identitas Demografi Pasien')->schema([
                TextInput::make('no_rm')
                    ->label('No. RM')
                    ->default(fn () => Pasien::generateNoRm())
                    ->readonly()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('nik')
                    ->label('NIK')
                    ->validationAttribute('NIK')
                    ->required()
                    ->length(16)
                    ->numeric()
                    ->unique(ignoreRecord: true),
                TextInput::make('nama_pasien')
                    ->label('Nama Lengkap')
                    ->required(),
                \Filament\Forms\Components\Grid::make(2)->schema([
                    TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->required(),
                    DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->closeOnDateSelection()
                        ->required(),
                ]),
                Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'])
                    ->required(),
                TextInput::make('no_hp')
                    ->label('Nomor HP')
                    ->tel()
                    ->required(),
            ])->columns(2),

            Section::make('Alamat Lengkap')->schema([
                Textarea::make('alamat')
                    ->label('Alamat Jalan / Lingkungan')
                    ->rows(2)
                    ->required(),
                \Filament\Forms\Components\Grid::make(3)->schema([
                    TextInput::make('desa_kelurahan')
                        ->label('Desa / Kelurahan')
                        ->required(),
                    TextInput::make('rt')
                        ->label('RT')
                        ->placeholder('000')
                        ->maxLength(5),
                    TextInput::make('rw')
                        ->label('RW')
                        ->placeholder('000')
                        ->maxLength(5),
                ]),
            ]),

            Section::make('Informasi Kontak & Penjamin')->schema([
                Select::make('cara_bayar')
                    ->label('Cara Bayar')
                    ->options([
                        'Umum' => 'Umum',
                        'BPJS' => 'BPJS (JKN)',
                        'Jamkesda' => 'Jamkesda',
                    ])
                    ->default('Umum')
                    ->required()
                    ->live(),
                TextInput::make('no_bpjs')
                    ->label('Nomor Kartu BPJS')
                    ->placeholder('Contoh: 0001234567890')
                    ->required(fn ($get) => $get('cara_bayar') === 'BPJS')
                    ->visible(fn ($get) => $get('cara_bayar') === 'BPJS'),
            ])->columns(2),

            Section::make('Akun Sistem')->schema([
                Placeholder::make('info_akun')
                    ->label('Informasi Akun')
                    ->content('Akun login pasien akan dibuat otomatis menggunakan No. RM sebagai username dan password default "Puskes[No. RM]".'),
            ])->visible(fn($operation) => $operation === 'create'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema(self::getFormSchema());
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('no_rm')->label('No. RM')->searchable()->sortable(),
            TextColumn::make('nik')->label('NIK')->searchable()->sortable(),
            TextColumn::make('nama_pasien')->searchable()->sortable(),
            TextColumn::make('tanggal_lahir')->date(),
            TextColumn::make('jenis_kelamin')->label('JK'),
            TextColumn::make('no_hp')->label('No. HP'),
            TextColumn::make('cara_bayar')
                ->label('Jenis Pembayaran')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'BPJS' => 'success',
                    'Umum' => 'info',
                    default => 'gray',
                })
                ->searchable()
                ->sortable(),
            TextColumn::make('user.username')
                ->label('Username Akun')
                ->badge()
                ->color('success')
                ->placeholder('Belum Dibuat'),
        ])->actions([
            Tables\Actions\Action::make('cetak_kartu')
                ->label('Cetak Kartu')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (Pasien $record): string => route('pasien.cetak-kartu', $record))
                ->openUrlInNewTab(),
            EditAction::make(),
            DeleteAction::make(),
        ])->groupedBulkActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => PasienResource\Pages\ListPasiens::route('/'),
            'create' => PasienResource\Pages\CreatePasien::route('/create'),
            'edit' => PasienResource\Pages\EditPasien::route('/{record}/edit'),
        ];
    }
}
