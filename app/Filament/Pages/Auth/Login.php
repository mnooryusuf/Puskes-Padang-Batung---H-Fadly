<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BasePage;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BasePage
{
    public function mount(): void
    {
        parent::mount();

        if (app()->environment('local')) {
            $this->form->fill([
                'username' => 'admin',
                'password' => 'password',
                'remember' => true,
            ]);
        }
    }

    public function getHeading(): string | Htmlable
    {
        return 'Aplikasi Rawat Jalan Puskesmas Padang Batung';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Masuk ke Sistem';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent()->revealable(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
