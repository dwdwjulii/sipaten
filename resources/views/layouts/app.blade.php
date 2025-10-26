{{-- File: resources/views/layouts/app.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/datepicker.min.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    [x-cloak] {
        display: none !important
    }
    </style>
</head>


<body class="font-sans antialiased">

    <div class="flex h-screen bg-neutral-100 dark:bg-gray-900">

        @include('layouts.sidebar')

        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

        <main class="flex flex-1 flex-col min-w-0 min-h-0">

            <header class="bg-white sm:px-3 py-3 border-b shadow-sm">
                <div class="flex items-center justify-between">
                    {{-- Tombol toggle sidebar di kiri --}}
                    <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-gray-950 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd"
                                d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    {{-- Dropdown profil di kanan --}}
                    <div class="relative ml-auto">
                        <x-dropdown align="right" width="32">

                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                {{-- Link Profil --}}
                                <x-dropdown-link :href="route('profile.edit')">
                                    <x-slot name="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-user-pen">
                                            <path d="M11.5 15H7a4 4 0 0 0-4 4v2" />
                                            <path
                                                d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                                            <circle cx="10" cy="7" r="4" />
                                        </svg>
                                    </x-slot>
                                    {{ __('Profil') }}
                                </x-dropdown-link>

                                {{-- Link Logout --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        <x-slot name="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-log-out">
                                                <path d="m16 17 5-5-5-5" />
                                                <path d="M21 12H9" />
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            </svg>
                                        </x-slot>
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>

                        </x-dropdown>
                    </div>
                </div>
            </header>

            @props(['pagePadding' => 'sm:px-1'])
            <div class="flex-1 overflow-y-auto pt-2 pb-2 {{ $pagePadding }} scroll-area scroll-smooth">
                @if (isset($header))
                <div class="mb-4">
                    {{ $header }}
                </div>
                @endif
                @if (request()->routeIs('dashboard'))
                <h1 class="text-xl font-extrabold text-gray-900 mb-4 mt-4">
                    Selamat Datang Kembali, {{ Auth::user()->name }}
                </h1>
                @endif
                {{ $slot }}
            </div>

            <footer class="bg-white p-4 text-left flex border-t border-gray-50">
                <p class="text-xs text-black px-1">© 2025</p>
                <p class="text-xs text-red-500">BUMDes Dwi Amertha Sari</p>
            </footer>
        </main>
    </div>

    {{-- Script toggle sidebar & overlay --}}
    <script>
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('overlay');

    // Toggle sidebar & overlay saat tombol diklik
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    // Tutup sidebar saat overlay diklik
    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
    </script>

    {{-- Dropdown Filter Status dan jenis ternak --}}
    <script>
    function createDropdown(labelDefault, width = 80) {
        return {
            open: false,
            top: 0,
            left: 0,
            minWidth: width,
            label: labelDefault,
            toggle() {
                this.open = !this.open;
                if (this.open) setTimeout(() => this.position(), 0);
            },
            position() {
                const container = this.$el;
                const rect = container.getBoundingClientRect();
                const scrollX = window.scrollX || window.pageXOffset;
                const scrollY = window.scrollY || window.pageYOffset;

                const dropdownWidth = this.minWidth;

                // selalu center di tengah kolom th
                let calcLeft = rect.left + scrollX + (rect.width / 2) - (dropdownWidth / 2);

                // batasi biar nggak keluar layar
                const maxLeft = Math.max(8, window.innerWidth - dropdownWidth - 8);

                this.left = Math.max(8, Math.min(calcLeft, maxLeft));
                this.top = rect.bottom + scrollY + 6; // jarak kecil dari th
            },
            select(option) {
                this.label = option;
                this.close();
                console.log(`Filter ${this.label} dipilih:`, option);
            },
            close() {
                this.open = false;
            }
        }
    }

    </script>

    {{-- Include script tambahan dari halaman lain --}}

    @php
        // Cek semua kemungkinan notifikasi dan siapkan variabelnya
        $notification = null;
        if (session()->has('success_tahap')) {
            $notification = ['icon' => 'success', 'title' => 'Berhasil!', 'text' => session('success_tahap')];
        } elseif (session()->has('deleted_tahap')) {
            $notification = ['icon' => 'success', 'title' => 'Berhasil Dihapus!', 'text' => session('deleted_tahap')];
        } elseif (session()->has('error_tahap')) {
            $notification = ['icon' => 'error', 'title' => 'Gagal!', 'text' => session('error_tahap')];
        } elseif (session()->has('error_delete_tahap')) {
            $notification = ['icon' => 'error', 'title' => 'Gagal Menghapus!', 'text' => session('error_delete_tahap')];
        } elseif (session()->has('success')) {
            $notification = ['icon' => 'success', 'title' => 'Berhasil!', 'text' => session('success')];
        } elseif (session()->has('error')) {
            $notification = ['icon' => 'error', 'title' => 'Gagal!', 'text' => session('error')];
        } elseif (session()->has('info')) {
            $notification = ['icon' => 'info', 'title' => 'Info', 'text' => session('info')];
        } elseif (session()->has('warning')) {
            $notification = ['icon' => 'warning', 'title' => 'Peringatan', 'text' => session('warning')];
        }
        // Tambahkan notifikasi lain di sini jika ada...
        // elseif (session()->has('nama_notifikasi_lain')) { ... }
    @endphp

    @if ($notification)
    <script>
        const notificationData = @json($notification);
        // console.log('NotificationData:', notificationData);
        
        // Fallback defaults to ensure title/text always visible
        const title = notificationData.title && notificationData.title.trim() ? notificationData.title : 'Informasi';
        const text = notificationData.text && String(notificationData.text).trim() ? String(notificationData.text) : 'Operasi berhasil diproses.';

        Swal.fire({
            icon: notificationData.icon || 'info',
            title: title,
            html: text, // use html to support richer messages from backend
            timer: 3500,
            showConfirmButton: false
        });
    </script>
    @endif

    @stack('scripts')

</body>

</html>