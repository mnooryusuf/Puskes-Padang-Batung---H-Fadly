<?php

namespace App\Filament\Pages;

use App\Models\Poli;
use App\Models\Tindakan;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;

class InformasiLayanan extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Layanan & Tarif';
    protected static ?string $title = 'Informasi Layanan & Tarif';
    protected static ?string $navigationGroup = 'Layanan Pasien';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('pasien');
    }

    protected static string $view = 'filament.pages.informasi-layanan';

    public function table(Table $table): Table
    {
        return $table
            ->query(Tindakan::query()->where('is_active', true))
            ->columns([
                TextColumn::make('kategori')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Tindakan' => 'info',
                        'Penunjang' => 'warning',
                        'BHP' => 'gray',
                        default => 'primary',
                    }),
                TextColumn::make('nama_tindakan')
                    ->label('Nama Layanan / Tindakan')
                    ->searchable(),
                TextColumn::make('harga')
                    ->money('idr')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('kategori')
                    ->options([
                        'Tindakan' => 'Tindakan Medis',
                        'Penunjang' => 'Penunjang (Lab/EKG/dll)',
                        'BHP' => 'BHP',
                    ])
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getViewData(): array
    {
        return [
            'polis' => Poli::all(),
        ];
    }
}
