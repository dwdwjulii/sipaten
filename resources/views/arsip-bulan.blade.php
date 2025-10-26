<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Header Judul + Tombol Validasi --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Rekap Arsip Bulanan</h2>
                    <p class="text-sm text-gray-500">Menampilkan semua file arsip pada tahun <span class="font-bold">{{ $tahun }}</span></p>
                </div>

                {{-- PERUBAHAN 1: Form validasi tetap ada, tapi tombolnya diubah --}}
                <form id="form-validasi" action="{{ route('arsip.validasi', ['tahun' => $tahun]) }}" method="POST" class="hidden">
                    @csrf
                </form>

                {{-- Tombol diubah menjadi type="button" dan diberi ID --}}
                <button type="button" id="btn-validasi"
                        class="flex items-center justify-center space-x-1 bg-green-600 text-white hover:bg-green-700 font-medium text-sm px-4 py-2 rounded-lg transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="currentColor" d="m10 17l-5-5l1.41-1.42L10 14.17l7.59-7.59L19 8z"/>
                    </svg>
                    <span>Validasi</span>
                </button>
            </div>

            {{-- Menampilkan notifikasi sukses/gagal --}}
            @if(session('success'))
                <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Grid Card Dinamis --}}
            @if($arsips->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach ($arsips as $arsip)
                        {{-- Card untuk setiap file arsip --}}
                        <div class="flex flex-col justify-between transition bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md">
                            {{-- Konten Info Arsip --}}
                            <div class="p-5">
                                <h3 class="mb-2 text-lg font-semibold text-gray-800 truncate">
                                    {{ $arsip->nama_file }}
                                </h3>
                                <div class="flex items-center justify-between mt-4 text-sm text-gray-500">
                                    <span class="px-2 py-1 font-medium bg-gray-100 rounded">
                                        {{ \Carbon\Carbon::createFromFormat('!m', $arsip->bulan)->translatedFormat('F') }}
                                    </span>
                                    <span class="font-medium">
                                        {{ $arsip->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Tombol Aksi (Lihat, Edit, Hapus) --}}
                            <div class="flex items-center justify-end px-5 py-3 space-x-4 bg-gray-50 border-t rounded-b-lg">
                                <a href="{{ route('arsip.show', $arsip->id) }}" target="_blank" class="text-sm font-medium text-blue-600 hover:text-blue-800">Lihat</a>

                                {{-- PERUBAHAN 2: Form hapus diberi ID unik --}}
                                <form id="form-hapus-{{ $arsip->id }}" action="{{ route('arsip.destroy', $arsip->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                
                                {{-- Tombol hapus diubah menjadi type="button" dan diberi class serta atribut data-* --}}
                                <button type="button" class="btn-hapus text-sm font-medium text-red-600 hover:text-red-800" data-arsip-id="{{ $arsip->id }}">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Arsip</h3>
                    <p class="mt-1 text-sm text-gray-500">Saat ini belum ada laporan yang diarsipkan pada tahun {{ $tahun }}.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- PERUBAHAN 3: Skrip diperbarui untuk menggunakan event 'click' pada tombol --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // --- 1. Tangani Tombol Validasi (dipicu oleh KLIK) ---
            const btnValidasi = document.getElementById('btn-validasi');
            if (btnValidasi) {
                btnValidasi.addEventListener('click', function () {
                    const formValidasi = document.getElementById('form-validasi');
                    if(formValidasi) {
                        Swal.fire({
                            title: 'Anda yakin?',
                            text: "Status arsip tahun ini akan diubah menjadi 'Selesai' dan tidak dapat diubah lagi.",
                            icon: 'warning',
                            showCancelButton: true,
                            //confirmButtonColor: '#16a34a',
                            //cancelButtonColor: '#6e7881',
                            confirmButtonText: 'Ya, Validasi!',
                            cancelButtonText: 'Batal',
                            showLoaderOnConfirm: true,
                            reverseButtons: true,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'swal-button-konfirmasi-green', 
                                cancelButton: 'swal-button-batal'
                            },
                            preConfirm: () => {
                                return new Promise(() => {
                                    formValidasi.submit();
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        });
                    }
                });
            }

            // --- 2. Tangani Semua Tombol Hapus (dipicu oleh KLIK) ---
            const btnsHapus = document.querySelectorAll('.btn-hapus');
            btnsHapus.forEach(button => {
                button.addEventListener('click', function () {
                    const arsipId = this.dataset.arsipId;
                    const form = document.getElementById('form-hapus-' + arsipId);

                    if (form) {
                        Swal.fire({
                            title: 'Hapus Arsip?',
                            text: "Arsip ini akan dihapus permanen dan periode pencatatan akan dibuka kembali. Anda tidak dapat mengembalikan tindakan ini.",
                            icon: 'warning',
                            showCancelButton: true,
                            //confirmButtonColor: '#d33',
                            //cancelButtonColor: '#6e7881',
                            confirmButtonText: 'Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            showLoaderOnConfirm: true,
                            reverseButtons: true,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'swal-button-confirm-red',
                                cancelButton: 'swal-button-cancel'
                            },
                            preConfirm: () => {
                                return new Promise(() => {
                                    form.submit();
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        });
                    }
                });
            });

        });
    </script>

    <style>
    /* Perbaikan tombol SweetAlert agar tidak buram */
    .swal2-styled.swal2-confirm {
        opacity: 1 !important;
        filter: none !important;
        transition: none !important;
        background-color: #10b981 !important; /* hijau sesuai confirmButtonColor */
        color: white !important;
    }

    /* Saat disabled (misal pas loading), tetap terlihat normal */
    .swal2-styled.swal2-confirm:disabled {
        opacity: 1 !important;
        cursor: not-allowed !important;
        background-color: #10b981 !important;
    }
    </style>
</x-app-layout>
