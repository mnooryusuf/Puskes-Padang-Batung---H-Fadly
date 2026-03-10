<?php

namespace App\Observers;

use App\Models\Obat;

class ObatObserver
{
    /**
     * Handle the Obat "created" event.
     */
    public function created(Obat $obat): void
    {
        $obat->stockHistories()->create([
            'type' => 'in',
            'quantity' => $obat->stok,
            'stock_after' => $obat->stok,
            'description' => 'Stok awal saat pendaftaran obat',
        ]);
    }

    /**
     * Handle the Obat "updated" event.
     */
    public function updated(Obat $obat): void
    {
        if ($obat->isDirty('stok')) {
            $oldStock = $obat->getOriginal('stok');
            $newStock = $obat->stok;
            $diff = $newStock - $oldStock;

            $obat->stockHistories()->create([
                'type' => $diff > 0 ? 'in' : 'out',
                'quantity' => abs($diff),
                'stock_after' => $newStock,
                'description' => 'Perubahan stok',
            ]);
        }
    }

    /**
     * Handle the Obat "deleted" event.
     */
    public function deleted(Obat $obat): void
    {
        //
    }

    /**
     * Handle the Obat "restored" event.
     */
    public function restored(Obat $obat): void
    {
        //
    }

    /**
     * Handle the Obat "force deleted" event.
     */
    public function forceDeleted(Obat $obat): void
    {
        //
    }
}
