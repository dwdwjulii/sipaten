<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['user']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['user']); ?>
<?php foreach (array_filter((['user']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php if (isset($component)) { $__componentOriginal7ced20d759b20fae2fc05b14a946da2a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ced20d759b20fae2fc05b14a946da2a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-base','data' => ['maxWidth' => 'sm','scrollable' => false,'showTitle' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal-base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['maxWidth' => 'sm','scrollable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'showTitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    
     <?php $__env->slot('trigger', null, []); ?> 
        <button type="button" class="text-red-600 hover:text-red-900" title="Hapus">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                <path d="M3 6h18" />
                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
            </svg>
        </button>
     <?php $__env->endSlot(); ?>

    
    <p class="text-gray-700 text-sm whitespace-normal break-words">
        Apakah Anda yakin ingin menghapus data 
        <span class="font-medium text-gray-900"><?php echo e($user->name); ?></span>?
    </p>

    
     <?php $__env->slot('footer', null, []); ?> 
        <form action="<?php echo e(route('pengguna.destroy', $user->id)); ?>" method="POST" class="flex gap-2 w-full">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>

            
            <button type="button" @click="open = false"
                class="flex-1 px-3 py-1.5 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-100">
                Batal
            </button>

            
            <button type="submit"
                class="flex-1 px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                Ya, Hapus
            </button>
        </form>
     <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ced20d759b20fae2fc05b14a946da2a)): ?>
<?php $attributes = $__attributesOriginal7ced20d759b20fae2fc05b14a946da2a; ?>
<?php unset($__attributesOriginal7ced20d759b20fae2fc05b14a946da2a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ced20d759b20fae2fc05b14a946da2a)): ?>
<?php $component = $__componentOriginal7ced20d759b20fae2fc05b14a946da2a; ?>
<?php unset($__componentOriginal7ced20d759b20fae2fc05b14a946da2a); ?>
<?php endif; ?>


<?php if(session('success')): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "<?php echo e(session('success')); ?>",
        confirmButtonColor: '#2563eb'
    })
</script>
<?php endif; ?>
<?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/components/pengguna/delete-modal.blade.php ENDPATH**/ ?>