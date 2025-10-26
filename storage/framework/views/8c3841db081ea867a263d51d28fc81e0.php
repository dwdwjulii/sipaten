<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['href']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['href']); ?>
<?php foreach (array_filter((['href']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
$user = Auth::user();
$isDisabledProfile = $user && $user->role === 'petugas' && $href === route('profile.edit');
?>

<a <?php echo e($attributes->merge([
        'class' => 'flex items-center gap-2 w-full px-4 py-2 text-start text-sm leading-5 transition duration-150 ease-in-out ' .
                    ($isDisabledProfile
                        ? 'text-gray-700 cursor-not-allowed'
                        : 'text-gray-700 dark:text-gray-300 hover:bg-green-100 dark:hover:bg-green-800 focus:outline-none focus:bg-green-100 dark:focus:bg-green-800')
    ])); ?> href="<?php echo e($isDisabledProfile ? '#' : $href); ?>" <?php if($isDisabledProfile): ?> onclick="event.preventDefault();"
    <?php endif; ?>>
    
    <?php if(isset($icon)): ?>
    <span class="w-5 h-5 flex items-center justify-center">
        <?php echo e($icon); ?>

    </span>
    <?php endif; ?>

    
    <span><?php echo e($slot); ?></span>
</a><?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/components/dropdown-link.blade.php ENDPATH**/ ?>