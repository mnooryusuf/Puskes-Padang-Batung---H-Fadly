<?php

namespace App\Filament\Pages;

use App\Models\Pasien;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilSaya extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profil Saya';
    protected static ?string $title = 'Profil Saya';
    protected static ?string $navigationGroup = 'Layanan Pasien';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('pasien');
    }

    protected static string $view = 'filament.pages.profil-saya';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $pasien = $user->pasien;

        $this->form->fill([
            'username' => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'no_rm' => $pasien?->no_rm,
            'nik' => $pasien?->nik,
            'tempat_lahir' => $pasien?->tempat_lahir,
            'tanggal_lahir' => $pasien?->tanggal_lahir,
            'jenis_kelamin' => $pasien?->jenis_kelamin,
            'no_hp' => $pasien?->no_hp,
            'alamat' => $pasien?->alamat,
            'desa_kelurahan' => $pasien?->desa_kelurahan,
            'rt' => $pasien?->rt,
            'rw' => $pasien?->rw,
            'no_bpjs' => $pasien?->no_bpjs,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Akun')
                    ->description('Kelola detail login Anda')
                    ->schema([
                        TextInput::make('username')
                            ->required()
                            ->unique(User::class, 'username', ignorable: auth()->user()),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->rule(Password::default()),
                    ])->columns(2),

                Section::make('Data Pribadi')
                    ->description('Informasi dasar yang terdaftar di sistem (Hanya Baca)')
                    ->schema([
                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->disabled(),
                        TextInput::make('no_rm')
                            ->label('Nomor Rekam Medis')
                            ->disabled(),
                        TextInput::make('nik')
                            ->label('NIK')
                            ->disabled(),
                        TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->disabled(),
                        TextInput::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->disabled(),
                        TextInput::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->disabled(),
                    ])->columns(3),

                Section::make('Kontak & Alamat')
                    ->description('Pastikan data ini selalu terbaru untuk memudahkan komunikasi')
                    ->schema([
                        TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->tel()
                            ->required(),
                        TextInput::make('no_bpjs')
                            ->label('Nomor BPJS'),
                        Textarea::make('alamat')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('desa_kelurahan')
                            ->label('Desa / Kelurahan'),
                        TextInput::make('rt')
                            ->label('RT')
                            ->maxLength(3),
                        TextInput::make('rw')
                            ->label('RW')
                            ->maxLength(3),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();
        $pasien = $user->pasien;

        // Update User
        $userData = [
            'username' => $data['username'],
        ];

        if (isset($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user->update($userData);

        // Update Pasien
        if ($pasien) {
            $pasien->update([
                'no_hp' => $data['no_hp'],
                'no_bpjs' => $data['no_bpjs'],
                'alamat' => $data['alamat'],
                'desa_kelurahan' => $data['desa_kelurahan'],
                'rt' => $data['rt'],
                'rw' => $data['rw'],
            ]);
        }

        Notification::make()
            ->title('Profil Berhasil Diperbarui')
            ->success()
            ->send();
    }
}
