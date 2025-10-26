{{-- File: resources/views/layouts/sidebar.blade.php --}}
<aside
    class="sidebar fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 lg:flex lg:w-56 flex-col bg-white px-4">

    {{-- Logo --}}
    <div class="flex items-center justify-center">
        {{-- Logika link logo: admin ke dashboard, petugas ke pencatatan --}}
        <a href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('pencatatan.index') }}"
            class="flex items-center justify-center -my-5 pb-2">
            <img src="{{ asset('asset/images/logo_only.png') }}" alt="Logo Only" class="h-12 w-16">
            <img src="{{ asset('asset/images/name_logo.png') }}" alt="Logo Name" class="h-28 w-28 -ml-3">
        </a>
    </div>

    <div class="flex flex-1 flex-col overflow-y-auto">
        <nav class="space-y-2">

            {{-- =================================== --}}
            {{-- V V V      MENU KHUSUS ADMIN      V V V --}}
            {{-- =================================== --}}
            @if (auth()->user()->role === 'admin')
            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
                @class([ 'flex items-center rounded-md px-4 py-2.5 transition-all duration-200'
                , 'bg-gray-100 text-black shadow-sm'=> request()->routeIs('dashboard'),
                'text-gray-400 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('dashboard'),
                ])>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-layout-dashboard-icon lucide-layout-dashboard">
                    <rect width="7" height="9" x="3" y="3" rx="1" />
                    <rect width="7" height="5" x="14" y="3" rx="1" />
                    <rect width="7" height="9" x="14" y="12" rx="1" />
                    <rect width="7" height="5" x="3" y="16" rx="1" />
                </svg>

                <span class="ml-4 text-sm font-medium">Dashboard</span>
            </a>

            {{-- Kelola Pengguna --}}
            <a href="{{ route('pengguna.index') }}"
                @class([ 'flex items-center rounded-md px-4 py-2.5 transition-all duration-200'
                , 'bg-gray-100 text-black shadow-sm'=> request()->routeIs('pengguna.*'),
                'text-gray-400 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('pengguna.*'),
                ])>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-circle-user-round-icon lucide-circle-user-round">
                    <path d="M18 20a6 6 0 0 0-12 0" />
                    <circle cx="12" cy="10" r="4" />
                    <circle cx="12" cy="12" r="10" />
                </svg>
                <span class="ml-4 text-sm font-medium">Kelola Pengguna</span>
            </a>

            {{-- Kelola Anggota --}}
            <a href="{{ route('anggota.index') }}"
                @class([ 'flex items-center rounded-md px-4 py-2.5 transition-all duration-200'
                , 'bg-gray-100 text-black shadow-sm'=> request()->routeIs('anggota.*'),
                'text-gray-400 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('anggota.*'),
                ])>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-layers-icon lucide-layers">
                    <path
                        d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z" />
                    <path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12" />
                    <path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17" />
                </svg>

                <span class="ml-4 text-sm font-medium">Kelola Anggota</span>
            </a>

            {{-- Kelola Catatan --}}
            <a href="{{ route('pencatatan.index') }}"
                @class([ 'flex items-center rounded-md px-4 py-2.5 transition-all duration-200'
                , 'bg-gray-100 text-black shadow-sm'=> request()->routeIs('pencatatan.index', 'pencatatan.show'),
                'text-gray-400 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('pencatatan.index', 'pencatatan.show'),
                ])>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-file-text-icon lucide-file-text">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                    <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                    <path d="M10 9H8" />
                    <path d="M16 13H8" />
                    <path d="M16 17H8" />
                </svg>

                <span class="ml-4 text-sm font-medium">Kelola Catatan</span>
            </a>

            {{-- Arsip Laporan --}}
            <a href="{{ route('arsip.index') }}"
                @class([ 'flex items-center rounded-md px-4 py-2.5 transition-all duration-200'
                , 'bg-gray-100 text-black shadow-sm'=> request()->routeIs('arsip.*'),
                'text-gray-400 hover:bg-gray-50 hover:text-black' => !request()->routeIs('arsip.*'),
                ])>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-archive-icon lucide-archive">
                    <rect width="20" height="5" x="2" y="3" rx="1" />
                    <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                    <path d="M10 12h4" />
                </svg>
                <span class="ml-4 text-sm font-medium">Arsip Laporan</span>
            </a>
            @endif


            {{-- ====================================== --}}
            {{-- V V V      MENU KHUSUS PETUGAS     V V V --}}
            {{-- ====================================== --}}
            @if (auth()->user()->role === 'petugas')
            <a href="{{ route('pencatatan.index') }}"
                @class([ 'flex items-center rounded-md px-4 py-2.5 transition-all duration-200'
                , 'bg-gray-100 text-black shadow-sm'=> request()->routeIs('pencatatan.*'),
                    'text-gray-400 hover:bg-gray-50 hover:text-gray-900' => !request()->routeIs('pencatatan.*'),
                ])>
                
                {{-- Ganti dengan SVG icon yang sesuai untuk pencatatan --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-clipboard-list">
                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                    <path d="M12 11h4" />
                    <path d="M12 16h4" />
                    <path d="M8 11h.01" />
                    <path d="M8 16h.01" />
                </svg>
                <span class="ml-4 text-sm font-medium">Pencatatan</span>
            </a>
            @endif

        </nav>
    </div>
</aside>