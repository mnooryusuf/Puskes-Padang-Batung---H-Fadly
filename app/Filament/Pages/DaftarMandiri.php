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
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;

class DaftarMandiri extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';
    protected static ?string $navigationLabel = 'Pendaftaran Mandiri';
    protected static ?string $title = 'Pendaftaran Mandiri (Online)';
    protected static ?string $navigationGroup = 'Layanan Pasien';
    protected static ?int $navigationSort = 0;
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
                        'BOK' => 'BOK (Bantuan Operasional Kesehatan)',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->default(function () {
                        $pasien = auth()->user()?->pasien;
                        return $pasien?->cara_bayar ?? 'Umum';
                    })
                    ->required()
                    ->live(),

                TextInput::make('no_bpjs')
                    ->label('Nomor Kartu BPJS')
                    ->placeholder('Masukkan nomor kartu BPJS Anda...')
                    ->visible(fn ($get) => $get('jenis_pembayaran') === 'BPJS')
                    ->default(fn() => auth()->user()?->pasien?->no_bpjs)
                    ->required(fn ($get) => $get('jenis_pembayaran') === 'BPJS'),
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

        // Ambil data BPJS jika ada
        $noBpjs = $data['no_bpjs'] ?? null;
        
        Pendaftaran::create([
            'pasien_id' => $pasien->id,
            'tanggal_daftar' => $data['tanggal_daftar'],
            'poli_id' => $data['poli_id'],
            'no_antrian' => $noAntrian,
            'jenis_pembayaran' => $data['jenis_pembayaran'],
            'status' => 'Menunggu Poli',
            'jenis_kunjungan' => $hasHistory ? 'Lama' : 'Baru',
        ]);

        // Update BPJS data on Pasien if filled or changed
        if ($data['jenis_pembayaran'] === 'BPJS' && $noBpjs) {
            $pasien->update([
                'no_bpjs' => $noBpjs,
                'cara_bayar' => 'BPJS',
            ]);
        }

        Notification::make()
            ->title('Pendaftaran Berhasil')
            ->body('Anda berhasil mengambil nomor antrian.')
            ->success()
            ->send();

        $this->redirect(\App\Filament\Pages\PasienDashboard::getUrl());
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\SharedJadwalDokterWidget::class,
        ];
    }
}
