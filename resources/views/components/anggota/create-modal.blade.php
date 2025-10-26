@props(['tahapId'])

<x-modal-base title="Tambah Anggota" maxWidth="md" :scrollable="true" :showTitle="true">
    <x-slot name="trigger">
        <button
             class="inline-flex items-center px-2 py-1.5 text-sm font-medium text-white bg-green-700 border border-transparent rounded-lg hover:bg-green-800">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1">
                <path
                    d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Tambah Anggota
        </button>
    </x-slot>

    <div class="sticky top-0 z-10 pb-2 bg-white border-b border-white">
        <p class="mb-1 text-xs text-gray-500">
            Isi data anggota dengan lengkap untuk menambahkan data anggota
        </p>
    </div>

    {{-- Error message --}}
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

    {{-- Form Anggota --}}
    <form method="POST" action="{{ route('anggota.store') }}" class="space-y-2">
        @csrf
        <input type="hidden" name="tahap_id" value="{{ $tahapId }}">

        {{-- Nama --}}
        <div>
            <label class="block text-xs font-medium text-gray-800">Nama Anggota</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required
                class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
        </div>

        {{-- Jenis Ternak --}}
        <div x-data="{ open: false, jenis: '{{ old('jenis_ternak') ? old('jenis_ternak') : '' }}' }" class="relative">
            <label class="block text-xs font-medium text-gray-800">Jenis Ternak</label>

            <!-- input tersembunyi untuk dikirim ke server -->
            <input type="hidden" name="jenis_ternak" x-model="jenis">

            <!-- tombol dropdown -->
            <button type="button" @click="open = !open"
                class="w-full px-2 py-2 mt-1 text-xs text-left text-gray-800 capitalize bg-white border border-gray-200 rounded-md">
                <span x-text="jenis ? jenis : 'Pilih jenis ternak'" 
                    :class="jenis ? 'text-gray-800' : 'text-gray-400'"></span>

                <svg class="float-right w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                    :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- daftar pilihan -->
            <div x-show="open" @click.away="open=false"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-md">
                <ul class="py-1 text-xs text-gray-700">
                    <li>
                        <a href="#" @click.prevent="jenis='sapi'; open=false"
                            class="block px-3 py-1.5 hover:bg-green-100">Sapi</a>
                    </li>
                    <li>
                        <a href="#" @click.prevent="jenis='kambing'; open=false"
                            class="block px-3 py-1.5 hover:bg-green-100">Kambing</a>
                    </li>
                </ul>
            </div>
        </div>


        {{-- Jumlah & Harga Induk --}}
        <div 
            x-data="{
                jumlahInduk: {{ old('jumlah_induk', 1) }},
                // Inisialisasi hargaInduk sebagai array kosong
                hargaInduk: [],
                
                // Fungsi init akan dijalankan pertama kali oleh Alpine
                init() {
                    // Ambil data 'old' dari PHP dan ubah menjadi array JavaScript
                    const oldTernak = @json(old('ternak_awal', []));
                    // Atur panjang array hargaInduk agar sesuai dengan jumlahInduk
                    this.updateHargaArray();
                    // Isi array hargaInduk dengan nilai 'old' jika ada
                    oldTernak.forEach((ternak, index) => {
                        if (this.hargaInduk[index] !== undefined) {
                            this.hargaInduk[index] = ternak.harga || 0;
                        }
                    });
                },
                
                // Fungsi untuk menyesuaikan panjang array harga
                updateHargaArray() {
                    const currentLength = this.hargaInduk.length;
                    if (currentLength < this.jumlahInduk) {
                        // Tambahkan elemen baru jika jumlahInduk bertambah
                        for (let i = currentLength; i < this.jumlahInduk; i++) {
                            this.hargaInduk.push(0);
                        }
                    } else if (currentLength > this.jumlahInduk) {
                        // Potong array jika jumlahInduk berkurang
                        this.hargaInduk.splice(this.jumlahInduk);
                    }
                },

                formatRupiah(number) {
                    if (number === null || number === '' || isNaN(number) || number === 0) return '';
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
                },

                updateHarga(event, index) {
                    let rawValue = event.target.value;
                    let cleanedValue = rawValue.replace(/\D/g, '');
                    this.hargaInduk[index] = cleanedValue ? parseInt(cleanedValue) : 0;
                    
                    this.$nextTick(() => {
                        event.target.value = this.formatRupiah(this.hargaInduk[index]);
                    });
                }
            }" 
            class="grid grid-cols-1 md:grid-cols-2 gap-3"
            x-init="init()"
        >
            {{-- Input jumlah induk --}}
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

            {{-- Input harga induk dinamis sesuai jumlah --}}
           
            <template x-for="index in jumlahInduk" :key="index">
                <div>
                    <label 
                        class="block text-xs font-medium text-gray-800" 
                        x-text="`Harga Induk Awal ${index}`"
                    ></label>

                    {{-- Input tampilan (terformat Rupiah) --}}
                    <input 
                        type="text" 
                        :value="formatRupiah(hargaInduk[index - 1])"
                        @input="updateHarga($event, index - 1)"
                        placeholder="Rp 0"
                        class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500"
                    >

                    {{-- Input hidden (nilai angka bersih untuk server) --}}
                    <input 
                        type="hidden" 
                        :name="`ternak_awal[${index - 1}][harga]`" 
                        :value="hargaInduk[index - 1] || 0"
                    >
                </div>
            </template>
            
        </div>

        {{-- Tempat & Tanggal Lahir --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-800">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                    class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-800">Tanggal Lahir</label>
                <input id="tanggal_lahir" name="tanggal_lahir" type="text" value="{{ old('tanggal_lahir') }}"
                    datepicker datepicker-format="dd/mm/yyyy" required
                    class="block w-full mt-1 text-xs text-gray-800 bg-white border-gray-200 rounded-md focus:ring-0 focus:border-green-500"
                    placeholder="DD/MM/YYYY">
            </div>
        </div>

        {{-- No HP & Status --}}
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-800">No HP</label>
                <input type="tel" name="no_hp" value="{{ old('no_hp') }}" required
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,15)"
                    class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
            </div>
            <div x-data="{ open: false, status: '{{ old('status', 'aktif') }}' }" class="relative">
                <label class="block text-xs font-medium text-gray-800">Status</label>
                <input type="hidden" name="status" x-model="status">
                <button type="button" @click="open = !open"
                    class="w-full px-2 py-2 mt-1 text-xs text-left text-gray-800 bg-white border border-gray-200 rounded-md">
                    <span x-text="status.replace('-', ' ')" class="capitalize"></span>
                    <svg class="float-right w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                        :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open=false" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-md">
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
            <input type="text" name="lokasi_kandang" value="{{ old('lokasi_kandang') }}" required
                class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
        </div>

        {{-- Footer (dalam form, agar submit berfungsi) --}}
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


