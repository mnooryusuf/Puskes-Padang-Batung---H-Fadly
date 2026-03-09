<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PasienDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard Saya';
    protected static ?string $title = 'Dashboard Pasien';
    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.pages.pasien-dashboard';

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user?->hasRole('pasien') ?? false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PasienStatsWidget::class,
            \App\Filament\Widgets\MonitorAntrianWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\RiwayatBerobatWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }
}
