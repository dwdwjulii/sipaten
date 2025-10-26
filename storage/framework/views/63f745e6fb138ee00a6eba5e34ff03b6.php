
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
            <div class="bg-white shadow rounded-sm p-6">

                
                <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center md:justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">
                        Kelola Anggota
                    </h2>

                    
                    <div class="flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginal61542037d001e2034791c9aff5866543 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal61542037d001e2034791c9aff5866543 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-bar','data' => ['action' => route('anggota.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('search-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('anggota.index'))]); ?>
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
                                class="flex items-center space-x-1 px-3 w-36 justify-between py-1.5 text-sm font-normal text-gray-800 bg-white border border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">
                                <span class="whitespace-normal break-words text-left">
                                    <?php echo e($tahapDipilih ? $tahapDipilih->label : 'Pilih Tahap'); ?>

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
                                    
                                    <li class="border-b border-gray-100">
                                        <form action="<?php echo e(route('anggota.index')); ?>" method="GET">
                                            <button type="submit"
                                                class="w-full text-left px-4 py-2 text-xs hover:bg-green-100 hover:rounded-t-md">
                                                Semua Tahap
                                            </button>
                                        </form>
                                    </li>

                                    
                                    <?php $__currentLoopData = $tahaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tahap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="group">
                                        <div
                                            class="flex items-center justify-between w-full px-4 py-2 hover:bg-green-50">
                                            
                                            <form action="<?php echo e(route('anggota.index')); ?>" method="GET" class="flex-grow">
                                                <input type="hidden" name="tahap_id" value="<?php echo e($tahap->id); ?>">
                                                <button type="submit"
                                                    class="w-full text-left text-xs whitespace-normal break-words">
                                                    <?php echo e($tahap->label); ?>

                                                </button>
                                            </form>

                                            
                                            <div>
                                                <form action="<?php echo e(route('tahap.destroy', $tahap->id)); ?>" method="POST"
                                                    class="delete-tahap-form"
                                                    data-anggota-count="<?php echo e($tahap->anggotas_count); ?>"
                                                    data-tahap-name="<?php echo e($tahap->label); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
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
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <li class="border-t rounded-b-lg border-gray-100 sticky bottom-0 bg-white">
                                        <?php if (isset($component)) { $__componentOriginal7753f546013a9c837e857a84028a324e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7753f546013a9c837e857a84028a324e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.anggota.tahap-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('anggota.tahap-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7753f546013a9c837e857a84028a324e)): ?>
<?php $attributes = $__attributesOriginal7753f546013a9c837e857a84028a324e; ?>
<?php unset($__attributesOriginal7753f546013a9c837e857a84028a324e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7753f546013a9c837e857a84028a324e)): ?>
<?php $component = $__componentOriginal7753f546013a9c837e857a84028a324e; ?>
<?php unset($__componentOriginal7753f546013a9c837e857a84028a324e); ?>
<?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                        </div>


                        
                        <?php if($tahapDipilih): ?>
                        <?php if (isset($component)) { $__componentOriginal7df209969f985dc84dcf628cdb83529b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7df209969f985dc84dcf628cdb83529b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.anggota.create-modal','data' => ['tahapId' => $tahapDipilih->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('anggota.create-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tahapId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tahapDipilih->id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7df209969f985dc84dcf628cdb83529b)): ?>
