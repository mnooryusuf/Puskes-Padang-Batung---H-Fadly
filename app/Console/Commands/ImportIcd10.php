<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportIcd10 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-icd10';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data ICD-10 dari SATUSEHAT/WHO via GitHub';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://raw.githubusercontent.com/fendis0709/icd-10/master/master_icd_x.json';
        $this->info("Mengambil data dari $url...");

        try {
            $response = \Illuminate\Support\Facades\Http::get($url);
            if (!$response->successful()) {
                $this->error("Gagal mengambil data dari GitHub.");
                return Command::FAILURE;
            }

            $data = $response->json();
            $total = count($data);
            $this->info("Ditemukan $total data. Memulai proses impor...");

            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $chunks = array_chunk($data, 500);

            foreach ($chunks as $chunk) {
                $toUpsert = [];
                foreach ($chunk as $item) {
                    $toUpsert[] = [
                        'kode' => $item['kode_icd'],
                        'nama_penyakit' => $item['nama_icd_indo'] ?? $item['nama_icd'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                \App\Models\Penyakit::upsert($toUpsert, ['kode'], ['nama_penyakit', 'updated_at']);
                $bar->advance(count($chunk));
            }

            $bar->finish();
            $this->newLine();
            $this->info("Impor selesai! Seluruh data ICD-10 berhasil diperbarui.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Kesalahan: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
