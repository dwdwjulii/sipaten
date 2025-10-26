@props(['anggota', 'tahaps', 'currentTahapId'])

<x-modal-base title="Edit Anggota" maxWidth="md" :scrollable="true" :showTitle="true">
    <x-slot name="trigger">
        <button type="button" class="text-blue-600 hover:text-blue-900">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-square-pen">
                <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                <path
                    d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
            </svg>
        </button>
    </x-slot>

    <div class="sticky top-0 z-10 pb-2 bg-white border-b border-white">
        <p class="mb-1 text-xs text-gray-500">
            Ubah data anggota sesuai dengan informasi terbaru
        </p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 mb-3 text-red-700 bg-red-100 border border-red-400 rounded-md">
            <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
            <ul class="mt-2 text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('anggota.update', $anggota->id) }}" class="space-y-2">
        @csrf
        @method('PUT')
        <input type="hidden" name="current_tahap_id" value="{{ request('tahap_id') }}">
       
        <input type="hidden" name="tahap_id" value="{{ old('tahap_id', $anggota->tahap_id) }}">

        {{-- Nama --}}
        <div>
            <label class="block text-xs font-medium text-gray-800">Nama Anggota</label>
            <input type="text" name="nama" value="{{ old('nama', $anggota->nama) }}" required
                class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
        </div>

        {{-- Jenis Ternak --}}
        <div x-data="{ open: false, jenis: '{{ old('jenis_ternak', $anggota->jenis_ternak) }}' }" class="relative">
            <label class="block text-xs font-medium text-gray-800">Jenis Ternak</label>
            <input type="hidden" name="jenis_ternak" x-model="jenis">
            <button type="button" @click="open = !open"
                class="w-full px-2 py-2 mt-1 text-xs text-left text-gray-800 capitalize bg-white border border-gray-200 rounded-md">
                <span x-text="jenis"></span>
                <svg class="float-right w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                    :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" @click.away="open=false"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-md">
                <ul class="py-1 text-xs text-gray-700">
                    <li><a href="#" @click.prevent="jenis='Sapi'; open=false"
                            class="block px-3 py-1.5 hover:bg-green-100">Sapi</a></li>
                    <li><a href="#" @click.prevent="jenis='Kambing'; open=false"
                            class="block px-3 py-1.5 hover:bg-green-100">Kambing</a></li>
                </ul>
            </div>
        </div>

        @php
            // Kueri yang benar: mencari ternak dengan tipe 'Induk' dan status 'aktif'
            $activeInduks = $anggota->ternaks()
                                    ->where('tipe_ternak', 'Induk') // Menggunakan informasi dari controller Anda
                                    ->where('status_aktif', 'aktif')
                                    ->get();
            $activeIndukCount = $activeInduks->count();
        @endphp

         <input type="hidden" name="original_induk_count" value="{{ $activeIndukCount }}">

        <div x-data="{
            showTernakForm: {{ $activeIndukCount > 0 ? 'true' : 'false' }},
            jumlahInduk: {{ old('jumlah_induk', $activeIndukCount) }},
            hargaInduk: {{ json_encode(
                old('harga_induk', $activeInduks->pluck('harga')->map(function ($harga) {
                    return 'Rp ' . number_format($harga, 0, ',', '.');
                })->toArray()),
            ) }},
            formatRupiah(val) {
                if (!val) return 'Rp ';
                let number = String(val).replace(/\D/g, '');
                return 'Rp ' + number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }"
        x-init="$watch('jumlahInduk', (newVal, oldVal) => {
            const newSize = Number(newVal) || 0;
            const oldSize = hargaInduk.length;
            if (newSize > oldSize) {
                for (let i = oldSize; i < newSize; i++) {
                    hargaInduk.push('Rp '); // Tambah input harga baru
                }
            } else if (newSize < oldSize) {
                hargaInduk.splice(newSize); // Hapus input harga berlebih
            }
        })">

            {{-- Tampilan Notifikasi (jika tidak ada ternak) --}}
            <div x-show="!showTernakForm" x-transition>
                <div class="flex items-center justify-between w-full px-3 py-2 mt-2 text-xs text-yellow-800 bg-yellow-100 border border-yellow-300 rounded-md">
                    <span>Anggota ini tidak memiliki Induk aktif.</span>
                    <button type="button" @click="showTernakForm = true; jumlahInduk = 1;"
                        class="px-2 py-1 font-semibold text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        + Tambah Induk
                    </button>
                </div>
            </div>

            {{-- Tampilan Form Input Ternak --}}
            <div x-show="showTernakForm" x-transition class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-gray-800">Jumlah Induk</label>
                    
                    <div class="relative w-full mt-1"> 
                        
                        <input type="text" name="jumlah_induk" x-model.number="jumlahInduk" readonly
                            class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />

                        <div class="absolute inset-y-0 right-0 flex items-center gap-0.5 pr-1.5">
                            
                            <!-- Tombol Minus -->
                            <button type="button" 
                                    @click="if (jumlahInduk > 0) jumlahInduk--"
                                    :disabled="jumlahInduk <= 0"
                                    class="flex items-center justify-center w-4 h-4 mr-1 text-gray-600 bg-white border border-gray-300 rounded-full hover:bg-gray-100 active:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                </svg>
                            </button>
                            
                            <!-- Tombol Plus -->
                            <button type="button" 
                                    @click="jumlahInduk++"
                                    class="flex items-center justify-center w-4 h-4 text-white bg-blue-600 border border-blue-600 rounded-full hover:bg-blue-700 active:bg-blue-800 transition-colors">
                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                            
                        </div>
                    </div>
                </div>
                <template x-for="(harga, index) in Array.from({ length: jumlahInduk })" :key="index">
                    <div>
                        <label class="block text-xs font-medium text-gray-800"
                            x-text="`Harga Awal Induk ${index + 1}`"></label>
                        <input type="text" :name="`harga_induk[${index}]`" x-model="hargaInduk[index]"
                            x-on:input="hargaInduk[index] = formatRupiah($event.target.value)" placeholder="Rp " 
                            :required="showTernakForm"
                            class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
                    </div>
                </template>
            </div>
        </div>

        {{-- Tempat & Tanggal Lahir --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-800">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $anggota->tempat_lahir) }}"
                    required
                    class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-800">Tanggal Lahir</label>
                <input id="tanggal_lahir_edit_{{ $anggota->id }}" name="tanggal_lahir" type="text"
                    value="{{ old('tanggal_lahir', \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d/m/Y')) }}"
                    datepicker datepicker-format="dd/mm/yyyy" required
                    class="block w-full mt-1 text-xs text-gray-800 bg-white border-gray-200 rounded-md focus:ring-0 focus:border-green-500"
                    placeholder="DD/MM/YYYY">
            </div>
        </div>

        {{-- No HP & Status --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-800">No HP</label>
                <input type="tel" name="no_hp" value="{{ old('no_hp', $anggota->no_hp) }}" required
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,15)"
                    class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
            </div>
            <div x-data="{ open: false, status: '{{ old('status', $anggota->status) }}' }" class="relative">
                <label class="block text-xs font-medium text-gray-800">Status</label>
                <input type="hidden" name="status" x-model="status">
                <button type="button" @click="open = !open"
                    class="w-full px-2 py-2 mt-1 text-xs text-left text-gray-800 bg-white border border-gray-200 rounded-md">
                    <span x-text="status.replace('-', ' ')" class="capitalize"></span>
                    <svg class="float-right w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                        :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open=false"
                    class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-md">
                    <ul class="py-1 text-xs text-gray-700">
                        <li><a href="#" @click.prevent="status='aktif'; open=false"
                                class="block px-3 py-1.5 hover:bg-green-100">Aktif</a></li>
                        <li><a href="#" @click.prevent="status='non-aktif'; open=false"
                                class="block px-3 py-1.5 hover:bg-green-100">Non-Aktif</a></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Lokasi Kandang --}}
        <div>
            <label class="block text-xs font-medium text-gray-800">Lokasi Kandang</label>
            <input type="text" name="lokasi_kandang"
                value="{{ old('lokasi_kandang', $anggota->lokasi_kandang) }}" required
                class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
        </div>

        {{-- Footer --}}
        <div class="flex w-full gap-2 pt-2 sticky bottom-0 bg-white z-20">
            <button type="button" @click="open = false"
                class="flex-1 px-3 py-1.5 text-sm font-medium text-white bg-red-700 rounded-md hover:bg-red-800">
                Batal
            </button>
            <button type="submit"
                class="flex-1 px-3 py-1.5 text-sm font-medium text-white bg-blue-700 rounded-md hover:bg-blue-800">
                Simpan
            </button>
        </div>
    </form>
</x-modal-base>

{{-- SweetAlert --}}
@if (session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
@endif