

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
'title' => 'Modal Title',
'show' => false,
'maxWidth' => 'md', // default ukuran
'scrollable' => true, // default: modal bisa scroll
'showTitle' => true,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
'title' => 'Modal Title',
'show' => false,
'maxWidth' => 'md', // default ukuran
'scrollable' => true, // default: modal bisa scroll
'showTitle' => true,
]); ?>
<?php foreach (array_filter(([
'title' => 'Modal Title',
'show' => false,
'maxWidth' => 'md', // default ukuran
'scrollable' => true, // default: modal bisa scroll
'showTitle' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
$maxWidthClass = [
'sm' => 'sm:max-w-sm',
'md' => 'sm:max-w-md',
'lg' => 'sm:max-w-lg',
'xl' => 'sm:max-w-xl',
'2xl' => 'sm:max-w-2xl',
][$maxWidth] ?? 'sm:max-w-md';

// bodyClass otomatis menyesuaikan
$bodyClass = $scrollable
? 'flex-1 min-h-[56vh] max-h-[56vh] overflow-y-auto scrollbar-hide pr-1'
: 'flex-1';
?>

<div x-data="{ 
        open: <?php echo \Illuminate\Support\Js::from($show)->toHtml() ?>, 
        lockBodyScroll() { document.body.style.overflow = 'hidden' }, 
        unlockBodyScroll() { document.body.style.overflow = '' } 
    }" x-init="$watch('open', val => val ? lockBodyScroll() : unlockBodyScroll())" x-cloak>

    
    <?php if(isset($trigger)): ?>
    <div @click="open = true">
        <?php echo e($trigger); ?>

    </div>
    <?php endif; ?>

    
    <div x-show="open" x-transition.opacity.duration.200ms
        class="fixed inset-0 z-50 flex items-start justify-center bg-black bg-opacity-40">

        
        <div @click.outside="
                if (!$event.target.closest('.datepicker-dropdown')) {
                    open = false
                }
            "
            class="bg-white rounded-xl shadow-2xl w-full <?php echo e($maxWidthClass); ?> p-5 mt-10 transform transition-transform"
            x-show="open" x-transition:enter="transform -translate-y-10 opacity-0"
            x-transition:enter-end="transform translate-y-0 opacity-100"
            x-transition:leave="transform translate-y-0 opacity-100"
            x-transition:leave-end="transform -translate-y-10 opacity-0" x-transition.duration.300ms.ease-out>

            
            <?php if($showTitle && $title): ?>
            <div class="mb-1">
                <h2 class="text-lg font-semibold text-gray-900"><?php echo e($title); ?></h2>
                <?php echo e($header ?? ''); ?>

            </div>
            <?php endif; ?>

            
            <div class="space-y-4 relative">
                <div class="<?php echo e($bodyClass); ?>">
                    <?php echo e($slot); ?>

                </div>
            </div>

            
            <div class="flex justify-end gap-2 pt-4">
                <?php echo e($footer ?? ''); ?>

            </div>
        </div>
    </div>
</div><?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/components/modal-base.blade.php ENDPATH**/ ?>