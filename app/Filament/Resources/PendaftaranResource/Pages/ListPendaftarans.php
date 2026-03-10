<?php

namespace App\Filament\Resources\PendaftaranResource\Pages;

use App\Filament\Resources\PendaftaranResource;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPendaftarans extends ListRecords
{
    protected static string $resource = PendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('daftar_pasien')
                ->label('Tambah Pendaftaran')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->slideOver()
                ->form([
                    Select::make('pasien_id')
                        ->label('Pasien')
                        ->relationship('pasien', 'nama_pasien')
                        ->getOptionLabelFromRecordUsing(fn ($record) => "[{$record->no_rm}] {$record->nama_pasien}")
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('no_rm')
                                ->label('No. RM')
                                ->default(fn () => Pasien::generateNoRm())
                                ->readonly()
                                ->required(),
                            TextInput::make('nama_pasien')->required(),
                            DatePicker::make('tanggal_lahir')->required(),
                            Select::make('jenis_kelamin')->options(['L' => 'L', 'P' => 'P'])->required(),
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
                    DatePicker::make('tanggal_daftar')
                        ->default(now())
                        ->required(),
                    Select::make('poli_id')
                        ->label('Poli')
                        ->relationship('poli', 'nama_poli')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('no_antrian', $state ? Pendaftaran::generateNoAntrian($state) : null)),
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
                ])
                ->action(function (array $data): void {
                    $data['status'] = 'Menunggu Poli';
                    Pendaftaran::create($data);

                    Notification::make()
                        ->title('Pendaftaran Berhasil!')
                        ->body('Pasien telah didaftarkan dan antrian poli telah dibuat.')
                        ->success()
                        ->send();
                })
                ->modalHeading('Daftarkan Pasien Baru')
                ->modalSubmitActionLabel('Simpan Pendaftaran')
                ->modalWidth('lg'),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\PendaftaranResource\Widgets\JadwalDokterWidget::class,
        ];
    }
}
