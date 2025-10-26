<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Arsip Laporan Tahunan</h2>
                <p class="text-sm text-gray-500">Pilih tahun untuk melihat rekap arsip bulanan.</p>
            </div>

            {{-- Menampilkan notifikasi sukses/gagal dari proses validasi --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($arsipsPerTahun->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    @foreach ($arsipsPerTahun as $arsip)
                        @php
                            // Menentukan status, default 'progress' jika null
                            $status = $arsip->status ?? 'progress';
                            $bgColor = $status == 'selesai' ? 'bg-green-700' : 'bg-yellow-600';
                            $borderColor = $status == 'selesai' ? 'hover:border-green-500' : 'hover:border-yellow-500';
                            $textColor = $status == 'selesai' ? 'text-green-600' : 'text-yellow-600';
                        @endphp
                        <a href="{{ route('arsip.tahun', $arsip->tahun) }}"
                           class="relative flex flex-col items-center justify-between bg-white p-6 border border-gray-200 rounded-xl shadow-sm hover:shadow-lg {{ $borderColor }} transition min-h-[220px]">
                            
                            {{-- Badge Status --}}
                            <span class="absolute top-3 right-3 text-xs font-bold text-white px-2 py-1 rounded-full {{ $bgColor }}">
                                {{ ucfirst($status) }}
                            </span>

                            <span class="absolute top-3 left-3 text-sm font-semibold text-gray-600">
                                Tahun {{ $arsip->tahun }}
                            </span>

                            <div class="flex flex-col items-center gap-1 mt-6 {{ $textColor }}">
                                <svg class="w-20 h-20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M4 20q-.825 0-1.412-.587T2 18V6q0-.825.588-1.412T4 4h6l2 2h8q.825 0 1.413.588T22 8v10q0 .825-.587 1.413T20 20z"/></svg>
                            </div>
                            
                            <p class="w-full text-center px-2 py-1.5 text-sm font-medium text-white {{ $bgColor }} border border-transparent rounded-md">
                                {{ $arsip->jumlah }} Arsip
                            </p>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Arsip</h3>
                    <p class="mt-1 text-sm text-gray-500">Saat ini belum ada laporan yang diarsipkan.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
