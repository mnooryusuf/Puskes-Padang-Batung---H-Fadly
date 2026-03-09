<?php

namespace App\Filament\Pages;

use App\Models\Pendaftaran;
use App\Models\Poli;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class DaftarMandiri extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationLabel = 'Pendaftaran Mandiri';
    protected static ?string $title = 'Pendaftaran Mandiri (Online)';
    protected static ?int $navigationSort = -1;
    protected static string $view = 'filament.pages.daftar-mandiri';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('pasien') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('tanggal_daftar')
                    ->label('Tanggal Kunjungan')
                    ->default(now())
                    ->minDate(now())
                    ->maxDate(now()->addDays(7))
                    ->required()
                    ->native(false)
                    ->helperText('Pilih tanggal kunjungan antara hari ini hingga 7 hari ke depan.'),
                
                Select::make('poli_id')
                    ->label('Poli Tujuan')
                    ->options(Poli::pluck('nama_poli', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                
                Select::make('jenis_pembayaran')
                    ->label('Jenis Pembayaran')
                    ->options([
                        'Umum' => 'Umum',
                        'BPJS' => 'BPJS',
                        'Jamkesda' => 'Jamkesda',
                    ])
                    ->default(function () {
                        $pasien = auth()->user()?->pasien;
                        return $pasien?->cara_bayar ?? 'Umum';
                    })
                    ->required(),
            ])
            ->statePath('data');
    }

    public function mendaftar(): void
    {
        $data = $this->form->getState();
        $pasien = auth()->user()?->pasien;

        if (!$pasien) {
             Notification::make()
                ->title('Gagal mendaftar')
                ->body('Data pasien Anda belum terpasang di sistem. Silakan hubungi admin.')
                ->danger()
                ->send();
            return;
        }

        // Cek apakah hari ini sudah mendaftar di poli yang sama
        $alreadyRegistered = Pendaftaran::where('pasien_id', $pasien->id)
            ->whereDate('tanggal_daftar', $data['tanggal_daftar'])
            ->where('poli_id', $data['poli_id'])
            ->exists();

        if ($alreadyRegistered) {
            Notification::make()
                ->title('Gagal mendaftar')
                ->body('Anda sudah mendaftar ke Poli ini pada tanggal tersebut.')
                ->warning()
                ->send();
            return;
        }

        // Generate No Antrian
        $noAntrian = Pendaftaran::generateNoAntrian($data['poli_id']);
        
        // Cek riwayat untuk Jenis Kunjungan
        $hasHistory = Pendaftaran::where('pasien_id', $pasien->id)->exists();

        Pendaftaran::create([
            'pasien_id' => $pasien->id,
            'tanggal_daftar' => $data['tanggal_daftar'],
            'poli_id' => $data['poli_id'],
            'no_antrian' => $noAntrian,
            'jenis_pembayaran' => $data['jenis_pembayaran'],
            'status' => 'Menunggu Poli',
            'jenis_kunjungan' => $hasHistory ? 'Lama' : 'Baru',
        ]);

        Notification::make()
            ->title('Pendaftaran Berhasil')
            ->body('Anda berhasil mengambil nomor antrian.')
            ->success()
            ->send();

        $this->redirect(PasienDashboard::getUrl());
    }
}
