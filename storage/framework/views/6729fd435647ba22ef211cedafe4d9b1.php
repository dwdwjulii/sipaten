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
    <div class="py-6" x-data="{ filterOpen: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                     

            
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
                <h1 class="text-2xl font-bold text-gray-900 shrink-0">
                    Tugas Pencatatan
                </h1>

                
                <button @click="filterOpen = !filterOpen"
                        class="md:hidden mt-2 w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium border rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M4 6h16M7 12h10M10 18h4"/>
                    </svg>
                    Filter & Search
                </button>

                
                <div class="hidden md:flex md:items-center md:justify-end md:gap-3 md:w-full">
                    <form action="<?php echo e(route('pencatatan.index')); ?>" method="GET" class="flex items-center gap-3">

                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-44 pl-4 pr-2 py-1.5 text-sm bg-white border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                                <span class="text-left truncate">
                                    <?php echo e($tahapDipilih ? $tahapDipilih->label : 'Semua Tahap'); ?>

                                </span>
                                <svg class="w-5 h-5 text-gray-700 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition
                                x-cloak class="absolute z-10 mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg">
                                <ul class="max-h-56 overflow-y-auto text-sm text-gray-800">
                                    <li>
                                        <a href="<?php echo e(route('pencatatan.index', request()->except('tahap_id', 'page'))); ?>" class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                            Semua Tahap
                                        </a>
                                    </li>
                                    <?php $__currentLoopData = $tahaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a href="<?php echo e(route('pencatatan.index', array_merge(request()->except('page'), ['tahap_id' => $tahap->id]))); ?>" class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                                <?php echo e($tahap->label); ?>

                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                        
                        <?php
                            $statusDipilihLabel = 'Semua Status'; // Default
                            if (request('status') == 'sudah_dicatat') {
                                $statusDipilihLabel = 'Sudah Dicatat';
                            } elseif (request('status') == 'belum_dicatat') {
                                $statusDipilihLabel = 'Belum Dicatat';
                            }
                        ?>

                        <div x-data="{ open: false }" class="relative">
                            
                            <button type="button" @click="open = !open"
                                class="flex items-center justify-between w-44 pl-4 pr-2 py-1.5 text-sm bg-white border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                                <span class="text-left truncate">
                                    <?php echo e($statusDipilihLabel); ?>

                                </span>
                                <svg class="w-5 h-5 text-gray-700 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            
                            <div x-show="open" @click.away="open = false" x-transition
                                x-cloak class="absolute z-10 mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg">
                                <ul class="max-h-56 overflow-y-auto text-sm text-gray-800 text-xs">
                                    
                                    <li>
                                        <a href="<?php echo e(route('pencatatan.index', request()->except('status', 'page'))); ?>"
                                            class="block w-full text-left text-xs px-4 py-2 text-sm hover:bg-gray-100">
                                            Semua Status
                                        </a>
                                    </li>
                                    
                                    <li>
                                        <a href="<?php echo e(route('pencatatan.index', array_merge(request()->except('page'), ['status' => 'sudah_dicatat']))); ?>"
                                            class="block w-full text-left text-xs px-4 py-2 text-sm hover:bg-gray-100">
                                            Sudah Dicatat
                                        </a>
                                    </li>
                                    
                                    <li>
                                        <a href="<?php echo e(route('pencatatan.index', array_merge(request()->except('page'), ['status' => 'belum_dicatat']))); ?>"
                                            class="block w-full text-left text-xs px-4 py-2 text-sm hover:bg-gray-100">
                                            Belum Dicatat
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        
                        <div class="relative">
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama anggota..."
                                class="w-52 pl-4 pr-8 py-1.5 text-sm border border-gray-200 placeholder:text-gray-800 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                            <button type="submit" class="absolute inset-y-0 right-0 px-3 text-gray-800 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>

                        
                        <a href="<?php echo e(route('pencatatan.index')); ?>" 
                        class="px-3 py-1.5 text-sm text-gray-600 hover:text-blue-800 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            
            <div x-show="filterOpen" x-transition class="md:hidden mb-4">
                <form action="<?php echo e(route('pencatatan.index')); ?>" method="GET"
                    class="flex flex-col gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">

                    <?php
                        // Menyiapkan data untuk Alpine.js, menambahkan 'Semua Tahap' di awal
                        $tahapOptions = $tahaps->mapWithKeys(function ($tahap) {
                            return [$tahap->id => $tahap->label];
                        })->prepend('Semua Tahap', '');
                    ?>
                    <div x-data="{
                            open: false,
                            selectedTahap: '<?php echo e(request('tahap_id', '')); ?>',
                            tahapLabels: <?php echo e(json_encode($tahapOptions)); ?>

                        }" class="relative">

                        <input type="hidden" name="tahap_id" x-model="selectedTahap">

                        <button type="button" @click="open = !open"
                                class="flex items-center justify-between w-full pl-4 pr-2 py-1.5 text-sm bg-white border border-gray-200 rounded-lg">
                            <span x-text="tahapLabels[selectedTahap]" class="truncate"></span>
                            <svg class="w-5 h-5 text-gray-800 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                            <ul class="max-h-56 overflow-y-auto text-sm text-gray-800">
                                <?php $__currentLoopData = $tahapOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <button type="button" @click="selectedTahap = '<?php echo e($id); ?>'; open = false" class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                            <?php echo e($label); ?>

                                        </button>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                    
                    <?php
                        // Menyiapkan data untuk 'Status'
                        $statusOptions = [
                            '' => 'Semua Status',
                            'sudah_dicatat' => 'Sudah Dicatat',
                            'belum_dicatat' => 'Belum Dicatat',
                        ];
                    ?>
                    <div x-data="{
                            open: false,
                            selectedStatus: '<?php echo e(request('status', '')); ?>',
                            statusLabels: <?php echo e(json_encode($statusOptions)); ?>

                        }" class="relative">

                        <input type="hidden" name="status" x-model="selectedStatus">

                        <button type="button" @click="open = !open"
                            class="flex items-center justify-between w-full pl-4 pr-2 py-1.5 text-sm bg-white border border-gray-200 rounded-lg">
                            <span x-text="statusLabels[selectedStatus]" class="truncate"></span>
                            <svg class="w-5 h-5 text-gray-800 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition x-cloak
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                            <ul class="max-h-56 overflow-y-auto text-sm text-gray-800">
                                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <button type="button" @click="selectedStatus = '<?php echo e($id); ?>'; open = false"
                                            class="block w-full text-left px-4 py-2 text-xs hover:bg-gray-100">
                                            <?php echo e($label); ?>

                                        </button>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>

                    
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama anggota..."
                            class="w-full pl-4 pr-8 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                        <span class="absolute inset-y-0 right-0 px-3 flex items-center placeholder:text-gray-800 text-gray-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                    </div>

                    
                    <div class="flex justify-end items-center gap-3 mt-2">
                        <a href="<?php echo e(route('pencatatan.index')); ?>"
                            class="px-4 py-1.5 text-sm text-gray-600 hover:text-blue-800 border border-gray-200 rounded-lg hover:bg-gray-50">
                            Reset
                        </a>
                        <button type="submit"
                                class="px-4 py-1.5 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700">
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>

            
            <div class="bg-white shadow rounded-sm p-4 sm:p-6">
                <?php if($locked ?? false): ?>
                    
                    <div class="text-center py-16 bg-white border-2 border-dashed rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Periode ini sudah diarsip</h3>
                        <p class="mt-1 text-sm text-gray-500">Tunggu admin memulai pencatatan baru bulan ini.</p>
                    </div>
                <?php else: ?>
                    <?php $__empty_1 = true; $__currentLoopData = $anggotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if($loop->first): ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        <?php endif; ?>

                        
                        
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-500 transition-all duration-300 flex flex-col">

                            
                            
                            
                            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-700">
                                    <?php echo e($anggota->tahap?->label ?? 'Tanpa Tahap'); ?>

                                </span>

                                
                                <?php if($anggota->latestPencatatan): ?>
                                    <?php if($anggota->latestPencatatan->is_locked): ?>
                                        
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">
                                            Arsip
                                        </span>
                                    <?php else: ?>
                                        
                                        <?php
                                            $pencatatan = $anggota->latestPencatatan;
                                            
                                            // Hitung ternak aktif SAAT INI (real-time)
                                            $jumlahTernakAktifSekarang = $anggota->ternaks()
                                                ->where('status_aktif', 'aktif')
                                                ->count();
                                            
                                            // Hitung detail yang sudah dicatat DAN ternaknya masih aktif
                                            $jumlahDetailAktifTercatat = 0;
                                            $pernahAdaDetail = false;
                                            
                                            if ($pencatatan) {
                                                // Cek apakah pernah ada detail yang terisi
                                                $pernahAdaDetail = $pencatatan->details->filter(function($detail) {
                                                    return !empty($detail->kondisi_ternak);
                                                })->isNotEmpty();
                                                
                                                // Hitung detail yang terisi DAN ternaknya masih aktif
                                                $jumlahDetailAktifTercatat = $pencatatan->details->filter(function($detail) {
                                                    return !empty($detail->kondisi_ternak) && 
                                                        $detail->ternak && 
                                                        $detail->ternak->status_aktif === 'aktif';
                                                })->count();
                                            }
                                        ?>

                                        <?php if($jumlahTernakAktifSekarang > 0 && !$pernahAdaDetail): ?>
                                            
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                                Belum Dicatat
                                            </span>
                                        <?php elseif($pernahAdaDetail && $jumlahDetailAktifTercatat < $jumlahTernakAktifSekarang): ?>
                                            
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-orange-100 text-orange-800">
                                                Perlu Update
                                            </span>
                                        <?php else: ?>
                                            
                                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                                Sudah Dicatat
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                                        Tidak Ada Data
                                    </span>
                                <?php endif; ?>
                            </div>

                            
                            
                            
                            <div class="p-4 flex-grow">
                                <p class="text-base font-bold text-gray-800 line-clamp-2 leading-tight mb-3">
                                    <?php echo e($anggota->nama); ?>

                                </p>
                                <div class="space-y-2 text-sm text-gray-600">
                                    
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
                                            <rect x="8" y="2" width="8" height="4" rx="1" ry="1"/>
                                        </svg>
                                        <span>Jenis: <span class="font-semibold"><?php echo e(Str::ucfirst($anggota->jenis_ternak)); ?></span></span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span><?php echo e($anggota->lokasi_kandang); ?></span>
                                    </div>
                                </div>
                            </div>

                            
                            
                            
                            <div class="p-4 mt-auto border-t rounded-b-lg border-gray-200 bg-gray-50">
                                
                                <?php if($anggota->latestPencatatan): ?>
                                    <?php if($anggota->latestPencatatan->is_locked): ?>
                                        <button disabled class="block w-full text-center px-3 py-2 text-sm font-semibold text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">
                                            Terkunci
                                        </button>
                                    <?php elseif($anggota->latestPencatatan->details->isEmpty()): ?>
                                        <a href="<?php echo e(route('pencatatan.create', $anggota)); ?>" class="block w-full text-center px-3 py-2 text-sm font-semibold text-white bg-green-600 rounded-md hover:bg-green-700">
                                            + Buat Catatan
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('pencatatan.edit', $anggota->latestPencatatan)); ?>" class="block w-full text-center px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                            Lihat / Edit Catatan
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    
                                    <a href="<?php echo e(route('pencatatan.create', $anggota)); ?>" class="block w-full text-center px-3 py-2 text-sm font-semibold text-white bg-green-600 rounded-md hover:bg-green-700">
                                        + Buat Catatan
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($loop->last): ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-16 bg-white border-2 border-dashed rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak Ada Tugas</h3>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada data anggota yang cocok dengan filter Anda.</p>
                        </div>
                    <?php endif; ?>

                    <?php if($anggotas->count() > 0): ?>
                        <div class="mt-6">
                            <?php echo e($anggotas->appends(request()->query())->links()); ?>

                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
<?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/petugas/pencatatan.blade.php ENDPATH**/ ?>