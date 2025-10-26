{{-- File: resources/views/anggota.blade.php --}}
<x-app-layout>
    <div class="py-6">

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-4">
            <div class="bg-white shadow rounded-sm p-6">

                {{-- Header Konten --}}
                <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Kelola Anggota
                    </h2>

                    {{-- Search, Filter, Tambah --}}
                    <div class="flex items-center gap-2">
                        <x-search-bar :action="route('anggota.index')" />

                        {{-- Filter Tahap --}}
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open"
                                class="flex items-center space-x-1 px-3 w-36 justify-between py-1.5 text-sm font-normal text-gray-800 bg-white border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                                <span class="whitespace-normal break-words text-left">
                                    {{ $tahapDipilih ? $tahapDipilih->label : 'Pilih Tahap' }}
                                </span>
                                <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute z-50 mt-1 w-36 bg-white border border-gray-200 rounded-md shadow-lg">
                                <ul class="max-h-52 overflow-y-auto text-sm text-gray-700">
                                    {{-- Opsi untuk menampilkan semua tahap --}}
                                    <li class="border-b border-gray-100">
                                        <form action="{{ route('anggota.index') }}" method="GET">
                                            <button type="submit"
                                                class="w-full text-left px-4 py-2 text-xs hover:bg-green-100 hover:rounded-t-md">
                                                Semua Tahap
                                            </button>
                                        </form>
                                    </li>

                                    {{-- Perulangan untuk setiap tahap dengan ikon hapus --}}
                                    @foreach ($tahaps as $tahap)
                                    <li class="group">
                                        <div
                                            class="flex items-center justify-between w-full px-4 py-2 hover:bg-green-50">
                                            {{-- Tombol Filter (Sebelah Kiri) --}}
                                            <form action="{{ route('anggota.index') }}" method="GET" class="flex-grow">
                                                <input type="hidden" name="tahap_id" value="{{ $tahap->id }}">
                                                <button type="submit"
                                                    class="w-full text-left text-xs whitespace-normal break-words">
                                                    {{ $tahap->label }}
                                                </button>
                                            </form>

                                            {{-- Tombol Hapus dengan Ikon (Sebelah Kanan) --}}
                                            <div>
                                                <form action="{{ route('tahap.destroy', $tahap->id) }}" method="POST"
                                                    class="delete-tahap-form"
                                                    data-anggota-count="{{ $tahap->anggotas_count }}"
                                                    data-tahap-name="{{ $tahap->label }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity mt-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                    {{-- Opsi untuk menambah tahap baru --}}
                                    <li class="border-t rounded-b-lg border-gray-100 sticky bottom-0 bg-white">
                                        <x-anggota.tahap-modal />
                                    </li>
                                </ul>
                            </div>
                        </div>


                        {{-- Tombol Tambah Anggota --}}
                        @if($tahapDipilih)
                        <x-anggota.create-modal :tahapId="$tahapDipilih->id" />
                        @else
                        <button disabled
                            class="inline-flex items-center px-2 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-4 h-4 mr-1">
                                <path
                                    d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Tambah Anggota
                        </button>
                        @endif

                    </div>
                </div>

                {{-- Tabel Anggota --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs text-gray-600 border border-gray-200">
                        <thead class="bg-gray-100 text-gray-500 text-sm">
                            <tr>
                               <th class="w-10 px-2 py-3 border border-gray-200 text-center">No</th>
                                <th class="w-32 px-2 py-3 border border-gray-200 text-left">Nama Anggota</th>
                                <th class="w-20 px-2 py-3 border border-gray-200 text-center">Tahap</th>
                                <th class="w-20 px-2 py-3 border border-gray-200 text-center">Jenis Ternak</th>
                                <th class="px-2 py-3 border border-gray-200 text-center">Jumlah Induk</th>
                                <th class="px-2 py-3 border border-gray-200 text-center">Total Harga Induk</th>
                                <th class="px-2 py-3 border border-gray-200 text-center">No. HP</th>
                                <th class="px-2 py-3 border border-gray-200 text-center">Lokasi Kandang</th>
                                <th class="px-2 py-3 border border-gray-200 text-center">Status</th>
                                <th class="w-24 px-2 py-3 border border-gray-200 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($anggotas as $anggota)
                            <tr class="hover:bg-gray-50">
                                <td class="w-[4%] px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                    {{ $loop->iteration + ($anggotas->currentPage() - 1) * $anggotas->perPage() }}
                                </td>
                                <td class="w-[18%] px-2 py-3 border border-gray-100 text-gray-900 text-left truncate max-w-[250px]"
                                    title="{{$anggota->nama}}">
                                    {{ $anggota->nama }}
                                </td>
                                <td class="w-[6%] px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                    {{ $anggota->tahap?->label ?? 'Tidak ada tahap' }}
                                </td>
                                <td class="w-[8%] px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                    {{ $anggota->jenis_ternak }}
                                </td>
                                <td class="w-[9%] px-2 py-3 border border-gray-100  text-gray-900 text-center">
                                    {{ $anggota->jumlah_induk }}
                                </td>
                                <td class="w-[14%] px-2 py-3 border border-gray-100 text-gray-900 text-left">
                                    Rp {{ number_format($anggota->total_harga_induk, 0, ',', '.') }}
                                </td>
                                <td class="w-[10%] px-2 py-3 border  border-gray-100 text-gray-900 text-center">
                                    {{ $anggota->no_hp }}
                                </td>
                                <td class="px-2 py-3 border border-gray-100 text-gray-900 text-left truncate max-w-[150px]"
                                    title="{{$anggota->lokasi_kandang}}">
                                    {{ $anggota->lokasi_kandang }}
                                </td>
                                <td class="w-[8%] px-2 py-3 border border-gray-100  text-center">
                                    @if ($anggota->status == 'aktif')
                                    <span
                                        class="px-2 py-0 text-xs rounded-lg bg-green-200 text-green-700 border border-green-400 font-semibold">Aktif</span>
                                    @else
                                    <span
                                        class="px-2 py-0 text-xs rounded-lg bg-red-200 text-red-700 border border-red-400 font-semibold">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="px-2 py-3 border border-gray-100 ">
                                    <div class="flex justify-center items-center space-x-2">
                                        <x-anggota.detail-modal :anggota="$anggota" />
                                        <x-anggota.edit-modal :anggota="$anggota" :tahaps="$tahaps"
                                            :currentTahapId="$tahapDipilih?->id" />
                                        <x-anggota.delete-modal :anggota="$anggota"
                                            :currentTahapId="$tahapDipilih?->id" />
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-gray-500">
                                    @if($tahapDipilih)
                                    Tidak ada data anggota pada tahap "{{ $tahapDipilih->label }}".
                                    @else
                                    Tidak ada data anggota. Silakan pilih tahap terlebih dahulu.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- BAGIAN BARU YANG SUDAH DIPERBAIKI --}}
                <div class="flex items-center justify-between mt-4 text-sm text-gray-600">

                    {{-- [KIRI] Dropdown untuk menampilkan jumlah entri --}}
                    <x-show-entries />

                    {{-- [TENGAH] Teks informasi hasil --}}
                    {{-- Hanya tampilkan teks ini jika ada data --}}
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

                    {{-- [KANAN] Tombol navigasi halaman --}}
                    <div>
                        {{ $anggotas->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>


@push('scripts')
<script>
window.onload = function() {
    console.log('Script aktif (window.onload)');

    const deleteForms = document.querySelectorAll('.delete-tahap-form');
    console.log('Jumlah form ditemukan:', deleteForms.length);

    deleteForms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Submit form dipicu:', this.action);

            const tahapName = this.dataset.tahapName;
            const anggotaCount = parseInt(this.dataset.anggotaCount) || 0;

            // Pesan konfirmasi
            let message = `Anda akan menghapus tahap <strong>"${tahapName}"</strong>`;
            if (anggotaCount > 0) {
                message += `<br><br>Peringatan: Ini akan menghapus <strong>${anggotaCount} anggota</strong> yang terkait dengan tahap ini!`;
            }

            try {
                const result = await Swal.fire({
                    title: 'Konfirmasi Hapus',
                    html: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                    allowOutsideClick: false
                });

                if (result.isConfirmed) {
                    // Tampilkan loading SweetAlert
                    await Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // Kirim form secara manual via fetch
                    const formData = new FormData(this);
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (response.ok) {
                        await Swal.fire({
                            title: 'Berhasil!',
                            text: 'Tahap berhasil dihapus',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        window.location.reload();
                    } else {
                        throw new Error('Gagal menghapus tahap');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                await Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus tahap',
                    icon: 'error'
                });
            }
        });
    });
};
</script>
@endpush

