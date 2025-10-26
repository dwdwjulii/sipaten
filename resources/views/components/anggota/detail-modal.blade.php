@props(['anggota'])

<x-modal-base title="Detail Anggota" maxWidth="lg" :scrollable="true" :showTitle="true">
    <x-slot name="trigger">
        {{-- Tombol Detail --}}
        <button type="button" class="text-yellow-500 hover:text-yellow-700">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-eye">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
        </button>
    </x-slot>

    <div class="space-y-6">
        {{-- Header Card dengan Avatar --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-white">
                            {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $anggota->nama }}</h2>
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi Personal --}}
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Personal
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-medium text-gray-500">Tempat, Tanggal Lahir</span>
                        <span class="text-sm text-gray-900 text-right">
                            {{ $anggota->tempat_lahir }},
                            {{ \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d F Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">No. HP</span>
                        <span class="text-sm text-gray-900">{{ $anggota->no_hp }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Tahap</span>
                        <span class="text-sm font-semibold text-blue-600">{{ $anggota->tahap->label ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="size-5 mr-2 text-green-500">
                        <path
                            d="M15.5 2A1.5 1.5 0 0 0 14 3.5v13a1.5 1.5 0 0 0 1.5 1.5h1a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 16.5 2h-1ZM9.5 6A1.5 1.5 0 0 0 8 7.5v9A1.5 1.5 0 0 0 9.5 18h1a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 10.5 6h-1ZM3.5 10A1.5 1.5 0 0 0 2 11.5v5A1.5 1.5 0 0 0 3.5 18h1A1.5 1.5 0 0 0 6 16.5v-5A1.5 1.5 0 0 0 4.5 10h-1Z" />
                    </svg>
                    Informasi Ternak
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Jenis Ternak</span>
                        <span class="text-sm font-medium text-gray-900 capitalize">{{ $anggota->jenis_ternak }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">Jumlah Induk</span>
                        <span class="text-sm font-bold text-green-600">{{ $anggota->ternaks->where('tipe_ternak', 'Induk')->count() }} ekor</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="text-sm font-medium text-gray-500">Lokasi Kandang</span>
                        <span class="text-sm text-gray-900 text-right max-w-32">{{ $anggota->lokasi_kandang }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Harga Induk --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    class="size-5 mr-2 text-yellow-500">
                    <path
                        d="M10.75 10.818v2.614A3.13 3.13 0 0 0 11.888 13c.482-.315.612-.648.612-.875 0-.227-.13-.56-.612-.875a3.13 3.13 0 0 0-1.138-.432ZM8.33 8.62c.053.055.115.11.184.164.208.16.46.284.736.363V6.603a2.45 2.45 0 0 0-.35.13c-.14.065-.27.143-.386.233-.377.292-.514.627-.514.909 0 .184.058.39.202.592.037.051.08.102.128.152Z" />
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-6a.75.75 0 0 1 .75.75v.316a3.78 3.78 0 0 1 1.653.713c.426.33.744.74.925 1.2a.75.75 0 0 1-1.395.55 1.35 1.35 0 0 0-.447-.563 2.187 2.187 0 0 0-.736-.363V9.3c.698.093 1.383.32 1.959.696.787.514 1.29 1.27 1.29 2.13 0 .86-.504 1.616-1.29 2.13-.576.377-1.261.603-1.96.696v.299a.75.75 0 1 1-1.5 0v-.3c-.697-.092-1.382-.318-1.958-.695-.482-.315-.857-.717-1.078-1.188a.75.75 0 1 1 1.359-.636c.08.173.245.376.54.569.313.205.706.353 1.138.432v-2.748a3.782 3.782 0 0 1-1.653-.713C6.9 9.433 6.5 8.681 6.5 7.875c0-.805.4-1.558 1.097-2.096a3.78 3.78 0 0 1 1.653-.713V4.75A.75.75 0 0 1 10 4Z"
                        clip-rule="evenodd" />
                </svg>
                Rincian Harga Induk
            </h3>

            @if ($anggota->ternaks->count())
            <div class="space-y-2 mb-4">
                @foreach ($anggota->ternaks as $index => $ternak)
                <div class="flex justify-between items-center py-3 px-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold text-blue-600">{{ $index + 1 }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Induk {{ $index + 1 }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-base font-bold text-gray-900">
                            Rp {{ number_format($ternak->harga, 0, ',', '.') }}
                        </span>
                        @if($ternak->kode_eartag)
                        <div class="text-xs text-gray-500">{{ $ternak->kode_eartag }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            

            {{-- Total Harga --}}
            <div class="border-t border-gray-200 pt-4">
                <div
                    class="flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4">
                    <div>
                        <span class="text-sm font-medium text-gray-600">Total Modal Awal</span>
                        <div class="text-xs text-gray-500">{{ $anggota->ternaks->count() }} ekor induk ternak</div>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-bold text-blue-600">
                            Rp {{ number_format($anggota->ternaks->sum('harga'), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <p class="text-gray-500 text-sm">Belum ada data ternak untuk anggota ini.</p>
                <p class="text-xs text-gray-400 mt-1">Tambahkan data ternak melalui form edit anggota</p>
            </div>
            @endif
        </div>

        {{-- Timeline atau Status Info --}}
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-4">
            <div class="flex items-center justify-between text-xs text-gray-600">
                <span>Dibuat: {{ \Carbon\Carbon::parse($anggota->created_at)->format('d M Y H:i') }}</span>
                <span>Diperbarui: {{ \Carbon\Carbon::parse($anggota->updated_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- Footer Actions --}}
    <div class="flex justify-end space-x-3 pt-4 sticky bottom-0 bg-white">
        <button type="button" @click="open = false"
            class="px-4 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-blue-500">
            Tutup
        </button>
    </div>

</x-modal-base>