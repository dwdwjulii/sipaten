<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
'type' => 'info', // error | success | warning
'title' => '',
'messages' => [], // array of ['text' => '', 'status' => 'success'|'error']
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
'type' => 'info', // error | success | warning
'title' => '',
'messages' => [], // array of ['text' => '', 'status' => 'success'|'error']
]); ?>
<?php foreach (array_filter(([
'type' => 'info', // error | success | warning
'title' => '',
'messages' => [], // array of ['text' => '', 'status' => 'success'|'error']
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
$styles = [
'error' => [
'bg' => 'bg-red-100',
'text' => 'text-red-900',
'iconBg' => 'bg-red-500',
'icon' => asset('asset/images/error.png'),
'close' => 'text-red-600 hover:text-red-700',
],
'success' => [
'bg' => 'bg-emerald-100',
'text' => 'text-green-900',
'iconBg' => 'bg-green-500',
'icon' => asset('asset/images/success.png'),
'close' => 'text-green-600 hover:text-green-700',
],
'warning' => [
'bg' => 'bg-orange-100',
'text' => 'text-orange-800',
'iconBg' => 'bg-orange-500',
'icon' => asset('asset/images/warning.png'),
'close' => 'text-orange-600 hover:text-orange-700',
],
];
$style = $styles[$type];
?>

<div x-data="{ open: true }" x-show="open" x-transition
    class="<?php echo e($style['bg']); ?> rounded-sm flex overflow-hidden relative">

    
    <div class="w-12 <?php echo e($style['iconBg']); ?> flex items-center justify-center">
        <img src="<?php echo e($style['icon']); ?>" alt="icon" class="w-6 h-6 object-contain">
    </div>

    
    <div class="flex-1 px-3 py-3 flex items-start">
        <div class="flex-1 flex flex-col justify-center">
            <p class="text-sm <?php echo e($style['text']); ?>">
                <span class="font-semibold"><?php echo e($title); ?></span>
                <?php if(is_array($messages)): ?>
                <?php echo e(implode(' ', array_map(fn($msg) => is_array($msg) ? $msg['text'] : $msg, $messages))); ?>

                <?php else: ?>
                <?php echo e($messages); ?>

                <?php endif; ?>
            </p>
        </div>
    </div>


    
    <button @click="open = false" class="absolute top-2 right-2 <?php echo e($style['close']); ?> focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd"
                d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                clip-rule="evenodd" />
        </svg>
    </button>
</div><?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/components/alert-status.blade.php ENDPATH**/ ?>