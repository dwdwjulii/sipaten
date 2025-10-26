<x-app-layout>
    {{-- Slot untuk Header Halaman --}}
   
        <div class="mb-6 mt-6 pl-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('pencatatan.index') }}"
                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Tugas
                </a>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Buat Catatan Ternak</h1>
            <p class="text-sm text-gray-600">Lengkapi data ternak untuk anggota: <span
                    class="font-semibold">{{ $anggota->nama }}</span></p>
        </div>

        {{-- Konten Utama Halaman --}}
        <div class="py-2">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">

                @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm mx-4 sm:mx-0">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan saat mengisi form</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <form id="pencatatan-form" action="{{ route('pencatatan.update', $pencatatan->id) }}" method="POST"
                    enctype="multipart/form-data" class="px-4 sm:px-0">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Kolom Kiri: Form Data --}}
                        <div class="lg:col-span-2 space-y-6">

                            {{-- Informasi Anggota (Read-only) --}}
                            <div class="section-card bg-white rounded-lg shadow-md p-6">
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-user-circle text-blue-500 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h2 class="text-lg font-semibold text-gray-900">Informasi Anggota Ternak</h2>
                                        <p class="text-sm text-gray-600">Data dasar anggota dan informasi kandang</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <label class="block font-medium text-gray-700 mb-1">Nama Anggota</label>
                                        <div class="p-3 bg-gray-100 border border-gray-200 rounded-md text-gray-900">
                                            {{ $anggota->nama }}</div>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-gray-700 mb-1">Tahap</label>
                                        <div class="p-3 bg-gray-100 border border-gray-200 rounded-md text-gray-900">
                                            {{ $anggota->tahap?->label ?? 'Tidak ada tahap' }}</div>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-gray-700 mb-1">Jenis Ternak</label>
                                        <div class="p-3 bg-gray-100 border border-gray-200 rounded-md text-gray-900">
                                            {{ Str::ucfirst($anggota->jenis_ternak) }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-gray-700 mb-1">Jumlah Induk Awal</label>
                                        <div class="p-3 bg-gray-100 border border-gray-200 rounded-md text-gray-900">
                                            {{ $anggota->ternaks()->where('tipe_ternak', 'Induk')->where('status_aktif', 'aktif')->count() }} ekor</div>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-gray-700 mb-1">Harga Awal Induk</label>
                                        <div class="p-3 bg-gray-100 border border-gray-200 rounded-md text-gray-900">Rp
                                            {{ number_format($anggota->total_harga_induk ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div>
                                        <label class="block font-medium text-gray-700 mb-1">Lokasi Kandang</label>
                                        <div class="p-3 bg-gray-100 border border-gray-200 rounded-md text-gray-900">
                                            {{ $anggota->lokasi_kandang }}</div>
                                    </div>
                                    
                                </div>
                            </div>

                            {{-- Data Ternak --}}
                            <div id="ternakContainer" class="space-y-4">

                                @foreach ($groupedDetails as $group)

                                    {{-- ======================================================= --}}
                                    {{-- Tipe 1: Grup untuk Induk yang Masih Aktif & Anaknya  --}}
                                    {{-- ======================================================= --}}
                                    @if ($group['type'] === 'active_parent')
                                        @php
                                            $indukDetail = $group['induk'];
                                            $anakDetailsCollection = $group['anak'];
                                        @endphp
                                        <div class="bg-white shadow rounded-lg ternak-group mb-4">
                                            {{-- Header Card --}}
                                            <div class="p-4 border-b border-gray-200">
                                                <div class="flex justify-between items-center">
                                                    <h3 class="text-lg font-semibold text-gray-900">Data Induk #{{ $group['group_index'] }}</h3>
                                                    <button type="button" onclick="addAnak('{{ $indukDetail->ternak->id }}', {{ $group['group_index'] }})"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        <i class="fas fa-plus mr-2"></i>Tambah Anak
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Form untuk Data Ternak --}}
                                            <div class="p-6">
                                                {{-- Data Induk --}}
                                                <div class="mb-6">
                                                    <h4 class="text-sm font-semibold text-gray-800 mb-3">Data Induk</h4>
                                                    <input type="hidden" name="ternaks[{{ $indukDetail->id }}][detail_id]" value="{{ $indukDetail->id }}">
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Tipe Ternak</label>
                                                            <select name="ternaks[{{ $indukDetail->id }}][tipe_ternak]" 
                                                                class="tipe-ternak-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="Induk" {{ $indukDetail->ternak->tipe_ternak == 'Induk' ? 'selected' : '' }}>Induk</option>
                                                                <option value="Anak" {{ $indukDetail->ternak->tipe_ternak == 'Anak' ? 'selected' : '' }}>Anak</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Jenis Kelamin</label>
                                                            <select name="ternaks[{{ $indukDetail->id }}][jenis_kelamin]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="">Pilih Kelamin</option>   
                                                                <option value="Jantan" {{ $indukDetail->ternak->jenis_kelamin == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                                                                <option value="Betina" {{ $indukDetail->ternak->jenis_kelamin == 'Betina' ? 'selected' : '' }}>Betina</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">No Ear Tag</label>
                                                            <select name="ternaks[{{ $indukDetail->id }}][no_ear_tag]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="">Pilih Ear Tag</option>   
                                                                <option value="Ada" {{ $indukDetail->ternak->no_ear_tag == 'Ada' ? 'selected' : '' }}>Ada</option>
                                                                <option value="Tidak Ada" {{ $indukDetail->ternak->no_ear_tag == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Umur</label>
                                                            <input type="text" name="ternaks[{{ $indukDetail->id }}][umur_ternak]" value="{{ old('ternaks.'.$indukDetail->id.'.umur_ternak', $indukDetail->umur_saat_dicatat) }}" class="mt-1 block w-full border-gray-300 placeholder:text-red-500 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs" placeholder="Contoh: 6 bulan">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Kondisi</label>
                                                            <select name="ternaks[{{ $indukDetail->id }}][kondisi_ternak]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="">Pilih Kondisi</option>
                                                                <option value="Sehat" {{ old('ternaks.'.$indukDetail->id.'.kondisi_ternak', $indukDetail->kondisi_ternak) == 'Sehat' ? 'selected' : '' }}>Sehat</option>
                                                                <option value="Sakit" {{ old('ternaks.'.$indukDetail->id.'.kondisi_ternak', $indukDetail->kondisi_ternak) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                                                <option value="Mati" {{ old('ternaks.'.$indukDetail->id.'.kondisi_ternak', $indukDetail->kondisi_ternak) == 'Mati' ? 'selected' : '' }}>Mati</option>
                                                                <option value="Terjual" {{ old('ternaks.'.$indukDetail->id.'.kondisi_ternak', $indukDetail->kondisi_ternak) == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Status Vaksin</label>
                                                            <select name="ternaks[{{ $indukDetail->id }}][status_vaksin]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="">Pilih Vaksin</option>
                                                                <option value="Sudah" {{ old('ternaks.'.$indukDetail->id.'.status_vaksin', $indukDetail->status_vaksin) == 'Sudah' ? 'selected' : '' }}>Sudah</option>
                                                                <option value="Belum" {{ old('ternaks.'.$indukDetail->id.'.status_vaksin', $indukDetail->status_vaksin) == 'Belum' ? 'selected' : '' }}>Belum</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Container untuk anak-anak --}}
                                                <div id="anakContainer-{{ $group['group_index'] }}" class="space-y-3">
                                                    @foreach($anakDetailsCollection as $anakDetail)
                                                    <div class="anak-section" id="existing-detail-{{ $anakDetail->id }}">
                                                        <div class="flex justify-between items-center mb-3">
                                                            <h4 class="text-sm font-semibold text-gray-800">Data Anak Ternak</h4>
                                                            <button type="button" onclick="removeExistingDetail('{{ $anakDetail->id }}')" class="text-red-500 hover:text-red-700 transition-colors">
                                                                <i class="fas fa-times"></i> Hapus
                                                            </button>
                                                        </div>
                                                        <input type="hidden" name="ternaks[{{ $anakDetail->id }}][detail_id]" value="{{ $anakDetail->id }}">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700">Tipe Ternak</label>
                                                                <select name="ternaks[{{ $anakDetail->id }}][tipe_ternak]" class="tipe-ternak-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                    <option value="Induk" {{ $anakDetail->ternak->tipe_ternak == 'Induk' ? 'selected' : '' }}>Induk</option>
                                                                    <option value="Anak" {{ $anakDetail->ternak->tipe_ternak == 'Anak' ? 'selected' : '' }}>Anak</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700">Jenis Kelamin</label>
                                                                <select name="ternaks[{{ $anakDetail->id }}][jenis_kelamin]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                    <option value="Jantan" {{ $anakDetail->ternak->jenis_kelamin == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                                                                    <option value="Betina" {{ $anakDetail->ternak->jenis_kelamin == 'Betina' ? 'selected' : '' }}>Betina</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700">No Ear Tag</label>
                                                                <select name="ternaks[{{ $anakDetail->id }}][no_ear_tag]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                    <option value="Ada" {{ $anakDetail->ternak->no_ear_tag == 'Ada' ? 'selected' : '' }}>Ada</option>
                                                                    <option value="Tidak Ada" {{ $anakDetail->ternak->no_ear_tag == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700">Umur</label>
                                                                <input type="text" name="ternaks[{{ $anakDetail->id }}][umur_ternak]" value="{{ old('ternaks.'.$anakDetail->id.'.umur_ternak', $anakDetail->umur_saat_dicatat) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700">Kondisi</label>
                                                                <select name="ternaks[{{ $anakDetail->id }}][kondisi_ternak]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                    <option value="Sehat" {{ old('ternaks.'.$anakDetail->id.'.kondisi_ternak', $anakDetail->kondisi_ternak) == 'Sehat' ? 'selected' : '' }}>Sehat</option>
                                                                    <option value="Sakit" {{ old('ternaks.'.$anakDetail->id.'.kondisi_ternak', $anakDetail->kondisi_ternak) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                                                    <option value="Mati" {{ old('ternaks.'.$anakDetail->id.'.kondisi_ternak', $anakDetail->kondisi_ternak) == 'Mati' ? 'selected' : '' }}>Mati</option>
                                                                    <option value="Terjual" {{ old('ternaks.'.$anakDetail->id.'.kondisi_ternak', $anakDetail->kondisi_ternak) == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-700">Status Vaksin</label>
                                                                <select name="ternaks[{{ $anakDetail->id }}][status_vaksin]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                    <option value="Sudah" {{ old('ternaks.'.$anakDetail->id.'.status_vaksin', $anakDetail->status_vaksin) == 'Sudah' ? 'selected' : '' }}>Sudah</option>
                                                                    <option value="Belum" {{ old('ternaks.'.$anakDetail->id.'.status_vaksin', $anakDetail->status_vaksin) == 'Belum' ? 'selected' : '' }}>Belum</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- =================================================== --}}
                                    {{-- Tipe 2: Grup Khusus untuk Anak "Yatim"           --}}
                                    {{-- =================================================== --}}
                                    @if ($group['type'] === 'orphan')
                                        <div class="bg-white shadow rounded-lg ternak-group mb-4">
                                            <div class="p-4 border-b border-gray-200">
                                                <h3 class="text-lg font-semibold text-gray-900">Data Ternak Lanjutan (Induk Tidak Tercatat)</h3>
                                            </div>
                                            <div class="p-6">
                                                @foreach($group['anak'] as $anakDetail)
                                                <div class="anak-section" id="existing-detail-{{ $anakDetail->id }}" style="border-left-color: #f97316;">
                                                    <div class="flex justify-between items-center mb-3">
                                                        <h4 class="text-sm font-semibold text-gray-800">Data Anak Ternak</h4>
                                                        <button type="button" onclick="removeExistingDetail('{{ $anakDetail->id }}')" class="text-red-500 hover:text-red-700 transition-colors">
                                                            <i class="fas fa-times"></i> Hapus
                                                        </button>
                                                    </div>
                                                    <input type="hidden" name="ternaks[{{ $anakDetail->id }}][detail_id]" value="{{ $anakDetail->id }}">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Tipe Ternak</label>
                                                            <select name="ternaks[{{ $anakDetail->id }}][tipe_ternak]" class="tipe-ternak-select mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="Induk" {{ $anakDetail->ternak->tipe_ternak == 'Induk' ? 'selected' : '' }}>Induk</option>
                                                                <option value="Anak" {{ $anakDetail->ternak->tipe_ternak == 'Anak' ? 'selected' : '' }}>Anak</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Jenis Kelamin</label>
                                                            <select name="ternaks[{{ $anakDetail->id }}][jenis_kelamin]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="Jantan" {{ $anakDetail->ternak->jenis_kelamin == 'Jantan' ? 'selected' : '' }}>Jantan</option>
                                                                <option value="Betina" {{ $anakDetail->ternak->jenis_kelamin == 'Betina' ? 'selected' : '' }}>Betina</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">No Ear Tag</label>
                                                            <select name="ternaks[{{ $anakDetail->id }}][no_ear_tag]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="Ada" {{ $anakDetail->ternak->no_ear_tag == 'Ada' ? 'selected' : '' }}>Ada</option>
                                                                <option value="Tidak Ada" {{ $anakDetail->ternak->no_ear_tag == 'Tidak Ada' ? 'selected' : '' }}>Tidak Ada</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Umur</label>
                                                            <input type="text" name="ternaks[{{ $anakDetail->id }}][umur_ternak]" value="{{ old('ternaks.'.$anakDetail->id.'.umur_ternak', $anakDetail->umur_saat_dicatat) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Kondisi</label>
                                                            <select name="ternaks[{{ $anakDetail->id }}][kondisi_ternak]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="Sehat" {{ old('ternaks.'.$anakDetail->id.'.kondisi_ternak', $anakDetail->kondisi_ternak) == 'Sehat' ? 'selected' : '' }}>Sehat</option>
                                                                <option value="Sakit" {{ old('ternaks.'.$anakDetail->id.'.kondisi_ternak', $anakDetail->kondisi_ternak) == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                                                <option value="Mati" {{ old('ternaks.'.$anakDetail->id.'.kondisi_ternak', $anakDetail->kondisi_ternak) == 'Mati' ? 'selected' : '' }}>Mati</option>
                                                                <option value="Terjual" {{ old('ternaks.'.$anakDetail->id.'.kondisi_ternak', $anakDetail->kondisi_ternak) == 'Terjual' ? 'selected' : '' }}>Terjual</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-700">Status Vaksin</label>
                                                            <select name="ternaks[{{ $anakDetail->id }}][status_vaksin]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 text-xs">
                                                                <option value="Sudah" {{ old('ternaks.'.$anakDetail->id.'.status_vaksin', $anakDetail->status_vaksin) == 'Sudah' ? 'selected' : '' }}>Sudah</option>
                                                                <option value="Belum" {{ old('ternaks.'.$anakDetail->id.'.status_vaksin', $anakDetail->status_vaksin) == 'Belum' ? 'selected' : '' }}>Belum</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                @endforeach

                            </div>

                            {{-- Untuk menampung ID detail yang akan dihapus --}}
                            <div id="deleteContainer"></div>


                            {{-- Temuan Lapangan --}}
                            <div class="section-card bg-white rounded-lg shadow-md p-6">
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clipboard-list text-yellow-500 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h2 class="text-lg font-semibold text-gray-900">Temuan di Lapangan</h2>
                                        <p class="text-sm text-gray-600">Deskripsikan kondisi dan temuan di lapangan</p>
                                    </div>
                                </div>
                                <div>
                                    <label for="temuan_lapangan"
                                        class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Temuan</label>
                                    <textarea id="temuan_lapangan" name="temuan_lapangan" rows="4"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3"
                                        placeholder="Deskripsikan kondisi kandang, kesehatan ternak, dan temuan lainnya...">{{ old('temuan_lapangan', $pencatatan->temuan_lapangan) }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Dokumentasi & Aksi --}}
                        <div class="lg:col-span-1">
                            <div class="sticky top-6 space-y-6">
                                <div class="bg-white shadow-md rounded-lg p-6">
                                    <div class="flex items-center mb-6">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-camera text-purple-500 text-xl"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h2 class="text-lg font-semibold text-gray-900">Dokumentasi Foto</h2>
                                            <p class="text-sm text-gray-600">Unggah foto dokumentasi</p>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <div class="bg-gray-50 rounded-lg p-4 border">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-sm font-medium text-gray-700">Total Foto:</span>
                                                <span id="photoCounter"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    0 foto
                                                </span>
                                            </div>
                                            <div id="selectedFilesList" class="space-y-2 max-h-32 overflow-y-auto">
                                            </div>
                                        </div>

                                        {{-- Preview Grid --}}
                                        <div id="photoPreviewGrid" class="{{ $pencatatan->foto_dokumentasi && count($pencatatan->foto_dokumentasi) > 0 ? '' : 'hidden' }}">
                                            <h3 class="text-sm font-medium text-gray-700 mb-3">Preview Foto:</h3>
                                            <div id="previewContainer" class="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto p-1">
                                                {{-- Foto yang sudah ada --}}
                                                @if($pencatatan->foto_dokumentasi && count($pencatatan->foto_dokumentasi) > 0)
                                                    @foreach($pencatatan->foto_dokumentasi as $index => $foto)
                                                    <div class="relative group photo-preview-item">
                                                        <img src="{{ asset('storage/' . $foto) }}" 
                                                             class="w-full h-20 object-cover rounded border" 
                                                             alt="Dokumentasi {{ $index + 1 }}">
                                                        <input type="hidden" name="existing_photos[]" value="{{ $foto }}">
                                                        <button type="button" 
                                                                onclick="removeExistingPhoto(this)"
                                                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full h-5 w-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity focus:opacity-100">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Unggah Foto
                                                Dokumentasi Baru</label>
                                            <div class="space-y-3">
                                                <div class="relative">
                                                    <input id="foto_dokumentasi" name="foto_dokumentasi[]" type="file"
                                                        class="sr-only" accept="image/*" multiple
                                                        onchange="handleFileSelect(this)">
                                                    <label for="foto_dokumentasi"
                                                        class="cursor-pointer flex flex-col items-center justify-center px-6 py-4 border-2 border-gray-300 border-dashed rounded-md hover:border-purple-400 transition-colors text-center bg-gray-50 hover:bg-gray-100">
                                                        <i
                                                            class="fas fa-cloud-upload-alt text-gray-400 text-xl mb-2"></i>
                                                        <span class="text-sm font-medium text-gray-600">Pilih Beberapa
                                                            Foto</span>
                                                        <span class="text-xs text-gray-500 mt-1">Klik untuk memilih
                                                            file</span>
                                                    </label>
                                                </div>
                                                <button type="button" onclick="openMultiCamera()"
                                                    class="w-full flex flex-col items-center justify-center px-6 py-4 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors text-center bg-blue-50 hover:bg-blue-100">
                                                    <i class="fas fa-camera text-blue-500 text-xl mb-2"></i>
                                                    <span class="text-sm font-medium text-blue-600">Ambil Foto dengan
                                                        Kamera</span>
                                                    <span class="text-xs text-blue-500 mt-1">Ambil beberapa foto
                                                        sekaligus</span>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-2 text-center">PNG, JPG, JPEG hingga 2MB
                                                per file</p>
                                        </div>

                                        <div id="clearAllSection" class="hidden pt-3 border-t border-gray-200">
                                            <button type="button" onclick="clearAllPhotos()"
                                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                                <i class="fas fa-trash mr-2"></i>Hapus Semua Foto Baru
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white shadow-md rounded-lg p-6">
                                    <div class="bg-blue-50 rounded-lg p-4 mb-4 border border-blue-200">
                                        <h3 class="text-sm font-medium text-blue-900 mb-3 flex items-center">
                                            <i class="fas fa-chart-bar mr-2"></i>
                                            Ringkasan Data Ternak
                                        </h3>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-blue-700">Total Induk:</span>
                                                <span class="font-medium text-blue-900" id="totalInduk">0</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-blue-700">Total Anak:</span>
                                                <span class="font-medium text-blue-900" id="totalAnak">0</span>
                                            </div>
                                            <div class="flex justify-between border-t border-blue-200 pt-2 mt-2">
                                                <span class="text-blue-800 font-medium">Total Keseluruhan:</span>
                                                <span class="font-bold text-blue-900" id="totalKeseluruhan">0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-3 pt-4 border-t border-gray-200">
                                        <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm transition-colors">
                                            <i class="fas fa-save mr-2"></i>
                                            <span>Simpan Perubahan</span>
                                        </button>
                                        <a href="{{ route('pencatatan.index') }}"
                                            onclick="window.location.assign('{{ route('pencatatan.index') }}'); return false;"
                                            class="w-full text-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Kamera --}}
        <div id="multiCameraModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeMultiCamera()">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <div
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                    <div>
                        <div class="text-center">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Ambil Foto Dokumentasi</h3>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Foto diambil:</span>
                                    <span id="cameraPhotoCount"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">0</span>
                                </div>
                            </div>
                            <video id="cameraVideo" class="w-full h-64 sm:h-80 bg-black rounded-lg mb-4"
                                playsinline></video>
                            <canvas id="cameraCanvas" class="hidden"></canvas>
                            <div id="capturedPhotosPreview" class="hidden mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Foto yang Diambil:</h4>
                                <div id="capturedPhotosContainer"
                                    class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 max-h-32 overflow-y-auto">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 flex flex-col sm:flex-row-reverse gap-3">
                        <button type="button" onclick="capturePhoto()"
                            class="flex-1 inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                            <i class="fas fa-camera mr-2"></i>Ambil Foto
                        </button>
                        <button type="button" onclick="finishCapturing()"
                            class="flex-1 inline-flex justify-center rounded-md border shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm">
                            <i class="fas fa-check mr-2"></i>Selesai
                        </button>
                        <button type="button" onclick="closeMultiCamera()"
                            class="flex-1 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm mt-3 sm:mt-0">
                            <i class="fas fa-times mr-2"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ASET CSS & JS DILETAKKAN DI SINI AGAR SELF-CONTAINED --}}
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
        .section-card {
            transition: all 0.3s ease;
            border-left: 4px solid #3b82f6;
        }

        .section-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .ternak-group {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .ternak-header {
            background-color: #f9fafb;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .anak-section {
            background-color: #f9fafb;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 1rem;
            border-left: 4px solid #10b981;
        }
        </style>

        <script>
        let anakCounters = {};
        let stream = null;
        let capturedPhotos = [];
        let selectedFiles = [];

        document.addEventListener('DOMContentLoaded', function() {
            updateTernakSummary();
            updatePhotoCounter();

            document.querySelectorAll('.tipe-ternak-select').forEach(select => {
                select.addEventListener('change', updateTernakSummary);
            });

            const form = document.getElementById('pencatatan-form');
            form.addEventListener('submit', function(e) {
                document.querySelectorAll('button[type="submit"]').forEach(btn => {
                    btn.disabled = true;
                    btn.innerHTML =
                        `<i class="fas fa-spinner fa-spin mr-2"></i><span>Menyimpan...</span>`;
                });
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !document.getElementById('multiCameraModal').classList
                    .contains('hidden')) {
                    closeMultiCamera();
                }
            });
        });

        function addAnak(indukId, groupIndex) {
            if (anakCounters[groupIndex] === undefined) {
                anakCounters[groupIndex] = 0;
            }
            anakCounters[groupIndex]++;
            const anakCounter = anakCounters[groupIndex];

            const container = document.getElementById(`anakContainer-${groupIndex}`);
            if (!container) {
                
                console.error(`Container 'anakContainer-${groupIndex}' tidak ditemukan.`);
                return;
            }

            const anakDiv = document.createElement('div');
            anakDiv.className = 'anak-section';
            const uniqueId = `anak-${groupIndex}-${anakCounter}`;
            anakDiv.id = uniqueId;

            const arrayKey = `baru_anak_${groupIndex}_${Date.now()}`;

            anakDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-sm font-semibold text-gray-800">Anak Ternak Baru #${anakCounter}</h4>
                <button type="button" onclick="removeAnak('${uniqueId}')" class="text-red-500 hover:text-red-700 transition-colors">
                    <i class="fas fa-times"></i> Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                 <input type="hidden" name="ternaks[${arrayKey}][induk_id]" value="${indukId}">
                
                <div>
                    <label class="block text-xs font-medium text-gray-700">Tipe Ternak</label>
                    <select name="ternaks[${arrayKey}][tipe_ternak]" class="tipe-ternak-select mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs" onchange="updateTernakSummary()">
                        <option value="Induk">Induk</option>
                        <option value="Anak" selected>Anak</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700">Jenis Kelamin</label>
                    <select name="ternaks[${arrayKey}][jenis_kelamin]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                        <option value="">Pilih Kelamin</option>
                        <option value="Jantan">Jantan</option>
                        <option value="Betina">Betina</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700">No Ear Tag</label>
                    <select name="ternaks[${arrayKey}][no_ear_tag]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                        <option value="">Pilih Ear Tag</option>   
                        <option value="Ada">Ada</option>
                        <option value="Tidak Ada">Tidak Ada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700">Umur</label>
                    <input type="text" name="ternaks[${arrayKey}][umur_ternak]" class="mt-1 block w-full border-gray-300 placeholder:text-red-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs" placeholder="Contoh: 6 bulan">
                </div>
                
                <div class="md:col-span-2 grid grid-cols-2 gap-3">
                    {{-- REVISI: Mengubah KONDISI menjadi dropdown --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Kondisi</label>
                        <select name="ternaks[${arrayKey}][kondisi_ternak]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                            <option value="">Pilih Kondisi</option>  
                            <option value="Sehat">Sehat</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Mati">Mati</option>
                            <option value="Terjual">Terjual</option>
                        </select>
                    </div>
                    {{-- REVISI: Mengubah STATUS VAKSIN menjadi dropdown --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Status Vaksin</label>
                         <select name="ternaks[${arrayKey}][status_vaksin]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                            <option value="">Pilih Vaksin</option>   
                            <option value="Sudah">Sudah</option>
                            <option value="Belum">Belum</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
            container.appendChild(anakDiv);
            updateTernakSummary();
        }

        
        function removeAnak(elementId) {
            document.getElementById(elementId)?.remove();
            updateTernakSummary();
        }

        function removeExistingDetail(detailId) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus anak ternak ini ini?',
                icon: 'warning', 
                showCancelButton: true, 
                //confirmButtonColor: '#d33',
                //cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'swal-button-confirm-red', 
                    cancelButton: 'swal-button-cancel'  
                }
            }).then((result) => {
                // Hanya jalankan kode hapus jika pengguna menekan "Ya, Hapus!"
                if (result.isConfirmed) {
                    
                    // --- Kode asli Anda setelah konfirmasi ---
                    const anakSection = document.getElementById(`existing-detail-${detailId}`);
                    if (anakSection) {
                        // 1. Sembunyikan elemen form secara visual
                        anakSection.style.display = 'none';

                        // 2. Buat hidden input untuk menandai penghapusan
                        const deleteContainer = document.getElementById('deleteContainer');
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'delete_details[]'; // Nama array sesuai validasi controller
                        hiddenInput.value = detailId;
                        deleteContainer.appendChild(hiddenInput);

                        // 3. Update summary (opsional, tapi bagus)
                        updateTernakSummary();
                    } else {
                        console.error(`Elemen anak dengan ID 'existing-detail-${detailId}' tidak ditemukan.`);
                    }
                    // --- Akhir kode asli ---

                } 
                // Jika result.isConfirmed false (klik Batal atau tutup modal), 
                // tidak ada yang perlu dilakukan (sama seperti 'return;' Anda sebelumnya)
            });
        }
         

        // Jangan lupa fungsi updateTernakSummary() yang sudah dimodifikasi:
        function updateTernakSummary() {
            let totalInduk = 0;
            let totalAnak = 0;
            document.querySelectorAll('.tipe-ternak-select').forEach(select => {
                const parentSection = select.closest('.anak-section, .ternak-group'); 
                if (parentSection && parentSection.style.display !== 'none') {
                    if (select.value === 'Induk') totalInduk++;
                    else if (select.value === 'Anak') totalAnak++;
                } else if (select.closest('.ternak-group') && !select.closest('.anak-section')) {
                    if (select.closest('.ternak-group').style.display !== 'none' && select.value === 'Induk') totalInduk++;
                }
            });

            // Hitung juga induk utama
            document.querySelectorAll('.ternak-group').forEach(group => {
                const indukSelect = group.querySelector(':scope > div > div > div > select[name*="[tipe_ternak]"]');
                if (group.style.display !== 'none' && indukSelect && indukSelect.value === 'Induk') {
                    if (!indukSelect.classList.contains('tipe-ternak-select')) {
                        totalInduk++; 
                    }
                }
            });

            document.getElementById('totalInduk').textContent = totalInduk;
            document.getElementById('totalAnak').textContent = totalAnak;
            document.getElementById('totalKeseluruhan').textContent = totalInduk + totalAnak;
        }

        function updateAllUI() {
            updatePhotoCounter();
            updateFilesList();
            updatePreviewGrid();
            updateFormFileInput();
        }

        function updatePhotoCounter() {
            const existingPhotosCount = document.querySelectorAll('input[name="existing_photos[]"]').length;
            const newPhotosCount = selectedFiles.length + capturedPhotos.length;
            const totalPhotos = existingPhotosCount + newPhotosCount;

            document.getElementById('photoCounter').textContent = `${totalPhotos} foto`;
            document.getElementById('clearAllSection').classList.toggle('hidden', newPhotosCount === 0);
            document.getElementById('photoPreviewGrid').classList.toggle('hidden', totalPhotos === 0);
        }

        function updateFilesList() {
            const filesList = document.getElementById('selectedFilesList');
            filesList.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                filesList.innerHTML +=
                    `<div class="flex items-center justify-between text-xs bg-white rounded p-2 border"><div class="flex items-center min-w-0"><i class="fas fa-image text-blue-500 mr-2"></i><span class="truncate">${file.name}</span></div><button type="button" onclick="removeSelectedFile(${index})" class="text-red-500 hover:text-red-700 ml-2 flex-shrink-0"><i class="fas fa-times"></i></button></div>`;
            });
            capturedPhotos.forEach((photo, index) => {
                filesList.innerHTML +=
                    `<div class="flex items-center justify-between text-xs bg-white rounded p-2 border"><div class="flex items-center"><i class="fas fa-camera text-green-500 mr-2"></i><span>Foto Kamera ${index + 1}</span></div><button type="button" onclick="removeCapturedPhoto(${index})" class="text-red-500 hover:text-red-700 ml-2 flex-shrink-0"><i class="fas fa-times"></i></button></div>`;
            });
        }

        function updatePreviewGrid() {
            const previewContainer = document.getElementById('previewContainer');
            previewContainer.querySelectorAll('.new-photo-preview').forEach(el => el.remove());

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative group new-photo-preview';
                    previewDiv.innerHTML =
                        `<img src="${e.target.result}" class="w-full h-20 object-cover rounded border"><button type="button" onclick="removeSelectedFile(${index})" class="absolute top-1 right-1 bg-red-600 text-white rounded-full h-5 w-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity focus:opacity-100"><i class="fas fa-times text-xs"></i></button>`;
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });

            capturedPhotos.forEach((photoBlob, index) => {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'relative group new-photo-preview';
                previewDiv.innerHTML =
                    `<img src="${URL.createObjectURL(photoBlob)}" class="w-full h-20 object-cover rounded border"><button type="button" onclick="removeCapturedPhoto(${index})" class="absolute top-1 right-1 bg-red-600 text-white rounded-full h-5 w-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity focus:opacity-100"><i class="fas fa-times text-xs"></i></button>`;
                previewContainer.appendChild(previewDiv);
            });
        }

       function handleFileSelect(input) {
            const validFiles = [];

            Array.from(input.files).forEach(file => {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: 'Ukuran File Terlalu Besar!',
                        text: `File "${file.name}" Maksimal 2MB.`,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        title: 'Format Tidak Didukung!',
                        text: `File "${file.name}" bukan format gambar yang valid.`,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                    return;
                }

                validFiles.push(file);
            });

            if (validFiles.length > 0) {
                selectedFiles = [...selectedFiles, ...validFiles];
                updateAllUI();
            }

            input.value = '';
        }



        function removeSelectedFile(index) {
            selectedFiles.splice(index, 1);
            updateAllUI();
        }

        function removeCapturedPhoto(index) {
            capturedPhotos.splice(index, 1);
            updateAllUI();
        }

        function removeExistingPhoto(button) {
            Swal.fire({
                title: 'Hapus Foto Ini?',
                text: 'Apakah Anda yakin ingin menghapus foto ini dari catatan?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // merah
                cancelButtonColor: '#6b7280',  // abu-abu
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'swal-button-confirm-red', 
                    cancelButton: 'swal-button-cancel'  
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hapus elemen foto dari tampilan
                    button.closest('.photo-preview-item').remove();
                    updatePhotoCounter();

                    Swal.fire({
                        title: 'Dihapus!',
                        text: 'Foto telah dihapus dari catatan.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }


        function clearAllPhotos() {
            Swal.fire({
                title: 'Hapus Semua Foto?',
                text: 'Apakah Anda yakin ingin menghapus semua foto baru yang dipilih atau diambil?',
                icon: 'warning',
                showCancelButton: true,
                //confirmButtonColor: '#ef4444', // merah
                //cancelButtonColor: '#6b7280',  // abu-abu
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'swal-button-confirm-red', 
                    cancelButton: 'swal-button-cancel'  
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    selectedFiles = [];
                    capturedPhotos = [];
                    updateAllUI();

                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Semua foto telah dihapus.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }


        function updateFormFileInput() {
            const fileInput = document.getElementById('foto_dokumentasi');
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            capturedPhotos.forEach((blob, index) => {
                const file = new File([blob], `kamera_${Date.now()}_${index}.jpg`, {
                    type: 'image/jpeg'
                });
                dt.items.add(file);
            });
            fileInput.files = dt.files;
        }

        async function openMultiCamera() {
            const modal = document.getElementById('multiCameraModal');
            const video = document.getElementById('cameraVideo');
            modal.classList.remove('hidden');

            document.getElementById('cameraPhotoCount').textContent = capturedPhotos.length;
            updateCapturedPhotosPreviewInModal();

            const constraints = {
                video: {
                    facingMode: 'environment',
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    }
                }
            };
            try {
                stream = await navigator.mediaDevices.getUserMedia(constraints);
            } catch (err) {
                console.warn('Kamera belakang tidak ditemukan, mencoba kamera depan:', err);
                try {
                    const fallbackConstraints = {
                        video: {
                            facingMode: 'user',
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            }
                        }
                    };
                    stream = await navigator.mediaDevices.getUserMedia(fallbackConstraints);
                } catch (fallbackErr) {
                    alert('Tidak dapat mengakses kamera. Pastikan browser memiliki izin.');
                    closeMultiCamera();
                    return;
                }
            }
            video.srcObject = stream;
            video.play();
        }

        function capturePhoto() {
            const video = document.getElementById('cameraVideo');
            const canvas = document.getElementById('cameraCanvas');
            if (video.readyState < 2) return alert('Kamera belum siap, mohon tunggu.');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(blob => {
                if (blob) {
                    capturedPhotos.push(blob);
                    document.getElementById('cameraPhotoCount').textContent = capturedPhotos.length;
                    updateCapturedPhotosPreviewInModal();
                    video.style.filter = 'brightness(1.5)';
                    setTimeout(() => {
                        video.style.filter = 'brightness(1)';
                    }, 150);
                }
            }, 'image/jpeg', 0.85);
        }

        function updateCapturedPhotosPreviewInModal() {
            const previewSection = document.getElementById('capturedPhotosPreview');
            const previewContainer = document.getElementById('capturedPhotosContainer');
            previewSection.classList.toggle('hidden', capturedPhotos.length === 0);
            previewContainer.innerHTML = '';
            capturedPhotos.forEach((blob, index) => {
                const previewDiv = document.createElement('div');
                previewDiv.className = 'relative';
                previewDiv.innerHTML =
                    `<img src="${URL.createObjectURL(blob)}" class="w-full h-16 object-cover rounded border"><button type="button" onclick="removeCapturedPhotoFromModal(${index})" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full h-4 w-4 flex items-center justify-center text-xs"><i class="fas fa-times"></i></button>`;
                previewContainer.appendChild(previewDiv);
            });
        }

        function removeCapturedPhotoFromModal(index) {
            capturedPhotos.splice(index, 1);
            document.getElementById('cameraPhotoCount').textContent = capturedPhotos.length;
            updateCapturedPhotosPreviewInModal();
        }

        function finishCapturing() {
            updateAllUI();
            closeMultiCamera();
        }

        function closeMultiCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            document.getElementById('cameraVideo').srcObject = null;
            document.getElementById('multiCameraModal').classList.add('hidden');
        }
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

        @if(session('success'))
        <script>
        // Paksa kembali ke halaman utama jika masih tertahan di halaman ini setelah sukses
        window.location.assign("{{ route('pencatatan.index') }}");
        </script>
        @endif
</x-app-layout>