<x-app-layout>
    <div class="py-6" x-data="{ filterOpen: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                     

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <h1 class="text-2xl font-bold text-gray-900 shrink-0">
                    Tugas Pencatatan
                </h1>

                {{-- Tombol toggle (mobile) --}}
                <button @click="filterOpen = !filterOpen"
                        class="md:hidden mt-2 w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium border rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M4 6h16M7 12h10M10 18h4"/>
                    </svg>
                    Filter & Search
                </button>

                {{-- Filter Desktop --}}
                <div class="hidden md:flex md:items-center md:justify-end md:gap-3 md:w-full">
                    <form action="{{ route('pencatatan.index') }}" method="GET" class="flex items-center gap-3">

                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-44 pl-4 pr-2 py-1.5 text-sm bg-white border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                                <span class="text-left truncate">
                                    {{ $tahapDipilih ? $tahapDipilih->label : 'Semua Tahap' }}
                                </span>
                                <svg class="w-5 h-5 text-gray-700 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition
                                x-cloak class="absolute z-10 mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg">
                                <ul class="max-h-56 overflow-y-auto text-sm text-gray-800">
                                    <li>
                                        <a href="{{ route('pencatatan.index', request()->except('tahap_id', 'page')) }}" class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                            Semua Tahap
                                        </a>
                                    </li>
                                    @foreach ($tahaps as $tahap)
                                        <li>
                                            <a href="{{ route('pencatatan.index', array_merge(request()->except('page'), ['tahap_id' => $tahap->id])) }}" class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                                {{ $tahap->label }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        {{-- Filter Status --}}
                        @php
                            $statusDipilihLabel = 'Semua Status'; // Default
                            if (request('status') == 'sudah_dicatat') {
                                $statusDipilihLabel = 'Sudah Dicatat';
                            } elseif (request('status') == 'belum_dicatat') {
                                $statusDipilihLabel = 'Belum Dicatat';
                            }
                        @endphp

                        <div x-data="{ open: false }" class="relative">
                            {{-- Tombol Trigger Dropdown --}}
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-between w-44 pl-4 pr-2 py-1.5 text-sm bg-white border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                                <span class="text-left truncate">
                                    {{ $statusDipilihLabel }}
                                </span>
                                <svg class="w-5 h-5 text-gray-700 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            {{-- Panel Dropdown --}}
                            <div x-show="open" @click.away="open = false" x-transition
                                x-cloak class="absolute z-10 mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg">
                                <ul class="max-h-56 overflow-y-auto text-sm text-gray-800 text-xs">
                                    {{-- Opsi 1: Semua Status --}}
                                    <li>
                                        <a href="{{ route('pencatatan.index', request()->except('status', 'page')) }}"
                                            class="block w-full text-left text-xs px-4 py-2 text-sm hover:bg-gray-100">
                                            Semua Status
                                        </a>
                                    </li>
                                    {{-- Opsi 2: Sudah Dicatat --}}
                                    <li>
                                        <a href="{{ route('pencatatan.index', array_merge(request()->except('page'), ['status' => 'sudah_dicatat'])) }}"
                                            class="block w-full text-left text-xs px-4 py-2 text-sm hover:bg-gray-100">
                                            Sudah Dicatat
                                        </a>
                                    </li>
                                    {{-- Opsi 3: Belum Dicatat --}}
                                    <li>
                                        <a href="{{ route('pencatatan.index', array_merge(request()->except('page'), ['status' => 'belum_dicatat'])) }}"
                                            class="block w-full text-left text-xs px-4 py-2 text-sm hover:bg-gray-100">
                                            Belum Dicatat
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Search Box --}}
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama anggota..."
                                class="w-52 pl-4 pr-8 py-1.5 text-sm border border-gray-200 placeholder:text-gray-800 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                            <button type="submit" class="absolute inset-y-0 right-0 px-3 text-gray-800 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Reset Button (Tidak berubah) --}}
                        <a href="{{ route('pencatatan.index') }}" 
                        class="px-3 py-1.5 text-sm text-gray-600 hover:text-blue-800 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            {{-- Filter Mobile --}}
            <div x-show="filterOpen" x-transition class="md:hidden mb-4">
                <form action="{{ route('pencatatan.index') }}" method="GET"
                    class="flex flex-col gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">

                    @php
                        // Menyiapkan data untuk Alpine.js, menambahkan 'Semua Tahap' di awal
                        $tahapOptions = $tahaps->mapWithKeys(function ($tahap) {
                            return [$tahap->id => $tahap->label];
                        })->prepend('Semua Tahap', '');
                    @endphp
                    <div x-data="{
                            open: false,
                            selectedTahap: '{{ request('tahap_id', '') }}',
                            tahapLabels: {{ json_encode($tahapOptions) }}
                        }" class="relative">

                        <input type="hidden" name="tahap_id" x-model="selectedTahap">

                        <button type="button" @click="open = !open"
                                class="flex items-center justify-between w-full pl-4 pr-2 py-1.5 text-sm bg-white border border-gray-200 rounded-lg">
                            <span x-text="tahapLabels[selectedTahap]" class="truncate"></span>
                            <svg class="w-5 h-5 text-gray-800 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                            <ul class="max-h-56 overflow-y-auto text-sm text-gray-800">
                                @foreach ($tahapOptions as $id => $label)
                                    <li>
                                        <button type="button" @click="selectedTahap = '{{ $id }}'; open = false" class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                            {{ $label }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    {{-- Filter Status Mobile (Tidak berubah) --}}
                    @php
                        // Menyiapkan data untuk 'Status'
                        $statusOptions = [
                            '' => 'Semua Status',
                            'sudah_dicatat' => 'Sudah Dicatat',
                            'belum_dicatat' => 'Belum Dicatat',
                        ];
                    @endphp
                    <div x-data="{
                            open: false,
                            selectedStatus: '{{ request('status', '') }}',
                            statusLabels: {{ json_encode($statusOptions) }}
                        }" class="relative">

                        <input type="hidden" name="status" x-model="selectedStatus">

                        <button type="button" @click="open = !open"
                            class="flex items-center justify-between w-full pl-4 pr-2 py-1.5 text-sm bg-white border border-gray-200 rounded-lg">
                            <span x-text="statusLabels[selectedStatus]" class="truncate"></span>
                            <svg class="w-5 h-5 text-gray-800 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                            <ul class="max-h-56 overflow-y-auto text-sm text-gray-800">
                                @foreach ($statusOptions as $id => $label)
                                    <li>
                                        <button type="button" @click="selectedStatus = '{{ $id }}'; open = false"
                                            class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                            {{ $label }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Search Box Mobile (Tidak berubah) --}}
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama anggota..."
                            class="w-full pl-4 pr-8 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                        <span class="absolute inset-y-0 right-0 px-3 flex items-center placeholder:text-gray-800 text-gray-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                    </div>

                    {{-- Action Buttons Mobile (Tidak berubah) --}}
                    <div class="flex justify-end items-center gap-3 mt-2">
                        <a href="{{ route('pencatatan.index') }}"
                            class="px-4 py-1.5 text-sm text-gray-600 hover:text-blue-800 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Reset
                        </a>
                        <button type="submit"
                                class="px-4 py-1.5 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Konten utama --}}
            <div class="bg-white shadow rounded-sm p-4 sm:p-6">
                @if($locked ?? false)
                    {{-- Sudah diarsip --}}
                    <div class="text-center py-16 bg-white border-2 border-dashed rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Periode ini sudah diarsip</h3>
                        <p class="mt-1 text-sm text-gray-500">Tunggu admin memulai pencatatan baru bulan ini.</p>
                    </div>
                @else
                    @forelse ($anggotas as $anggota)
                        @if($loop->first)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        @endif

                        {{-- Card Anggota --}}
                        {{-- Ganti seluruh card Anda dengan kode di bawah ini --}}
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-500 transition-all duration-300 flex flex-col">

                            {{-- ======================================================= --}}
                            {{-- BAGIAN HEADER CARD --}}
                            {{-- ======================================================= --}}
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">
                                    {{ $anggota->tahap?->label ?? 'Tanpa Tahap' }}
                                </span>

                                {{-- 🚩 PERBAIKAN: Logika Status yang Konsisten dengan Controller --}}
                                @if($anggota->latestPencatatan)
                                    @if($anggota->latestPencatatan->is_locked)
                                        {{-- 1. Status Arsip (Terkunci) --}}
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">
                                            Arsip
                                        </span>
                                    @else
                                        {{-- 2. Status Aktif (Belum Terkunci) --}}
                                        @php
                                            $pencatatan = $anggota->latestPencatatan;
                                            
                                            // Hitung ternak aktif SAAT INI (real-time)
                                            $jumlahTernakAktifSekarang = $anggota->ternaks()
                                                ->where('status_aktif', 'aktif')
                                                ->count();
                                            
                                            // Hitung detail yang sudah dicatat DAN ternaknya masih aktif
                                            $jumlahDetailAktifTercatat = 0;
                                            $pernahAdaDetail = false;
                                            
                                            if ($pencatatan) {
                                                // Cek apakah pernah ada detail yang terisi
                                                $pernahAdaDetail = $pencatatan->details->filter(function($detail) {
                                                    return !empty($detail->kondisi_ternak);
                                                })->isNotEmpty();
                                                
                                                // Hitung detail yang terisi DAN ternaknya masih aktif
                                                $jumlahDetailAktifTercatat = $pencatatan->details->filter(function($detail) {
                                                    return !empty($detail->kondisi_ternak) && 
                                                        $detail->ternak && 
                                                        $detail->ternak->status_aktif === 'aktif';
                                                })->count();
                                            }
                                        @endphp

                                        @if($jumlahTernakAktifSekarang > 0 && !$pernahAdaDetail)
                                            {{-- ERROR: Ada ternak aktif tapi belum pernah dicatat sama sekali --}}
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                                Belum Dicatat
                                            </span>
                                        @elseif($pernahAdaDetail && $jumlahDetailAktifTercatat < $jumlahTernakAktifSekarang)
                                            {{-- WARNING: Pernah dicatat tapi ada ternak baru yang belum tercatat --}}
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-orange-100 text-orange-800">
                                                Perlu Update
                                            </span>
                                        @else
                                            {{-- SUCCESS: Semua lengkap atau tidak ada ternak aktif --}}
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                                Sudah Dicatat
                                            </span>
                                        @endif
                                    @endif
                                @else
                                    {{-- 3. Tidak ada placeholder pencatatan sama sekali --}}
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                                        Tidak Ada Data
                                    </span>
                                @endif
                            </div>

                            {{-- ======================================================= --}}
                            {{-- BAGIAN INFO CARD --}}
                            {{-- ======================================================= --}}
                            <div class="p-4 flex-grow">
                                <p class="text-base font-bold text-gray-800 line-clamp-2 leading-tight mb-3">
                                    {{ $anggota->nama }}
                                </p>
                                <div class="space-y-2 text-sm text-gray-600">
                                    {{-- Info Jenis Ternak --}}
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                        </svg>
                                        <span>Jenis: <span class="font-semibold">{{ Str::ucfirst($anggota->jenis_ternak) }}</span></span>
                                    </div>
                                    {{-- Info Lokasi --}}
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $anggota->lokasi_kandang }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ======================================================= --}}
                            {{-- BAGIAN TOMBOL CARD --}}
                            {{-- ======================================================= --}}
                            <div class="p-4 mt-auto border-t rounded-b-lg border-gray-200 bg-gray-50">
                                {{-- 🚩 PERBAIKAN 2: Gunakan latestPencatatan secara konsisten di sini juga --}}
                                @if($anggota->latestPencatatan)
                                    @if($anggota->latestPencatatan->is_locked)
                                        <button disabled class="block w-full text-center px-3 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                                            Terkunci
                                        </button>
                                    @elseif($anggota->latestPencatatan->details->isEmpty())
                                        <a href="{{ route('pencatatan.create', $anggota) }}" class="block w-full text-center px-3 py-2 text-sm font-semibold text-white bg-green-600 rounded-md hover:bg-green-700">
                                            + Buat Catatan
                                        </a>
                                    @else
                                        <a href="{{ route('pencatatan.edit', $anggota->latestPencatatan) }}" class="block w-full text-center px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                            Lihat / Edit Catatan
                                        </a>
                                    @endif
                                @else
                                    {{-- Kondisi ini idealnya tidak akan pernah terjadi jika Reset berfungsi benar,
                                        tapi sebagai pengaman, kita tetap berikan tombol Create --}}
                                    <a href="{{ route('pencatatan.create', $anggota) }}" class="block w-full text-center px-3 py-2 text-sm font-semibold text-white bg-green-600 rounded-md hover:bg-green-700">
                                        + Buat Catatan
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($loop->last)
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-16 bg-white border-2 border-dashed rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak Ada Tugas</h3>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada data anggota yang cocok dengan filter Anda.</p>
                        </div>
                    @endforelse

                    @if($anggotas->count() > 0)
                        <div class="mt-6">
                            {{ $anggotas->appends(request()->query())->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
