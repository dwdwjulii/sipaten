
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

                
                <div class="flex flex-col gap-3 mb-4 md:flex-row md:items-center">
                    
                    <h2 class="text-xl font-semibold text-gray-800">
                        Kelola Pengguna
                    </h2>

                    
                    <div class="flex items-center ml-auto space-x-2">
                        
                        <div class="max-w-sm w-full">
                            <?php if (isset($component)) { $__componentOriginal61542037d001e2034791c9aff5866543 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal61542037d001e2034791c9aff5866543 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search-bar','data' => ['action' => route('pengguna.index')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('search-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['action' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('pengguna.index'))]); ?>
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
                        </div>

                        
                        <div class="flex-shrink-0">
                            <?php if (isset($component)) { $__componentOriginal85529f491d54fcd5306289b224b8bdd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal85529f491d54fcd5306289b224b8bdd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pengguna.create-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('pengguna.create-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal85529f491d54fcd5306289b224b8bdd3)): ?>
<?php $attributes = $__attributesOriginal85529f491d54fcd5306289b224b8bdd3; ?>
<?php unset($__attributesOriginal85529f491d54fcd5306289b224b8bdd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal85529f491d54fcd5306289b224b8bdd3)): ?>
<?php $component = $__componentOriginal85529f491d54fcd5306289b224b8bdd3; ?>
<?php unset($__componentOriginal85529f491d54fcd5306289b224b8bdd3); ?>
<?php endif; ?>
                        </div>
                    </div>
                </div>


                
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
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                        <?php echo e($loop->iteration + ($users->currentPage() - 1) * $users->perPage()); ?>

                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 text-left truncate max-w-[150px]">
                                        <?php echo e($user->name); ?>

                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900"><?php echo e($user->email); ?></td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900">****************</td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900 text-center"><?php echo e(ucfirst($user->role)); ?></td>
                                    <td class="px-2 py-3 border border-gray-100 text-gray-900 text-center">
                                        
                                        <?php if($user->status === 'aktif'): ?> 
                                            <span class="px-2 text-xs rounded-lg text-center bg-green-200 text-green-700 border border-green-400 font-semibold min-w-[74px] inline-block">
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 text-xs rounded-lg text-center bg-red-200 text-red-700 border border-red-400 font-semibold min-w-[74px] inline-block">
                                                Non-Aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-2 py-3 border border-gray-100 whitespace-nowrap">
                                        <div class="flex justify-center space-x-2">
                                            
                                            <?php if (isset($component)) { $__componentOriginaleadccb526a026a778859e391578a8c60 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleadccb526a026a778859e391578a8c60 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pengguna.edit-modal','data' => ['user' => $user]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('pengguna.edit-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleadccb526a026a778859e391578a8c60)): ?>
<?php $attributes = $__attributesOriginaleadccb526a026a778859e391578a8c60; ?>
<?php unset($__attributesOriginaleadccb526a026a778859e391578a8c60); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleadccb526a026a778859e391578a8c60)): ?>
<?php $component = $__componentOriginaleadccb526a026a778859e391578a8c60; ?>
<?php unset($__componentOriginaleadccb526a026a778859e391578a8c60); ?>
<?php endif; ?>
                                            
                                            <?php if (isset($component)) { $__componentOriginal977aa12910ce3eacce79e3d8532e1e07 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal977aa12910ce3eacce79e3d8532e1e07 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pengguna.delete-modal','data' => ['user' => $user]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('pengguna.delete-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal977aa12910ce3eacce79e3d8532e1e07)): ?>
<?php $attributes = $__attributesOriginal977aa12910ce3eacce79e3d8532e1e07; ?>
<?php unset($__attributesOriginal977aa12910ce3eacce79e3d8532e1e07); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal977aa12910ce3eacce79e3d8532e1e07)): ?>
<?php $component = $__componentOriginal977aa12910ce3eacce79e3d8532e1e07; ?>
<?php unset($__componentOriginal977aa12910ce3eacce79e3d8532e1e07); ?>
<?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                
                                <tr>
                                    <td colspan="7" class="text-center py-4 border border-gray-100">
                                        Tidak ada data pengguna.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between mt-4">
                    
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

                    
                    <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/pengguna.blade.php ENDPATH**/ ?>