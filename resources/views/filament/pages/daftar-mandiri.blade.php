<x-filament-panels::page>
    <x-filament-panels::form wire:submit="mendaftar">
        {{ $this->form }}
        
        <x-filament::button type="submit" size="lg" class="mt-4 w-full md:w-auto">
            Daftar Sekarang
        </x-filament::button>
    </x-filament-panels::form>
</x-filament-panels::page>
