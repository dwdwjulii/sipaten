<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['pencatatan']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['pencatatan']); ?>
<?php foreach (array_filter((['pencatatan']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div x-data="{ open: false }" class="inline-block">
    
    <button 
        @click="open = true" 
        type="button" 
        class="text-gray-400 hover:text-blue-600 transition-colors"
        title="Lihat Detail">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
            <circle cx="12" cy="12" r="3" />
        </svg>
    </button>

    
    <div 
        x-show="open" 
        x-transition.opacity.duration.300ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4"
        style="display: none;"
    >
        
        <div 
            @click.away="open = false"
            x-show="open"
            x-transition
            class="relative w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden"
            style="display: none;"
        >
            
            <div class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Detail Catatan Ternak</h3>
                    <p class="text-sm text-gray-600 mt-1">Informasi lengkap pencatatan ternak</p>
                </div>
                <button 
                    @click="open = false" 
                    class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-white/50 rounded-full transition-all"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            
            <div class="max-h-[90vh] overflow-y-auto scrollbar-hide px-8 py-6 space-y-8">
                
                
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs font-medium text-blue-600 uppercase mb-1">Nama Anggota</p>
                            <p class="text-gray-900 font-semibold text-lg"><?php echo e($pencatatan->anggota->nama); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-blue-600 uppercase mb-1">Tahap Program</p>
                            <p class="text-gray-900 font-semibold text-lg"><?php echo e($pencatatan->anggota->tahap->label); ?></p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-blue-600 uppercase mb-1">Tanggal Catatan</p>
                            <p class="text-gray-900 font-semibold text-lg"><?php echo e($pencatatan->tanggal_catatan->format('d F Y')); ?></p>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white border rounded-xl p-6">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 ml-4 mr-16">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">Temuan di Lapangan</h4>
                           <p class="text-gray-700 leading-relaxed break-all"><?php echo e($pencatatan->temuan_lapangan ?? 'Tidak ada catatan temuan.'); ?></p>
                        </div>
                    </div>
                </div>

                
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="w-10 h-10 ml-2 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 ml-4 mr-20">
                        <p class="text-xs text-gray-500 font-medium">Dicatat oleh</p>
                        <p class="text-gray-900 font-semibold"><?php echo e($pencatatan->petugas?->name ?? 'Belum ada petugas'); ?>

                        </p>
                    </div>
                </div>

                
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Data Ternak yang Dicatat</h4>
                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Ear Tag</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kelamin</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Umur</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Kondisi</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Vaksin</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                        // Ambil semua detail
                                        $allDetails = $pencatatan->details;
                                        
                                        // 1. Grup 1: Semua INDUK dengan anak-anaknya
                                        $indukDetails = $allDetails->filter(function($detail) {
                                            return $detail->ternak && $detail->ternak->tipe_ternak === 'Induk';
                                        });
                                        
                                        $indukTernakIds = $indukDetails->pluck('ternak_id');
                                        
                                        // 2. Grup 2: ANAK YATIM (anak yang induknya tidak ada di pencatatan ini)
                                        $anakYatimDetails = $allDetails->filter(function($detail) use ($indukTernakIds) {
                                            return $detail->ternak 
                                                && $detail->ternak->tipe_ternak === 'Anak' 
                                                && !$indukTernakIds->contains($detail->ternak->induk_id);
                                        });
                                    ?>

                                    
                                    <?php $__empty_1 = true; $__currentLoopData = $indukDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $indukDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="bg-gray-50 font-semibold">
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                <?php echo e($indukDetail->ternak->tipe_ternak); ?> #<?php echo e($loop->iteration); ?>

                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($indukDetail->ternak->no_ear_tag ?? '-'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($indukDetail->ternak->jenis_kelamin ?? '-'); ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($indukDetail->umur_saat_dicatat); ?></td>
                                            <td class="px-4 py-3">
                                                  <?php
                                                $colorClass = match ($indukDetail->kondisi_ternak) {
                                                'Sehat' => 'bg-green-100 text-green-800',
                                                'Sakit' => 'bg-yellow-100 text-yellow-800',
                                                'Mati' => 'bg-red-100 text-red-800',
                                                'Terjual' => 'bg-blue-100 text-blue-800',
                                                default => 'bg-gray-100 text-gray-800',
                                                };
                                                ?>
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($colorClass); ?>">
                                                    <?php echo e($indukDetail->kondisi_ternak); ?>

                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <?php
                                                    $vaksinColorClass = match ($indukDetail->status_vaksin) {
                                                    'Sudah' => 'bg-blue-100 text-blue-800',
                                                    'Belum' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                    };
                                                ?>
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($vaksinColorClass); ?>">
                                                    <?php echo e($indukDetail->status_vaksin); ?>

                                                </span>
                                            </td>
                                        </tr>

                                        
                                        <?php $__currentLoopData = $allDetails->filter(function($detail) use ($indukDetail) {
                                            return $detail->ternak && $detail->ternak->induk_id === $indukDetail->ternak_id;
                                        }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anakDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-4 py-3 text-sm text-gray-900 pl-8">
                                                    <span class="text-gray-400 mr-1">↳</span> <?php echo e($anakDetail->ternak->tipe_ternak); ?>

                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($anakDetail->ternak->no_ear_tag ?? '-'); ?></td>
                                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($anakDetail->ternak->jenis_kelamin ?? '-'); ?></td>
                                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($anakDetail->umur_saat_dicatat); ?></td>
                                                <td class="px-4 py-3">
                                                     <?php
                                                        $colorClass = match ($anakDetail->kondisi_ternak) {
                                                        'Sehat' => 'bg-green-100 text-green-800',
                                                        'Sakit' => 'bg-yellow-100 text-yellow-800',
                                                        'Mati' => 'bg-red-100 text-red-800',
                                                        'Terjual' => 'bg-blue-100 text-blue-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                        };
                                                    ?>
                                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($colorClass); ?>">
                                                        <?php echo e($anakDetail->kondisi_ternak); ?>

                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                     <?php
                                                        $vaksinColorClass = match ($anakDetail->status_vaksin) {
                                                        'Sudah' => 'bg-blue-100 text-blue-800',
                                                        'Belum' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                        };
                                                    ?>
                                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($vaksinColorClass); ?>">
                                                        <?php echo e($anakDetail->status_vaksin); ?>

                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <?php endif; ?>

                                    
                                    <?php if($anakYatimDetails->isNotEmpty()): ?>
                                        
                                        <?php $__currentLoopData = $anakYatimDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yatimDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-amber-50 transition-colors">
                                                <td class="px-4 py-3 text-sm text-gray-900 pl-8">
                                                    <span class="text-amber-400 mr-1">◆</span> <?php echo e($yatimDetail->ternak->tipe_ternak); ?>

                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($yatimDetail->ternak->no_ear_tag ?? '-'); ?></td>
                                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($yatimDetail->ternak->jenis_kelamin ?? '-'); ?></td>
                                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($yatimDetail->umur_saat_dicatat); ?></td>
                                                <td class="px-4 py-3">
                                                     <?php
                                                        $colorClass = match ($yatimDetail->kondisi_ternak) {
                                                        'Sehat' => 'bg-green-100 text-green-800',
                                                        'Sakit' => 'bg-yellow-100 text-yellow-800',
                                                        'Mati' => 'bg-red-100 text-red-800',
                                                        'Terjual' => 'bg-blue-100 text-blue-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                        };
                                                    ?>
                                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($colorClass); ?>">
                                                        <?php echo e($yatimDetail->kondisi_ternak); ?>

                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <?php
                                                        $vaksinColorClass = match ($yatimDetail->status_vaksin) {
                                                        'Sudah' => 'bg-blue-100 text-blue-800',
                                                        'Belum' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                        };
                                                    ?>
                                                <span
                                                    class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($vaksinColorClass); ?>">
                                                    <?php echo e($yatimDetail->status_vaksin); ?>

                                                </span>
                                        </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>

                                    
                                    <?php if($allDetails->isEmpty()): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-8 text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                                <p class="font-medium">Tidak ada detail ternak</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-4">Foto Dokumentasi</h4>
                    <?php if(!empty($pencatatan->foto_dokumentasi)): ?>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php $__currentLoopData = $pencatatan->foto_dokumentasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $foto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(Storage::url($foto)); ?>" target="_blank"
                                   class="group relative aspect-square overflow-hidden rounded-xl border-2 border-gray-200 hover:border-blue-400 transition-all">
                                    <img src="<?php echo e(Storage::url($foto)); ?>" 
                                         alt="Dokumentasi" 
                                         class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 flex items-center justify-center transition-opacity">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" 
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-500 font-medium">Tidak ada foto dokumentasi</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div> 
        </div>
    </div>
</div><?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/components/pencatatan/detail-modal.blade.php ENDPATH**/ ?>