
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['id' => 'tahap-modal']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['id' => 'tahap-modal']); ?>
<?php foreach (array_filter((['id' => 'tahap-modal']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div x-data="{ open: false }">
    
    <button @click="open = true" type="button"
        class="w-full inline-flex items-center px-3 py-2 text-green-600 hover:bg-green-100 hover:rounded-b-md rounded-b-lg font-medium text-xs">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 mr-1">
            <path
                d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
        </svg>
        Tambah Tahap
    </button>

    
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

        
        <div @click.away="open = false" class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">

            <h2 class="text-lg font-medium text-gray-900">Tambah Tahap</h2>

            <div class="sticky z-10 mb-4">
                <p class="mb-1 text-xs text-gray-500">
                    Lengkapi informasi tahap serta tahun keanggotaan
                </p>
            </div>

            
            <form method="POST" action="<?php echo e(route('tahap.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-800">Tahap</label>
                    <input type="number" name="tahap_ke" min="1"
                        class="w-full mt-1 border-gray-200 rounded-md text-gray-800 text-xs focus:border-green-500 focus:ring-0"
                        placeholder="" required>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-800">Tahun</label>
                    <input type="number" name="tahun" min="2000"
                        class="w-full mt-1 border-gray-200 rounded-md text-gray-800 text-xs focus:border-green-500 focus:ring-0"
                        placeholder="" required>
                </div>

                <div class="flex w-full gap-2 pt-2">
                    <button type="button" @click="open = false"
                        class="flex-1 px-3 py-1.5 text-sm font-medium text-white bg-red-700 rounded-md hover:bg-red-800">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-3 py-1.5 text-sm font-medium text-white bg-blue-700 rounded-md hover:bg-blue-800">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/components/anggota/tahap-modal.blade.php ENDPATH**/ ?>