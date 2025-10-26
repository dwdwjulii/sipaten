

<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-4">

            
            
            
            <div class="mb-4 space-y-3">

                <?php if($statusKeseluruhan == 'error'): ?>
                    
                    <?php if (isset($component)) { $__componentOriginal2920fece7093889c66f6edad9ca62216 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2920fece7093889c66f6edad9ca62216 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-status','data' => ['type' => 'error','title' => 'Status Catatan Ternak: Perlu Tindakan!','messages' => [
                        ['text' => 'Masih terdapat laporan data catatan ternak yang belum dilengkapi oleh Petugas Lapangan.', 'status' => 'error']
                    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('alert-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'error','title' => 'Status Catatan Ternak: Perlu Tindakan!','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                        ['text' => 'Masih terdapat laporan data catatan ternak yang belum dilengkapi oleh Petugas Lapangan.', 'status' => 'error']
                    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2920fece7093889c66f6edad9ca62216)): ?>
<?php $attributes = $__attributesOriginal2920fece7093889c66f6edad9ca62216; ?>
<?php unset($__attributesOriginal2920fece7093889c66f6edad9ca62216); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2920fece7093889c66f6edad9ca62216)): ?>
<?php $component = $__componentOriginal2920fece7093889c66f6edad9ca62216; ?>
<?php unset($__componentOriginal2920fece7093889c66f6edad9ca62216); ?>
<?php endif; ?>
                
                <?php elseif($statusKeseluruhan == 'warning'): ?>
                    
                    <?php if (isset($component)) { $__componentOriginal2920fece7093889c66f6edad9ca62216 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2920fece7093889c66f6edad9ca62216 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-status','data' => ['type' => 'warning','title' => 'Status Catatan: Perlu Update','messages' => [
                        ['text' => 'Beberapa data ternak baru ditambahkan oleh Admin dan belum dicatat oleh Petugas. Harap petugas melengkapi data tersebut sebelum arsip.', 'status' => 'warning']
                    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('alert-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning','title' => 'Status Catatan: Perlu Update','messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                        ['text' => 'Beberapa data ternak baru ditambahkan oleh Admin dan belum dicatat oleh Petugas. Harap petugas melengkapi data tersebut sebelum arsip.', 'status' => 'warning']
                    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2920fece7093889c66f6edad9ca62216)): ?>
