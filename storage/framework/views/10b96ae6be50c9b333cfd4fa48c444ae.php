

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/datepicker.min.js"></script>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
    [x-cloak] {
        display: none !important
    }
    </style>
</head>


<body class="font-sans antialiased">

    <div class="flex h-screen bg-neutral-100 dark:bg-gray-900">

        <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

        <main class="flex flex-1 flex-col min-w-0 min-h-0">

            <header class="bg-white sm:px-3 py-3 border-b shadow-sm">
                <div class="flex items-center justify-between">
                    
                    <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-gray-950 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd"
                                d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    
                    <div class="relative ml-auto">
                        <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '32']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '32']); ?>

                             <?php $__env->slot('trigger', null, []); ?> 
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div><?php echo e(Auth::user()->name); ?></div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                             <?php $__env->endSlot(); ?>

                             <?php $__env->slot('content', null, []); ?> 
                                
                                <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('profile.edit')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile.edit'))]); ?>
                                     <?php $__env->slot('icon', null, []); ?> 
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-user-pen">
                                            <path d="M11.5 15H7a4 4 0 0 0-4 4v2" />
                                            <path
                                                d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                                            <circle cx="10" cy="7" r="4" />
                                        </svg>
                                     <?php $__env->endSlot(); ?>
                                    <?php echo e(__('Profil')); ?>

                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>

                                
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('logout'),'onclick' => 'event.preventDefault(); this.closest(\'form\').submit();']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('logout')),'onclick' => 'event.preventDefault(); this.closest(\'form\').submit();']); ?>
                                         <?php $__env->slot('icon', null, []); ?> 
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-log-out">
                                                <path d="m16 17 5-5-5-5" />
                                                <path d="M21 12H9" />
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            </svg>
                                         <?php $__env->endSlot(); ?>
                                        <?php echo e(__('Log Out')); ?>

                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
                                </form>
                             <?php $__env->endSlot(); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
                    </div>
                </div>
            </header>

            <?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['pagePadding' => 'sm:px-1']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['pagePadding' => 'sm:px-1']); ?>
<?php foreach (array_filter((['pagePadding' => 'sm:px-1']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
            <div class="flex-1 overflow-y-auto pt-2 pb-2 <?php echo e($pagePadding); ?> scroll-area scroll-smooth">
                <?php if(isset($header)): ?>
                <div class="mb-4">
                    <?php echo e($header); ?>

                </div>
                <?php endif; ?>
                <?php if(request()->routeIs('dashboard')): ?>
                <h1 class="text-xl font-extrabold text-gray-900 mb-4 mt-4">
                    Selamat Datang Kembali, <?php echo e(Auth::user()->name); ?>

                </h1>
                <?php endif; ?>
                <?php echo e($slot); ?>

            </div>

            <footer class="bg-white p-4 text-left flex border-t border-gray-50">
                <p class="text-xs text-black px-1">© 2025</p>
                <p class="text-xs text-red-500">BUMDes Dwi Amertha Sari</p>
            </footer>
        </main>
    </div>

    
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

    

    <?php
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
    ?>

    <?php if($notification): ?>
    <script>
        const notificationData = <?php echo json_encode($notification, 15, 512) ?>;
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
    <?php endif; ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html><?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/layouts/app.blade.php ENDPATH**/ ?>