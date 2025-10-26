@props(['anggota'])

<x-modal-base title="Konfirmasi Hapus" maxWidth="sm" :scrollable="false" :showTitle="true">
    {{-- Trigger button --}}
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

    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        
        <h3 class="text-lg font-medium text-gray-900 mb-2">
            Hapus Data Anggota
        </h3>
        
        <p class="text-sm text-gray-500 mb-1">
            Apakah Anda yakin ingin menghapus data anggota:
        </p>
        
        <p class="text-base font-semibold text-gray-900 mb-4">
            {{ $anggota->nama ?? '-' }}
        </p>
        
        <p class="text-xs text-red-600 mb-6">
            Data yang sudah dihapus tidak dapat dikembalikan!
        </p>
    </div>

    {{-- Form Delete --}}
    <form method="POST" action="{{ route('anggota.destroy', $anggota->id) }}">
        @csrf
        @method('DELETE')
        <!-- Tambahkan ini untuk menyimpan current tahap_id -->
        <input type="hidden" name="tahap_id" value="{{ request('tahap_id') }}">
        
        <div class="flex w-full gap-2">
            <button type="button" @click="open = false"
                class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Batal
            </button>
            <button type="submit"
                class="flex-1 px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Ya, Hapus
            </button>
        </div>
    </form>
</x-modal-base>