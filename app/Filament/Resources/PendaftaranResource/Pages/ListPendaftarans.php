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
                        ->createOptionForm(\App\Filament\Resources\PasienResource::getFormSchema())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $pasien = Pasien::find($state, ['*']);
                                if ($pasien) {
                                    $hasHistory = Pendaftaran::where('pasien_id', '=', $state)->exists();
                                    $set('jenis_kunjungan', $hasHistory ? 'Lama' : 'Baru');
                                    $set('no_bpjs', $pasien->no_bpjs);
                                    if ($pasien->no_bpjs) {
                                        $set('jenis_pembayaran', 'BPJS');
                                    }
                                }
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
                            'BOK' => 'BOK (Bantuan Operasional Kesehatan)',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->default('Umum')
                        ->required()
                        ->live()
                        ->label('Jenis Pembayaran'),
                    TextInput::make('no_bpjs')
                        ->label('Nomor Kartu BPJS')
                        ->placeholder('Isi jika kategori BPJS...')
                        ->visible(fn ($get) => $get('jenis_pembayaran') === 'BPJS'),
                    TextInput::make('no_antrian')
                        ->numeric()
                        ->required()
                        ->readonly()
                        ->label('No. Antrian'),
                ])
                ->action(function (array $data): void {
                    $noBpjs = $data['no_bpjs'] ?? null;
                    unset($data['no_bpjs']); // Remove from pendaftaran data before create
                    
                    $data['status'] = 'Menunggu Poli';
                    $pendaftaran = Pendaftaran::create($data);

                    // Update BPJS data on Pasien if filled
                    if ($data['pasien_id'] && $noBpjs) {
                        $pasien = Pasien::find($data['pasien_id'], ['*']);
                        if ($pasien) {
                            $pasien->update([
                                'no_bpjs' => $noBpjs,
                                'cara_bayar' => 'BPJS',
                            ]);
                        }
                    }

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
