<?php

namespace App\Filament\Resources\PenyakitResource\Pages;

use App\Filament\Resources\PenyakitResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePenyakits extends ManageRecords
{
    protected static string $resource = PenyakitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import')
                ->label('Import Spreadsheet')
                ->icon('heroicon-o-cloud-arrow-down')
                ->visible(fn () => !auth()->user()->hasRole('apoteker'))
                ->color('success')
                ->form([
                    \Filament\Forms\Components\TextInput::make('url')
                        ->label('URL Google Spreadsheet (CSV Export)')
                        ->url()
                        ->default('https://docs.google.com/spreadsheets/d/12_72PvHRLWny3VEwodEI6TRDc7QqWdiF/export?format=csv&gid=1224697162')
                        ->required()
                        ->helperText('Pastikan URL dapat diakses publik dan memiliki format export CSV. (Kolom 1: Kode, Kolom 2: Nama)')
                ])
                ->action(function (array $data) {
                    $url = $data['url'];
                    
                    try {
                        // Buka stream URL langsung
                        $file = fopen($url, 'r');
                        if (!$file) {
                            throw new \Exception("Tidak dapat membuka URL.");
                        }

                        $header = fgetcsv($file, 4000, ','); // skip header
                        
                        $imported = 0;
                        while (($row = fgetcsv($file, 4000, ',')) !== false) {
                            // Cek delimiter jika fgetcsv tertipu (satu string panjang)
                            $str = implode(',', $row);
                            $delimeter = strpos($str, ';') !== false && strpos($str, ',') === false ? ';' : ',';
                            
                            if($delimeter === ';'){
                                $row = str_getcsv($str, ';');
                            }
                            
                            $kode = isset($row[0]) ? trim(str_replace('"', '', $row[0])) : null;
                            $nama = isset($row[1]) ? trim(str_replace('"', '', $row[1])) : null;
                            
                            // Abaikan baris kosong atau baris header cadangan
                            if ($kode && $nama && strtolower($kode) !== 'kode') {
                                \App\Models\Penyakit::updateOrCreate(
                                    ['kode' => $kode],
                                    ['nama_penyakit' => $nama, 'is_active' => true]
                                );
                                $imported++;
                            }
                        }
                        fclose($file);

                        \Filament\Notifications\Notification::make()
                            ->title('Import Berhasil')
                            ->body("{$imported} master penyakit berhasil diunduh dan ditambahkan.")
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal Mengimpor!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
