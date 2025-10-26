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
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Arsip Laporan Tahunan</h2>
                <p class="text-sm text-gray-500">Pilih tahun untuk melihat rekap arsip bulanan.</p>
            </div>

            
            <?php if(session('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>

            <?php if($arsipsPerTahun->isNotEmpty()): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                    <?php $__currentLoopData = $arsipsPerTahun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $arsip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            // Menentukan status, default 'progress' jika null
                            $status = $arsip->status ?? 'progress';
                            $bgColor = $status == 'selesai' ? 'bg-green-700' : 'bg-yellow-600';
                            $borderColor = $status == 'selesai' ? 'hover:border-green-500' : 'hover:border-yellow-500';
                            $textColor = $status == 'selesai' ? 'text-green-600' : 'text-yellow-600';
                        ?>
                        <a href="<?php echo e(route('arsip.tahun', $arsip->tahun)); ?>"
                           class="relative flex flex-col items-center justify-between bg-white p-6 border border-gray-200 rounded-xl shadow-sm hover:shadow-lg <?php echo e($borderColor); ?> transition min-h-[220px]">
                            
                            
                            <span class="absolute top-3 right-3 text-xs font-bold text-white px-2 py-1 rounded-full <?php echo e($bgColor); ?>">
                                <?php echo e(ucfirst($status)); ?>

                            </span>

                            <span class="absolute top-3 left-3 text-sm font-semibold text-gray-600">
                                Tahun <?php echo e($arsip->tahun); ?>

                            </span>

                            <div class="flex flex-col items-center gap-1 mt-6 <?php echo e($textColor); ?>">
                                <svg class="w-20 h-20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M4 20q-.825 0-1.412-.587T2 18V6q0-.825.588-1.412T4 4h6l2 2h8q.825 0 1.413.588T22 8v10q0 .825-.587 1.413T20 20z"/></svg>
                            </div>
                            
                            <p class="w-full text-center px-2 py-1.5 text-sm font-medium text-white <?php echo e($bgColor); ?> border border-transparent rounded-md">
                                <?php echo e($arsip->jumlah); ?> Arsip
                            </p>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 text-gray-500">
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Arsip</h3>
                    <p class="mt-1 text-sm text-gray-500">Saat ini belum ada laporan yang diarsipkan.</p>
                </div>
            <?php endif; ?>
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
<?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/arsip.blade.php ENDPATH**/ ?>