<?php $attributes = $__attributesOriginal2920fece7093889c66f6edad9ca62216; ?>
<?php unset($__attributesOriginal2920fece7093889c66f6edad9ca62216); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2920fece7093889c66f6edad9ca62216)): ?>
<?php $component = $__componentOriginal2920fece7093889c66f6edad9ca62216; ?>
<?php unset($__componentOriginal2920fece7093889c66f6edad9ca62216); ?>
<?php endif; ?>
                
                <?php elseif($statusKeseluruhan == 'success' && !$statusArsip): ?>
                    
                    <?php if (isset($component)) { $__componentOriginal2920fece7093889c66f6edad9ca62216 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2920fece7093889c66f6edad9ca62216 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-status','data' => ['type' => 'success','title' => 'Status Catatan: Semua Anggota Sudah Dicatat','messages' => 'Semua catatan ternak telah lengkap untuk periode bulan ini.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('alert-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success','title' => 'Status Catatan: Semua Anggota Sudah Dicatat','messages' => 'Semua catatan ternak telah lengkap untuk periode bulan ini.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2920fece7093889c66f6edad9ca62216)): ?>
<?php $attributes = $__attributesOriginal2920fece7093889c66f6edad9ca62216; ?>
<?php unset($__attributesOriginal2920fece7093889c66f6edad9ca62216); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2920fece7093889c66f6edad9ca62216)): ?>
<?php $component = $__componentOriginal2920fece7093889c66f6edad9ca62216; ?>
<?php unset($__componentOriginal2920fece7093889c66f6edad9ca62216); ?>
<?php endif; ?>
                    
                    <?php if (isset($component)) { $__componentOriginal2920fece7093889c66f6edad9ca62216 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2920fece7093889c66f6edad9ca62216 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.alert-status','data' => ['type' => 'warning','title' => 'Tindakan Diperlukan: Arsipkan Laporan','messages' => 'Segera arsipkan laporan agar data tersimpan dengan aman dan siap untuk periode berikutnya.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('alert-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning','title' => 'Tindakan Diperlukan: Arsipkan Laporan','messages' => 'Segera arsipkan laporan agar data tersimpan dengan aman dan siap untuk periode berikutnya.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2920fece7093889c66f6edad9ca62216)): ?>
<?php $attributes = $__attributesOriginal2920fece7093889c66f6edad9ca62216; ?>
<?php unset($__attributesOriginal2920fece7093889c66f6edad9ca62216); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2920fece7093889c66f6edad9ca62216)): ?>
<?php $component = $__componentOriginal2920fece7093889c66f6edad9ca62216; ?>
<?php unset($__componentOriginal2920fece7093889c66f6edad9ca62216); ?>
<?php endif; ?>
                
                <?php elseif($statusKeseluruhan == 'success' && $statusArsip): ?>
                    
                    <div class="flex rounded-sm overflow-hidden bg-emerald-50">

                    
                    <div class="flex items-center justify-center bg-green-500 px-2">
                        <svg class="w-10 h-10 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 
                            00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 
                            00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    
                    <div class="p-4 flex-1">
                        <h2 class="text-lg font-semibold text-green-900">Status Pencatatan</h2>
                        <div class="mt-2 text-sm text-green-900">
                            <p>Data catatan bulan ini telah diarsipkan dan tidak dapat diubah. Mulai periode pencatatan
                                baru dengan klik tombol di bawah.</p>
                        </div>
                        <div class="mt-4">
                            <form action="<?php echo e(route('pencatatan.reset')); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
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
                <?php endif; ?>
            </div>

            
            
            
            <div class="p-6 bg-white shadow rounded-sm">

                
                <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Kelola Catatan</h2>

                    <div class="flex items-center gap-2">
                        
                        <?php if (isset($component)) { $__componentOriginal61542037d001e2034791c9aff5866543 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal61542037d001e2034791c9aff5866543 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-bar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('search-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal61542037d001e2034791c9aff5866543)): ?>
<?php $attributes = $__attributesOriginal61542037d001e2034791c9aff5866543; ?>
<?php unset($__attributesOriginal61542037d001e2034791c9aff5866543); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal61542037d001e2034791c9aff5866543)): ?>
<?php $component = $__componentOriginal61542037d001e2034791c9aff5866543; ?>
<?php unset($__componentOriginal61542037d001e2034791c9aff5866543); ?>
<?php endif; ?>

                        
                        
                        
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open"
                                class="flex items-center justify-between w-36 px-3 py-1.5 space-x-1 text-sm font-normal text-gray-800 bg-white border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                                <span class="text-left break-words whitespace-normal">
                                    <?php echo e($tahapDipilih ? $tahapDipilih->label : 'Semua Tahap'); ?>

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
                                <ul class="max-h-60 overflow-y-auto text-sm text-gray-700">
                                    <li>
                                        <a href="<?php echo e(route('pencatatan.index')); ?>"
                                            class="block px-3 py-2 hover:bg-green-100 hover:rounded-md">
                                            Semua Tahap
                                        </a>
                                    </li>
                                    <?php $__currentLoopData = $tahaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(route('pencatatan.index', ['tahap_id' => $tahap->id])); ?>"
                                            class="block px-3 py-2 break-words hover:bg-green-100 hover:rounded-lg whitespace-normal <?php if($tahapDipilih && $tahapDipilih->id == $tahap->id): ?> bg-green-100 <?php endif; ?> ">
                                            <?php echo e($tahap->label); ?>

                                        </a>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>

                        

                        
                        
                        
                        <?php
                            $semuaTercatat = $statusKeseluruhan === 'success';
                        ?>

                        <a href="<?php echo e(route('laporan.keseluruhan.export')); ?>" target="_blank"
                            class="flex items-center justify-center px-2.5 py-1.5 space-x-1 text-sm font-medium rounded-lg 
                            <?php echo e($semuaTercatat ? 'text-white bg-green-600 hover:bg-green-700' : 'text-gray-400 bg-gray-200 cursor-not-allowed'); ?>"
                            <?php echo e($semuaTercatat ? '' : 'aria-disabled=true tabindex=-1'); ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 17V3m-6 8l6 6l6-6m-9 9h6" />
                            </svg>
                            <span>Export</span>
                        </a>

                        <button type="button" id="btnArsip"
                            class="flex items-center justify-center px-3 py-1.5 space-x-1 text-sm font-medium rounded-lg w-20 
                            <?php echo e($semuaTercatat ? 'text-white bg-yellow-500 hover:bg-yellow-700' : 'text-gray-400 bg-gray-200 cursor-not-allowed'); ?>"
                            <?php echo e($semuaTercatat ? '' : 'disabled'); ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 8v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8m2-4h14a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2m5 6h4" />
                            </svg>
                            <span>Arsip</span>
                        </button>

                        <form id="formArsip" action="<?php echo e(route('laporan.arsip.keseluruhan')); ?>" method="POST" class="hidden">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </div>

                
                
                
                <form action="<?php echo e(url()->current()); ?>" method="GET"> 
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
                                    
                                    
                                    <th scope="col" class="relative px-4 py-3 text-center border border-gray-200">
                                        <div x-data="{ open: false }" @click.away="open = false" class="relative inline-block text-left">
                                            <button @click="open = !open" type="button" class="inline-flex items-center justify-center w-full text-sm">
                                                <span>Status Laporan</span>
                                                <svg class="w-5 h-5 ml-1 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                    
                                            <div x-show="open"
                                                x-transition
                                                class="absolute right-0 z-20 w-32 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5"
                                                style="display: none;">
                                                <div class="py-1">
                                                    <?php
                                                        // Helper untuk membuat URL filter sambil mempertahankan parameter lain
                                                        function build_filter_url($key, $value) {
                                                            $query = array_merge(request()->query(), [$key => $value, 'page' => 1]);
                                                            if (empty($value)) {
                                                                unset($query[$key]);
                                                            }
                                                            return url()->current() . '?' . http_build_query($query);
                                                        }
                                                    ?>
                                                    <a href="<?php echo e(build_filter_url('status_laporan', '')); ?>" class="block w-full px-4 py-2 text-xs text-left text-gray-700 hover:bg-gray-100 <?php echo e(!request('status_laporan') ? 'bg-gray-100 font-semibold' : ''); ?>">
                                                        Semua
                                                    </a>
                                                    <a href="<?php echo e(build_filter_url('status_laporan', 'sudah')); ?>" class="block w-full px-4 py-2 text-xs text-left text-gray-700 hover:bg-gray-100 <?php echo e(request('status_laporan') == 'sudah' ? 'bg-gray-100 font-semibold' : ''); ?>">
                                                        Sudah Dicatat
                                                    </a>
                                                    <a href="<?php echo e(build_filter_url('status_laporan', 'belum')); ?>" class="block w-full px-4 py-2 text-xs text-left text-gray-700 hover:bg-gray-100 <?php echo e(request('status_laporan') == 'belum' ? 'bg-gray-100 font-semibold' : ''); ?>">
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
                                <?php $__empty_1 = true; $__currentLoopData = $anggotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-2 py-3 text-center border border-gray-100 whitespace-nowrap">
                                        <?php echo e($loop->iteration + ($anggotas->currentPage() - 1) * $anggotas->perPage()); ?>

                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 text-left truncate max-w-[150px]">
                                        <?php echo e($anggota->nama); ?>

                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap border-gray-100">
                                        <?php echo e($anggota->tahap?->label ?? '-'); ?>

                                    </td>
                                    <td class="px-2 py-3 text-center border border-gray-100 whitespace-nowrap">
                                        <?php echo e($anggota->jenis_ternak); ?>

                                    </td>
                                    <td class="px-2 py-3 text-left border border-gray-100 whitespace-nowrap">
                                        Rp <?php echo e(number_format($anggota->total_harga_induk ?? 0, 0, ',', '.')); ?>

                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 text-left truncate max-w-[120px]">
                                        <?php echo e($anggota->lokasi_kandang); ?>

                                    </td>
                                    <td class="px-2 py-3 text-center border border-gray-100 whitespace-nowrap">
                                        <?php echo e($anggota->latestPencatatan?->tanggal_catatan?->format('d M Y') ?? 'Belum ada'); ?>

                                    </td>
                                    <td class="px-2 py-3 text-center border border-gray-100 whitespace-nowrap">
                                        <?php
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
                                        ?>

                                        <?php if($pencatatan && $pencatatan->is_locked): ?>
                                            
                                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">
                                                Diarsipkan
                                            </span>

                                        <?php elseif($pencatatan && $detailsExistAndFilled && $jumlahTernakAktif > 0 && $jumlahDetailLengkap < $jumlahTernakAktif): ?>
                                            
                                            
                                            <span class="px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full">
                                                Perlu Update
                                            </span>

                                        <?php elseif($pencatatan && $detailsExistAndFilled): ?>
                                            
                                            
                                            
                                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                Sudah Dicatat
                                            </span>

                                        <?php else: ?>
                                            
                                            
                                            <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                                Belum Dicatat
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-center border-gray-100">
                                        <?php if($anggota->latestPencatatan): ?>
                                            <?php if (isset($component)) { $__componentOriginal4b572cbc4bbc996f29a2b2ff2693de67 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4b572cbc4bbc996f29a2b2ff2693de67 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pencatatan.detail-modal','data' => ['pencatatan' => $anggota->latestPencatatan]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('pencatatan.detail-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pencatatan' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($anggota->latestPencatatan)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4b572cbc4bbc996f29a2b2ff2693de67)): ?>
<?php $attributes = $__attributesOriginal4b572cbc4bbc996f29a2b2ff2693de67; ?>
<?php unset($__attributesOriginal4b572cbc4bbc996f29a2b2ff2693de67); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4b572cbc4bbc996f29a2b2ff2693de67)): ?>
<?php $component = $__componentOriginal4b572cbc4bbc996f29a2b2ff2693de67; ?>
<?php unset($__componentOriginal4b572cbc4bbc996f29a2b2ff2693de67); ?>
<?php endif; ?>
                                        <?php else: ?>
                                            <button disabled class="text-gray-300 cursor-not-allowed" title="Belum ada catatan untuk dilihat">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" /><circle cx="12" cy="12" r="3" /></svg>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="py-6 text-center text-gray-500">
                                        Tidak ada data anggota yang ditemukan untuk filter ini.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>


                
                
                
                
                <div class="flex items-center justify-between mt-4 text-sm text-gray-600">
                    
                    
                    <?php if (isset($component)) { $__componentOriginal82bdacedace88cf74c4bc8dcf2c2d4bd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal82bdacedace88cf74c4bc8dcf2c2d4bd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.show-entries','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('show-entries'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal82bdacedace88cf74c4bc8dcf2c2d4bd)): ?>
