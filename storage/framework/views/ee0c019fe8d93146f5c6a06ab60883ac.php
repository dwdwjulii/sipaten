
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['action' => '#']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['action' => '#']); ?>
<?php foreach (array_filter((['action' => '#']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<form action="<?php echo e($action); ?>" method="GET">
    <div class="relative">
        <input type="text" name="search" placeholder="Search"
            class="w-full pl-4 pr-8 py-1.5 text-sm border border-gray-200 placeholder:text-gray-800 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0"
            value="<?php echo e(request('search')); ?>">
        <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3">
            
            <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>
</form><?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/components/search-bar.blade.php ENDPATH**/ ?>