<?php $attributes = $__attributesOriginal7df209969f985dc84dcf628cdb83529b; ?>
<?php unset($__attributesOriginal7df209969f985dc84dcf628cdb83529b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7df209969f985dc84dcf628cdb83529b)): ?>
<?php $component = $__componentOriginal7df209969f985dc84dcf628cdb83529b; ?>
<?php unset($__componentOriginal7df209969f985dc84dcf628cdb83529b); ?>
<?php endif; ?>
                        <?php else: ?>
                        <button disabled
                            class="inline-flex items-center px-2 py-1.5 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-4 h-4 mr-1">
                                <path
                                    d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Tambah Anggota
                        </button>
                        <?php endif; ?>

                    </div>
                </div>

                
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
                            <?php $__empty_1 = true; $__currentLoopData = $anggotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="w-[4%] px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                    <?php echo e($loop->iteration + ($anggotas->currentPage() - 1) * $anggotas->perPage()); ?>

                                </td>
                                <td class="w-[18%] px-2 py-3 border border-gray-100 text-gray-900 text-left truncate max-w-[250px]"
                                    title="<?php echo e($anggota->nama); ?>">
                                    <?php echo e($anggota->nama); ?>

                                </td>
                                <td class="w-[6%] px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                    <?php echo e($anggota->tahap?->label ?? 'Tidak ada tahap'); ?>

                                </td>
                                <td class="w-[8%] px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                    <?php echo e($anggota->jenis_ternak); ?>

                                </td>
                                <td class="w-[9%] px-2 py-3 border border-gray-100  text-gray-900 text-center">
                                    <?php echo e($anggota->jumlah_induk); ?>

                                </td>
                                <td class="w-[14%] px-2 py-3 border border-gray-100 text-gray-900 text-left">
                                    Rp <?php echo e(number_format($anggota->total_harga_induk, 0, ',', '.')); ?>

                                </td>
                                <td class="w-[10%] px-2 py-3 border  border-gray-100 text-gray-900 text-center">
                                    <?php echo e($anggota->no_hp); ?>

                                </td>
                                <td class="px-2 py-3 border border-gray-100 text-gray-900 text-left truncate max-w-[150px]"
                                    title="<?php echo e($anggota->lokasi_kandang); ?>">
                                    <?php echo e($anggota->lokasi_kandang); ?>

                                </td>
                                <td class="w-[8%] px-2 py-3 border border-gray-100  text-center">
                                    <?php if($anggota->status == 'aktif'): ?>
                                    <span
                                        class="px-2 py-0 text-xs rounded-lg bg-green-200 text-green-700 border border-green-400 font-semibold">Aktif</span>
                                    <?php else: ?>
                                    <span
                                        class="px-2 py-0 text-xs rounded-lg bg-red-200 text-red-700 border border-red-400 font-semibold">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-2 py-3 border border-gray-100 ">
                                    <div class="flex justify-center items-center space-x-2">
                                        <?php if (isset($component)) { $__componentOriginal1b34be25317d33ef4d17407613961a1f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1b34be25317d33ef4d17407613961a1f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.anggota.detail-modal','data' => ['anggota' => $anggota]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('anggota.detail-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['anggota' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($anggota)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1b34be25317d33ef4d17407613961a1f)): ?>
<?php $attributes = $__attributesOriginal1b34be25317d33ef4d17407613961a1f; ?>
<?php unset($__attributesOriginal1b34be25317d33ef4d17407613961a1f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1b34be25317d33ef4d17407613961a1f)): ?>
<?php $component = $__componentOriginal1b34be25317d33ef4d17407613961a1f; ?>
<?php unset($__componentOriginal1b34be25317d33ef4d17407613961a1f); ?>
<?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginal2140d3809f5bfe653fe3b4d0d41e63ca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2140d3809f5bfe653fe3b4d0d41e63ca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.anggota.edit-modal','data' => ['anggota' => $anggota,'tahaps' => $tahaps,'currentTahapId' => $tahapDipilih?->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('anggota.edit-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['anggota' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($anggota),'tahaps' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tahaps),'currentTahapId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tahapDipilih?->id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2140d3809f5bfe653fe3b4d0d41e63ca)): ?>
<?php $attributes = $__attributesOriginal2140d3809f5bfe653fe3b4d0d41e63ca; ?>
<?php unset($__attributesOriginal2140d3809f5bfe653fe3b4d0d41e63ca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2140d3809f5bfe653fe3b4d0d41e63ca)): ?>
<?php $component = $__componentOriginal2140d3809f5bfe653fe3b4d0d41e63ca; ?>
<?php unset($__componentOriginal2140d3809f5bfe653fe3b4d0d41e63ca); ?>
<?php endif; ?>
                                        <?php if (isset($component)) { $__componentOriginal5d5da5ebdbc90aab20d85eeaaed04944 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5d5da5ebdbc90aab20d85eeaaed04944 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.anggota.delete-modal','data' => ['anggota' => $anggota,'currentTahapId' => $tahapDipilih?->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('anggota.delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['anggota' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($anggota),'currentTahapId' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tahapDipilih?->id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5d5da5ebdbc90aab20d85eeaaed04944)): ?>
<?php $attributes = $__attributesOriginal5d5da5ebdbc90aab20d85eeaaed04944; ?>
<?php unset($__attributesOriginal5d5da5ebdbc90aab20d85eeaaed04944); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5d5da5ebdbc90aab20d85eeaaed04944)): ?>
<?php $component = $__componentOriginal5d5da5ebdbc90aab20d85eeaaed04944; ?>
<?php unset($__componentOriginal5d5da5ebdbc90aab20d85eeaaed04944); ?>
<?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-gray-500">
                                    <?php if($tahapDipilih): ?>
                                    Tidak ada data anggota pada tahap "<?php echo e($tahapDipilih->label); ?>".
                                    <?php else: ?>
                                    Tidak ada data anggota. Silakan pilih tahap terlebih dahulu.
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
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


<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/anggota.blade.php ENDPATH**/ ?>