{{-- File: resources/views/pengguna.blade.php --}}
<x-app-layout>
    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-4">
            <div class="bg-white shadow rounded-sm p-6">

                {{-- Header Konten --}}
                <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center">
                    {{-- Judul Kiri --}}
                    <h2 class="text-xl font-semibold text-gray-800">
                        Kelola Pengguna
                    </h2>

                    {{-- Search + Tambah di Kanan (dipaksa ke kanan, jarak antar elemen tetap ada) --}}
                    <div class="flex items-center ml-auto space-x-2">
                        {{-- batasi lebar search agar tidak mendorong tombol --}}
                        <div class="max-w-sm w-full">
                            <x-search-bar :action="route('pengguna.index')" />
                        </div>

                        {{-- pastikan tombol tidak melebar atau menyusut --}}
                        <div class="flex-shrink-0">
                            <x-pengguna.create-modal />
                        </div>
                    </div>
                </div>


                {{-- Tabel --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs text-left text-gray-600">
                        <thead class=" bg-gray-100 text-gray-500 text-sm">
                            <tr>
                                <th class="w-10 px-2 py-3 border border-gray-200 text-center">No</th>
                                <th class="px-2 py-3 border border-gray-200">Nama Pengguna</th>
                                <th class="px-2 py-3 border border-gray-200">Username</th>
                                <th class="px-2 py-3 border border-gray-200">Password</th>
                                <th class="px-2 py-3 border border-gray-200 text-center">Role</th>
                                <th class="px-2 py-3 border border-gray-200 text-center">Status</th>
                                
                                <th class="px-4 py-3 border border-gray-200 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    {{-- Gunakan $loop->iteration untuk nomor urut paginasi --}}
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                        {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900 text-left truncate max-w-[150px]">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900">{{ $user->email }}</td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900">****************</td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900 text-center">{{ ucfirst($user->role) }}</td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                        {{-- Contoh logika untuk status --}}
                                        @if ($user->status === 'aktif') {{-- Ganti 'status' sesuai nama kolom Anda --}}
                                            <span class="px-2 text-xs rounded-lg text-center bg-green-200 text-green-700 border border-green-400 font-semibold min-w-[74px] inline-block">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-2 text-xs rounded-lg text-center bg-red-200 text-red-700 border border-red-400 font-semibold min-w-[74px] inline-block">
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 whitespace-nowrap">
                                        <div class="flex justify-center space-x-2">
                                            {{-- Kirim data user ke modal edit --}}
                                            <x-pengguna.edit-modal :user="$user" />
                                            {{-- Kirim data user ke modal delete --}}
                                            <x-pengguna.delete-modal :user="$user" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                {{-- Tampilan jika data pengguna kosong --}}
                                <tr>
                                    <td colspan="7" class="text-center py-4 border border-gray-100">
                                        Tidak ada data pengguna.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>