<?php $attributes = $__attributesOriginal82bdacedace88cf74c4bc8dcf2c2d4bd; ?>
<?php unset($__attributesOriginal82bdacedace88cf74c4bc8dcf2c2d4bd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal82bdacedace88cf74c4bc8dcf2c2d4bd)): ?>
<?php $component = $__componentOriginal82bdacedace88cf74c4bc8dcf2c2d4bd; ?>
<?php unset($__componentOriginal82bdacedace88cf74c4bc8dcf2c2d4bd); ?>
<?php endif; ?>

                    
                    <?php if($anggotas->total() > 0): ?>
                    <div>
                        Menampilkan 
                        <span class="font-bold"><?php echo e($anggotas->firstItem()); ?></span>
                        sampai 
                        <span class="font-bold"><?php echo e($anggotas->lastItem()); ?></span>
                        dari 
                        <span class="font-bold"><?php echo e($anggotas->total()); ?></span>
                        hasil
                    </div>
                    <?php endif; ?>

                    
                    <div>
                        <?php echo e($anggotas->appends(request()->query())->links()); ?>

                    </div>

                </div>
                
            </div>
        </div>
    </div>

   

    <?php $__env->startPush('scripts'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    
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
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700',
                        cancelButton: 'px-4 py-2 ml-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formArsip').submit();
                    }
                });
            });
        }

        // --- PENANGANAN ALERT DINAMIS DARI CONTROLLER ---

        <?php if(session('arsip_gagal')): ?>
            var data = <?php echo json_encode(session('arsip_gagal'), 15, 512) ?>;
            var listHtml = '<ul style="text-align: left; list-style-position: inside; padding-left: 20px;">';
            data.list.forEach(function(nama) { listHtml += '<li>' + nama + '</li>'; });
            listHtml += '</ul>';
            Swal.fire({ icon: 'error', title: data.title, html: '<p>' + data.text + '</p>' + listHtml, confirmButtonText: 'Saya Mengerti' });
        <?php endif; ?>

        <?php if(session('arsip_sukses')): ?>
            var data = <?php echo json_encode(session('arsip_sukses'), 15, 512) ?>;
            Swal.fire({
                icon: 'success', title: data.title, text: data.text,
                confirmButtonText: 'Mulai Catatan Baru', showCancelButton: true, cancelButtonText: 'Tutup', buttonsStyling: false,
                customClass: {
                    confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700',
                    cancelButton: 'px-4 py-2 ml-2 text-sm font-medium text-gray-700 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?php echo e(route('pencatatan.mulaiBaru')); ?>";
                }
            });
        <?php endif; ?>

        <?php if(session('success')): ?>
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "<?php echo e(session('success')); ?>", timer: 2500, showConfirmButton: false });
        <?php endif; ?>
        <?php if(session('error')): ?>
            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: "<?php echo e(session('error')); ?>" });
        <?php endif; ?>
        <?php if(session('info')): ?>
            Swal.fire({ icon: 'info', title: 'Informasi', text: <?php echo json_encode(session('info'), 15, 512) ?>['text'] ?? "<?php echo e(session('info')); ?>" });
        <?php endif; ?>

        <?php if(session('success_tahap')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "<?php echo e(session('success_tahap')); ?>",
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>
        
        // Alert error tambah tahap
        <?php if(session('error_tahap')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: "<?php echo e(session('error_tahap')); ?>"
            });
        <?php endif; ?>
        
        // Alert berhasil hapus tahap
        <?php if(session('deleted_tahap')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dihapus!',
                text: "<?php echo e(session('deleted_tahap')); ?>",
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>
        
        // Alert error hapus tahap
        <?php if(session('error_delete_tahap')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus',
                text: "<?php echo e(session('error_delete_tahap')); ?>"
            });
        <?php endif; ?>
    });
    </script>
    <?php $__env->stopPush(); ?> 
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>

<?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/pencatatan.blade.php ENDPATH**/ ?>