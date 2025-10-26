

<?php if (isset($component)) { $__componentOriginal7ced20d759b20fae2fc05b14a946da2a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ced20d759b20fae2fc05b14a946da2a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-base','data' => ['title' => 'Tambah Pengguna Baru','maxWidth' => 'md','scrollable' => false,'showTitle' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal-base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tambah Pengguna Baru','maxWidth' => 'md','scrollable' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'showTitle' => true]); ?>

    
     <?php $__env->slot('trigger', null, []); ?> 
         <button type="button" @click="$refs.formCreatePengguna.reset(); open = true"
            class="inline-flex items-center rounded-lg bg-green-700 px-2 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-green-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14" />
                <path d="M12 5v14" />
            </svg>
            Tambah Pengguna
        </button>
     <?php $__env->endSlot(); ?>

    
    <p class="text-xs text-gray-500 mb-2">
        Buat akun baru untuk admin atau petugas.
    </p>

    
    <?php if($errors->any()): ?>
    <div class="mb-3 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md relative" role="alert">
        <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    
    <form method="POST" action="<?php echo e(route('pengguna.store')); ?>" class="space-y-3" x-ref="formCreatePengguna">
        
        <?php echo csrf_field(); ?>

        
        <div>
            <label class="block text-xs font-medium text-gray-800">Nama Pengguna</label>
            <input type="text" name="name"
                class="mt-1 block w-full rounded-md border-gray-200 text-gray-800   placeholder-gray-800 focus:ring-0 focus:border-green-500 text-xs"
                placeholder="Masukkan nama lengkap" required>
        </div>

        
        <div class="grid grid-cols-4 gap-3">
            <div class="col-span-3">
                <label class="block text-xs font-medium text-gray-800">Email (Username)</label>
                <input type="email" name="email"
                    class="mt-1 block w-full rounded-md text-gray-800placeholder-gray-800 border-gray-200 focus:ring-0 focus:border-green-500 text-xs"
                    placeholder="contoh@email.com" required>
            </div>

            
            <div x-data="{ open: false, selected: 'petugas' }" class="relative col-span-1">
                <label class="block text-xs font-medium text-gray-800">Role</label>
                <input type="hidden" name="role" x-model="selected">
                <button type="button" @click="open = !open"
                    class="mt-1 w-full px-2 py-2 border rounded-md text-left text-gray-800 bg-white border-gray-200 text-xs capitalize">
                    <span x-text="selected"></span>
                    <svg class="w-4 h-4 float-right text-gray-500 transform transition-transform duration-200"
                        :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open=false"
                    class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-md">
                    <ul class="py-1 text-xs text-gray-700">
                        <li><a href="#" @click.prevent="selected='admin'; open=false"
                                class="block px-3 py-1.5 hover:bg-green-100">Admin</a></li>
                        <li><a href="#" @click.prevent="selected='petugas'; open=false"
                                class="block px-3 py-1.5 hover:bg-green-100">Petugas</a></li>
                    </ul>
                </div>
            </div>
        </div>

        
        <div class="grid grid-cols-4 gap-3">
            
            <div class="col-span-3 relative" x-data="{ show: false }">
                <label class="block text-xs font-medium text-gray-800">Password</label>
                <input :type="show ? 'text' : 'password'" name="password"
                    class="mt-1 block w-full rounded-md text-gray-800placeholder-gray-800 border-gray-200 focus:ring-0 focus:border-green-500 text-xs"
                    placeholder="••••••••" required>
                <span class="absolute inset-y-0 right-3 flex items-center cursor-pointer mt-5" @click="show = !show">
                    
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                        class="w-4 h-4 text-gray-800">
                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        <path fill-rule="evenodd"
                            d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                            clip-rule="evenodd" />
                    </svg>
                    
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                        class="w-4 h-4 text-gray-800">
                        <path
                            d="M3.53 2.47a.75.75 0 0 0-1.06 1.06l18 18a.75.75 0 1 0 1.06-1.06l-18-18ZM22.676 12.553a11.249 11.249 0 0 1-2.631 4.31l-3.099-3.099a5.25 5.25 0 0 0-6.71-6.71L7.759 4.577a11.217 11.217 0 0 1 4.242-.827c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113Z" />
                        <path
                            d="M15.75 12c0 .18-.013.357-.037.53l-4.244-4.243A3.75 3.75 0 0 1 15.75 12ZM12.53 15.713l-4.243-4.244a3.75 3.75 0 0 0 4.244 4.243Z" />
                        <path
                            d="M6.75 12c0-.619.107-1.213.304-1.764l-3.1-3.1a11.25 11.25 0 0 0-2.63 4.31c-.12.362-.12.752 0 1.114 1.489 4.467 5.704 7.69 10.675 7.69 1.5 0 2.933-.294 4.242-.827l-2.477-2.477A5.25 5.25 0 0 1 6.75 12Z" />
                    </svg>
                </span>
            </div>

            
            <div x-data="{ open: false, selected: 'aktif' }" class="relative col-span-1">
                <label class="block text-xs font-medium text-gray-800">Status</label>
                <input type="hidden" name="status" x-model="selected">
                <button type="button" @click="open = !open"
                    class="mt-1 w-full px-2 py-2 border rounded-md text-gray-800 text-left bg-white border-gray-200 text-xs capitalize">
                    <span x-text="selected"></span>
                    <svg class="w-4 h-4 float-right text-gray-500 transform transition-transform duration-200"
                        :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open=false"
                    class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-md">
                    <ul class="py-1 text-xs text-gray-700">
                        <li><a href="#" @click.prevent="selected='aktif'; open=false"
                                class="block px-3 py-1.5 hover:bg-green-100">Aktif</a></li>
                        <li><a href="#" @click.prevent="selected='non-aktif'; open=false"
                                class="block px-3 py-1.5 hover:bg-green-100">Non-Aktif</a></li>
                    </ul>
                </div>
            </div>
        </div>

        
        <div class="flex gap-2 w-full pt-3">
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


<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if(session('success')): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: "<?php echo e(session('success')); ?>",
    showConfirmButton: false,
    timer: 2000
});
</script>
<?php endif; ?>

<?php if(session('error')): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: "<?php echo e(session('error')); ?>",
});
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/components/pengguna/create-modal.blade.php ENDPATH**/ ?>