<x-modal-base maxWidth="sm" :scrollable="false" :showTitle="false">
    <x-slot name="trigger">
        <div class="cursor-pointer">
            {{ $slot }}
        </div>
    </x-slot>

    {{-- Wrapper agar X bisa di pojok kanan atas --}}
    <div class="relative">
        {{-- Tombol Close --}}
        <button @click="open = false" class="absolute -top-2 -right-2 text-gray-400 hover:text-gray-600 p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Header --}}
        <h3 class="text-lg font-semibold text-gray-800 text-left pb-1">Lihat Laporan</h3>

        {{-- Body --}}
        <div class="text-left">
            <p class="text-xs text-gray-600 mb-5">
                Silakan pilih tombol <span class="font-medium text-gray-800">Lihat Laporan</span>
                untuk meninjau arsip catatan ternak.
            </p>
            <img src="{{ asset('asset/images/laporan.png') }}" alt="Ikon Laporan"
                class="mx-auto w-20 h-20 object-contain mb-5" />
        </div>
    </div>

    {{-- Footer --}}
    <x-slot name="footer">
        <button class="w-full bg-red-700 hover:bg-red-800 text-white font-medium py-2 rounded-md transition">
            Lihat Laporan
        </button>
    </x-slot>
</x-modal-base>