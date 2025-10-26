{{-- File: resources/views/components/arsip-bulan/delete-modal.blade.php --}}
@props(['name' => 'Arsip-Bulan'])

<x-modal-base maxWidth="sm" :scrollable="false" :showTitle="false">
    {{-- Trigger Button --}}
    <x-slot name="trigger">
        <button type="button" class="text-red-600 hover:text-red-900">
            {{-- Icon Trash --}}
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                <path d="M3 6h18" />
                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
            </svg>
        </button>
    </x-slot>

    {{-- Isi Modal --}}
    <p class="text-gray-700 text-sm whitespace-normal break-words">
        Apakah Anda yakin ingin menghapus <span class="font-medium text-gray-900">{{ $name }}</span>?
    </p>

    {{-- Footer --}}
    <x-slot name="footer">
        <div class="flex gap-2 w-full">
            <button type="button" @click="open = false"
                class="flex-1 px-3 py-1.5 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-100">
                Batal
            </button>
            <button type="button"
                class="flex-1 px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                Ya, Hapus
            </button>
        </div>
    </x-slot>
</x-modal-base>