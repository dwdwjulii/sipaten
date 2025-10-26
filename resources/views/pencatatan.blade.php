{{-- resources/views/pencatatan.blade.php --}}

<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-4">

            {{-- NOTIFIKASI STATUS DINAMIS --}}
            <div class="mb-4 space-y-3">

                @if($statusKeseluruhan == 'error')
                    {{-- Case 1: Masih ada catatan kosong (MERAH) --}}
                    <x-alert-status type="error" title="Status Catatan Ternak: Perlu Tindakan!" :messages="[
                        ['text' => 'Masih terdapat laporan data catatan ternak yang belum dilengkapi oleh Petugas Lapangan.', 'status' => 'error']
                    ]" />
                
                @elseif($statusKeseluruhan == 'warning')
                    {{-- Case 2: Ada catatan kurang / "Perlu Update" (ORANYE) --}}
                    <x-alert-status type="warning" title="Status Catatan: Perlu Update" :messages="[
                        ['text' => 'Beberapa data ternak baru ditambahkan oleh Admin dan belum dicatat oleh Petugas. Harap petugas melengkapi data tersebut sebelum arsip.', 'status' => 'warning']
                    ]" />
                
                @elseif($statusKeseluruhan == 'success' && !$statusArsip)
                    {{-- Case 3: Semua catatan sudah lengkap (HIJAU) --}}
                    <x-alert-status type="success" title="Status Catatan: Semua Anggota Sudah Dicatat"
                        messages="Semua catatan ternak telah lengkap untuk periode bulan ini." />
                    
                    <x-alert-status type="warning" title="Tindakan Diperlukan: Arsipkan Laporan"
                        messages="Segera arsipkan laporan agar data tersimpan dengan aman dan siap untuk periode berikutnya." />
                
                @elseif($statusKeseluruhan == 'success' && $statusArsip)
                    {{-- Case 4: Semua sudah diarsip, siap mulai periode baru --}}
                    <div class="flex rounded-sm overflow-hidden bg-emerald-50">

                    {{-- Sidebar kiri penuh --}}
                    <div class="flex items-center justify-center bg-green-500 px-2">
                        <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 
                            00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 
                            00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    {{-- Konten teks + tombol --}}
                    <div class="p-4 flex-1">
                        <h2 class="text-lg font-semibold text-green-900">Status Pencatatan</h2>
                        <div class="mt-2 text-sm text-green-900">
                            <p>Data catatan bulan ini telah diarsipkan dan tidak dapat diubah. Mulai periode pencatatan
                                baru dengan klik tombol di bawah.</p>
                        </div>
                        <div class="mt-4">
                            <form action="{{ route('pencatatan.reset') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mr-1" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-clipboard-list">
                                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                        <path
                                            d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                        <path d="M12 11h4" />
                                        <path d="M12 16h4" />
                                        <path d="M8 11h.01" />
                                        <path d="M8 16h.01" />
                                    </svg>
                                    Mulai Pencatatan
                                </button>
                            </form>
                        </div>
                    </div>
                    </div>
                @endif
            </div>

            {{-- CARD TABEL CATATAN --}}
            <div class="p-6 bg-white shadow rounded-sm">

                {{-- Header Konten --}}
                <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Kelola Catatan</h2>

                    <div class="flex items-center gap-2">
                        {{-- Search --}}
                        <x-search-bar />

                        {{-- BAGIAN 2: DROPDOWN TAHAP DINAMIS --}}
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open"
                                class="flex items-center justify-between w-36 px-3 py-1.5 space-x-1 text-sm font-normal text-gray-800 bg-white border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                                <span class="text-left break-words whitespace-normal">
                                    {{ $tahapDipilih ? $tahapDipilih->label : 'Semua Tahap' }}
                                </span>
                                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                                    :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition x-cloak
                                class="absolute z-50 w-36 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg">
                                <ul class="max-h-60 overflow-y-auto text-xs text-gray-700">
                                    <li>
                                        <a href="{{ route('pencatatan.index') }}"
                                            class="block px-3 py-2 text-xs hover:bg-green-100 hover:rounded-md">
                                            Semua Tahap
                                        </a>
                                    </li>
                                    @foreach($tahaps as $tahap)
                                    <li>
                                        <a href="{{ route('pencatatan.index', ['tahap_id' => $tahap->id]) }}"
                                            class="block px-3 py-2 break-words text-xs hover:bg-green-100 hover:rounded-lg whitespace-normal @if($tahapDipilih && $tahapDipilih->id == $tahap->id) bg-green-100 @endif ">
                                            {{ $tahap->label }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Ganti bagian dari baris 118 sampai 175 dengan kode di bawah ini --}}

                        {{-- =============================================================== --}}
                        {{-- BAGIAN 3: TOMBOL EXPORT & ARSIP --}}
                        {{-- =============================================================== --}}
                        @php
                            $semuaTercatat = $statusKeseluruhan === 'success';
                        @endphp

                        <a href="{{ route('laporan.keseluruhan.export') }}" target="_blank"
                            class="flex items-center justify-center px-2.5 py-1.5 space-x-1 text-sm font-medium rounded-lg 
                            {{ $semuaTercatat ? 'text-white bg-green-600 hover:bg-green-700' : 'text-gray-400 bg-gray-200 cursor-not-allowed' }}"
                            {{ $semuaTercatat ? '' : 'aria-disabled=true tabindex=-1' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 17V3m-6 8l6 6l6-6m-9 9h6" />
                            </svg>
                            <span>Export</span>
                        </a>

                        <button type="button" id="btnArsip"
                            class="flex items-center justify-center px-3 py-1.5 space-x-1 text-sm font-medium rounded-lg w-20 
                            {{ $semuaTercatat ? 'text-white bg-yellow-500 hover:bg-yellow-700' : 'text-gray-400 bg-gray-200 cursor-not-allowed' }}"
                            {{ $semuaTercatat ? '' : 'disabled' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-archive-icon lucide-archive">
                                <rect width="20" height="5" x="2" y="3" rx="1" />
                                <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                                <path d="M10 12h4" />
                            </svg>
                            <span>Arsip</span>
                        </button>

                        <form id="formArsip" action="{{ route('laporan.arsip.keseluruhan') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>

                {{-- =============================================================== --}}
                {{-- BAGIAN 4: TABEL DATA DINAMIS --}}
                {{-- =============================================================== --}}
                <form action="{{ url()->current() }}" method="GET"> 
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs text-gray-600 border border-gray-200">
                            <thead class="text-sm text-gray-500 bg-gray-100">
                                <tr>
                                    
                                    <th scope="col" class="w-12 px-4 py-3 text-center border border-gray-200">No</th>
                                    <th scope="col" class="px-4 py-3 border border-gray-200">Nama Anggota</th>
                                    <th scope="col" class="px-4 py-3 text-center border border-gray-200">Tahap</th>
                                    <th scope="col" class="px-4 py-3 text-center border border-gray-200">Jenis Ternak</th>
                                    <th scope="col" class="px-4 py-3 border border-gray-200">Total Harga Induk</th>
                                    <th scope="col" class="px-4 py-3 border border-gray-200">Lokasi Kandang</th>
                                    <th scope="col" class="px-4 py-3 text-center border border-gray-200">Tanggal Catatan</th>
                                    
                                    {{-- PERUBAHAN DI SINI: Mengganti dropdown dengan ikon filter --}}
                                    <th scope="col" class="relative px-4 py-3 text-center border border-gray-200">
                                        <div x-data="{ open: false }" @click.away="open = false" class="relative inline-block text-left">
                                            <button @click="open = !open" type="button" class="inline-flex items-center justify-center w-full text-sm">
                                                <span>Status Laporan</span>
                                                <svg class="w-5 h-5 ml-1 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                    
                                            <div x-show="open"
                                                x-transition
                                                class="absolute right-0 z-20 w-32 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5"
                                                style="display: none;">
                                                <div class="py-1">
                                                    @php
                                                        // Helper untuk membuat URL filter sambil mempertahankan parameter lain
                                                        function build_filter_url($key, $value) {
                                                            $query = array_merge(request()->query(), [$key => $value, 'page' => 1]);
                                                            if (empty($value)) {
                                                                unset($query[$key]);
                                                            }
                                                            return url()->current() . '?' . http_build_query($query);
                                                        }
                                                    @endphp
                                                    <a href="{{ build_filter_url('status_laporan', '') }}" class="block w-full px-4 py-2 text-xs text-left text-gray-700 hover:bg-gray-100 {{ !request('status_laporan') ? 'bg-gray-100 font' : '' }}">
                                                        Semua Laporan
                                                    </a>
                                                    <a href="{{ build_filter_url('status_laporan', 'sudah') }}" class="block w-full px-4 py-2 text-xs text-left text-gray-700 hover:bg-gray-100 {{ request('status_laporan') == 'sudah' ? 'bg-gray-100' : '' }}">
                                                        Sudah Dicatat
                                                    </a>
                                                    <a href="{{ build_filter_url('status_laporan', 'belum') }}" class="block w-full px-4 py-2 text-xs text-left text-gray-700 hover:bg-gray-100 {{ request('status_laporan') == 'belum' ? 'bg-gray-100' : '' }}">
                                                        Belum Dicatat
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center border border-gray-200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($anggotas as $anggota)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-2 py-3 text-center border border-gray-100 text-gray-900 whitespace-nowrap">
                                        {{ $loop->iteration + ($anggotas->currentPage() - 1) * $anggotas->perPage() }}
                                    </td>
                                    <td class="w-[18%] px-2 py-3 border border-gray-100 text-gray-900 text-left truncate max-w-[250px]">
                                        {{ $anggota->nama }}
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap text-gray-900 border border-gray-100">
                                        {{ $anggota->tahap?->label ?? '-' }}
                                    </td>
                                    <td class="px-2 py-3 text-center border border-gray-100 text-gray-900 whitespace-nowrap">
                                        {{ $anggota->jenis_ternak }}
                                    </td>
                                    <td class="px-2 py-3 text-left border border-gray-100 text-gray-900 whitespace-nowrap">
                                        Rp {{ number_format($anggota->total_harga_induk ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900 text-left truncate max-w-[150px]">
                                        {{ $anggota->lokasi_kandang }}
                                    </td>
                                    <td class="px-2 py-3 text-center border border-gray-100 text-gray-900 whitespace-nowrap">
                                        {{ $anggota->latestPencatatan?->tanggal_catatan?->format('d M Y') ?? 'Belum ada' }}
                                    </td>
                                    <td class="px-2 py-3 text-center border border-gray-100 whitespace-nowrap">
                                        @php
                                            $pencatatan = $anggota->latestPencatatan;
                                            $jumlahTernakAktif = $anggota->ternaks_count ?? 0;
                                            $jumlahDetailLengkap = 0;
                                            $detailsExistAndFilled = false; // Flag baru

                                            if ($pencatatan) {
                                                $filledDetails = $pencatatan->details->filter(function($detail) {
                                                    // Hanya hitung jika kondisi terisi (bukan '')
                                                    return !empty($detail->kondisi_ternak);
                                                });
                                                $detailsExistAndFilled = $filledDetails->isNotEmpty(); // Cek apakah ada yg terisi

                                                // Hitung detail lengkap (terisi DAN ternak masternya aktif)
                                                $jumlahDetailLengkap = $filledDetails->filter(function($detail) {
                                                    return $detail->ternak && $detail->ternak->status_aktif === 'aktif';
                                                })->count();
                                            }
                                        @endphp

                                        @if($pencatatan && $pencatatan->is_locked)
                                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                                Diarsipkan
                                            </span>

                                        @elseif($pencatatan && $detailsExistAndFilled && $jumlahTernakAktif > 0 && $jumlahDetailLengkap < $jumlahTernakAktif)
                                            <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full">
                                                Perlu Update
                                            </span>

                                        @elseif($pencatatan && $detailsExistAndFilled)
                                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                Sudah Dicatat
                                            </span>

                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                                Belum Dicatat
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center border border-gray-100">
                                        @if ($anggota->latestPencatatan)
                                            <x-pencatatan.detail-modal :pencatatan="$anggota->latestPencatatan" />
                                        @else
                                            <button disabled class="text-gray-300 cursor-not-allowed" title="Belum ada catatan untuk dilihat">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" /><circle cx="12" cy="12" r="3" /></svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="py-6 text-center text-gray-500">
                                        Tidak ada data anggota yang ditemukan untuk filter ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>


                {{-- =============================================================== --}}
                {{-- BAGIAN 5: PAGINATION --}}
                {{-- =============================================================== --}}
                {{-- Ganti blok div pagination lama Anda dengan ini --}}
                <div class="flex items-center justify-between mt-4 text-sm text-gray-600">
                    
                    {{-- Komponen dropdown jumlah entri --}}
                    <x-show-entries />

                    {{-- Teks "Menampilkan X sampai Y dari Z hasil" --}}
                    @if ($anggotas->total() > 0)
                    <div>
                        Menampilkan 
                        <span class="font-bold">{{ $anggotas->firstItem() }}</span>
                        sampai 
                        <span class="font-bold">{{ $anggotas->lastItem() }}</span>
                        dari 
                        <span class="font-bold">{{ $anggotas->total() }}</span>
                        hasil
                    </div>
                    @endif

                    {{-- Tombol navigasi halaman --}}
                    <div>
                        {{ $anggotas->appends(request()->query())->links() }}
                    </div>

                </div>
                {{-- Selesai mengganti blok div pagination --}}
            </div>
        </div>
    </div>

   

    @push('scripts')
    {{-- 1. Muat library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- 2. Jalankan kode kustom --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk tombol Arsip
        const btnArsip = document.getElementById('btnArsip');
        if (btnArsip) {
            btnArsip.addEventListener('click', function() {
                Swal.fire({
                    title: 'Arsipkan Laporan?',
                    text: "Pastikan semua data benar. Laporan akan dikunci setelah diarsipkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                            customClass: {
                                confirmButton: 'swal-button-konfirmasi-green', 
                                cancelButton: 'swal-button-batal'
                            },
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formArsip').submit();
                    }
                });
            });
        }

        // --- PENANGANAN ALERT DINAMIS DARI CONTROLLER ---

        @if(session('arsip_gagal'))
            var data = @json(session('arsip_gagal'));
            var listHtml = '<ul style="text-align: left; list-style-position: inside; padding-left: 20px;">';
            data.list.forEach(function(nama) { listHtml += '<li>' + nama + '</li>'; });
            listHtml += '</ul>';
            Swal.fire({ icon: 'error', title: data.title, html: '<p>' + data.text + '</p>' + listHtml, confirmButtonText: 'Saya Mengerti' });
        @endif

        @if(session('arsip_sukses'))
            var data = @json(session('arsip_sukses'));
            Swal.fire({
                icon: 'success', title: data.title, text: data.text,
                confirmButtonText: 'Mulai Catatan Baru', showCancelButton: true, cancelButtonText: 'Tutup', buttonsStyling: false,
                customClass: {
                    confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700',
                    cancelButton: 'px-4 py-2 ml-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('pencatatan.mulaiBaru') }}";
                }
            });
        @endif

        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 2500, showConfirmButton: false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: "{{ session('error') }}" });
        @endif
        @if(session('info'))
            Swal.fire({ icon: 'info', title: 'Informasi', text: @json(session('info'))['text'] ?? "{{ session('info') }}" });
        @endif

        @if(session('success_tahap'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success_tahap') }}",
                timer: 2500,
                showConfirmButton: false
            });
        @endif
        
        // Alert error tambah tahap
        @if(session('error_tahap'))
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: "{{ session('error_tahap') }}"
            });
        @endif
        
        // Alert berhasil hapus tahap
        @if(session('deleted_tahap'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dihapus!',
                text: "{{ session('deleted_tahap') }}",
                timer: 2500,
                showConfirmButton: false
            });
        @endif
        
        // Alert error hapus tahap
        @if(session('error_delete_tahap'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus',
                text: "{{ session('error_delete_tahap') }}"
            });
        @endif
    });
    </script>
    @endpush 
</x-app-layout>

