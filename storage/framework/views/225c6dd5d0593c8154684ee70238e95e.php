<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['tahapId']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['tahapId']); ?>
<?php foreach (array_filter((['tahapId']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php if (isset($component)) { $__componentOriginal7ced20d759b20fae2fc05b14a946da2a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ced20d759b20fae2fc05b14a946da2a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal-base','data' => ['title' => 'Tambah Anggota','maxWidth' => 'md','scrollable' => true,'showTitle' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal-base'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tambah Anggota','maxWidth' => 'md','scrollable' => true,'showTitle' => true]); ?>
     <?php $__env->slot('trigger', null, []); ?> 
        <button
             class="inline-flex items-center px-2 py-1.5 text-sm font-medium text-white bg-green-700 border border-transparent rounded-lg hover:bg-green-800">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1">
                <path
                    d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Tambah Anggota
        </button>
     <?php $__env->endSlot(); ?>

    <div class="sticky top-0 z-10 pb-2 bg-white border-b border-white">
        <p class="mb-1 text-xs text-gray-500">
            Isi data anggota dengan lengkap untuk menambahkan data anggota
        </p>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="px-4 py-3 mb-3 text-red-700 bg-red-100 border border-red-400 rounded-md">
            <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
            <ul class="mt-2 text-sm list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form method="POST" action="<?php echo e(route('anggota.store')); ?>" class="space-y-2">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="tahap_id" value="<?php echo e($tahapId); ?>">

        
        <div>
            <label class="block text-xs font-medium text-gray-800">Nama Anggota</label>
            <input type="text" name="nama" value="<?php echo e(old('nama')); ?>" required
                class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
        </div>

        
        <div x-data="{ open: false, jenis: '<?php echo e(old('jenis_ternak') ? old('jenis_ternak') : ''); ?>' }" class="relative">
            <label class="block text-xs font-medium text-gray-800">Jenis Ternak</label>

            <!-- input tersembunyi untuk dikirim ke server -->
            <input type="hidden" name="jenis_ternak" x-model="jenis">

            <!-- tombol dropdown -->
            <button type="button" @click="open = !open"
                class="w-full px-2 py-2 mt-1 text-xs text-left text-gray-800 capitalize bg-white border border-gray-200 rounded-md">
                <span x-text="jenis ? jenis : 'Pilih jenis ternak'" 
                    :class="jenis ? 'text-gray-800' : 'text-gray-400'"></span>

                <svg class="float-right w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                    :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- daftar pilihan -->
            <div x-show="open" @click.away="open=false"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-md">
                <ul class="py-1 text-xs text-gray-700">
                    <li>
                        <a href="#" @click.prevent="jenis='sapi'; open=false"
                            class="block px-3 py-1.5 hover:bg-green-100">Sapi</a>
                    </li>
                    <li>
                        <a href="#" @click.prevent="jenis='kambing'; open=false"
                            class="block px-3 py-1.5 hover:bg-green-100">Kambing</a>
                    </li>
                </ul>
            </div>
        </div>


        
        <div 
            x-data="{
                jumlahInduk: <?php echo e(old('jumlah_induk', 1)); ?>,
                // Inisialisasi hargaInduk sebagai array kosong
                hargaInduk: [],
                
                // Fungsi init akan dijalankan pertama kali oleh Alpine
                init() {
                    // Ambil data 'old' dari PHP dan ubah menjadi array JavaScript
                    const oldTernak = <?php echo json_encode(old('ternak_awal', []), 512) ?>;
                    // Atur panjang array hargaInduk agar sesuai dengan jumlahInduk
                    this.updateHargaArray();
                    // Isi array hargaInduk dengan nilai 'old' jika ada
                    oldTernak.forEach((ternak, index) => {
                        if (this.hargaInduk[index] !== undefined) {
                            this.hargaInduk[index] = ternak.harga || 0;
                        }
                    });
                },
                
                // Fungsi untuk menyesuaikan panjang array harga
                updateHargaArray() {
                    const currentLength = this.hargaInduk.length;
                    if (currentLength < this.jumlahInduk) {
                        // Tambahkan elemen baru jika jumlahInduk bertambah
                        for (let i = currentLength; i < this.jumlahInduk; i++) {
                            this.hargaInduk.push(0);
                        }
                    } else if (currentLength > this.jumlahInduk) {
                        // Potong array jika jumlahInduk berkurang
                        this.hargaInduk.splice(this.jumlahInduk);
                    }
                },

                formatRupiah(number) {
                    if (number === null || number === '' || isNaN(number) || number === 0) return '';
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
                },

                updateHarga(event, index) {
                    let rawValue = event.target.value;
                    let cleanedValue = rawValue.replace(/\D/g, '');
                    this.hargaInduk[index] = cleanedValue ? parseInt(cleanedValue) : 0;
                    
                    this.$nextTick(() => {
                        event.target.value = this.formatRupiah(this.hargaInduk[index]);
                    });
                }
            }" 
            class="grid grid-cols-1 md:grid-cols-2 gap-3"
            x-init="init()"
        >
            
            <div>
                <label class="block text-xs font-medium text-gray-800">Jumlah Induk</label>
                    
                    <div class="relative w-full mt-1"> 
                        
                        <input type="text" name="jumlah_induk" x-model.number="jumlahInduk" readonly
                            class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />

                        <div class="absolute inset-y-0 right-0 flex items-center gap-0.5 pr-1.5">
                            
                            <!-- Tombol Minus -->
                            <button type="button" 
                                    @click="if (jumlahInduk > 0) jumlahInduk--"
                                    :disabled="jumlahInduk <= 0"
                                    class="flex items-center justify-center w-4 h-4 mr-1 text-gray-600 bg-white border border-gray-300 rounded-full hover:bg-gray-100 active:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                </svg>
                            </button>
                            
                            <!-- Tombol Plus -->
                            <button type="button" 
                                    @click="jumlahInduk++"
                                    class="flex items-center justify-center w-4 h-4 text-white bg-blue-600 border border-blue-600 rounded-full hover:bg-blue-700 active:bg-blue-800 transition-colors">
                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                            
                        </div>
                    </div>
                </div> 

            
           
            <template x-for="index in jumlahInduk" :key="index">
                <div>
                    <label 
                        class="block text-xs font-medium text-gray-800" 
                        x-text="`Harga Induk Awal ${index}`"
                    ></label>

                    
                    <input 
                        type="text" 
                        :value="formatRupiah(hargaInduk[index - 1])"
                        @input="updateHarga($event, index - 1)"
                        placeholder="Rp 0"
                        class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500"
                    >

                    
                    <input 
                        type="hidden" 
                        :name="`ternak_awal[${index - 1}][harga]`" 
                        :value="hargaInduk[index - 1] || 0"
                    >
                </div>
            </template>
            
        </div>

        
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-800">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="<?php echo e(old('tempat_lahir')); ?>" required
                    class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-800">Tanggal Lahir</label>
                <input id="tanggal_lahir" name="tanggal_lahir" type="text" value="<?php echo e(old('tanggal_lahir')); ?>"
                    datepicker datepicker-format="dd/mm/yyyy" required
                    class="block w-full mt-1 text-xs text-gray-800 bg-white border-gray-200 rounded-md focus:ring-0 focus:border-green-500"
                    placeholder="DD/MM/YYYY">
            </div>
        </div>

        
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-800">No HP</label>
                <input type="tel" name="no_hp" value="<?php echo e(old('no_hp')); ?>" required
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,15)"
                    class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
            </div>
            <div x-data="{ open: false, status: '<?php echo e(old('status', 'aktif')); ?>' }" class="relative">
                <label class="block text-xs font-medium text-gray-800">Status</label>
                <input type="hidden" name="status" x-model="status">
                <button type="button" @click="open = !open"
                    class="w-full px-2 py-2 mt-1 text-xs text-left text-gray-800 bg-white border border-gray-200 rounded-md">
                    <span x-text="status.replace('-', ' ')" class="capitalize"></span>
                    <svg class="float-right w-4 h-4 text-gray-500 transition-transform duration-200 transform"
                        :class="open ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open=false" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-md">
                    <ul class="py-1 text-xs text-gray-700">
                        <li><a href="#" @click.prevent="status='aktif'; open=false"
                                class="block px-3 py-1.5 hover:bg-green-100">Aktif</a></li>
                        <li><a href="#" @click.prevent="status='non-aktif'; open=false"
                                class="block px-3 py-1.5 hover:bg-green-100">Non-Aktif</a></li>
                    </ul>
                </div>
            </div>
        </div>

        
        <div>
            <label class="block text-xs font-medium text-gray-800">Lokasi Kandang</label>
            <input type="text" name="lokasi_kandang" value="<?php echo e(old('lokasi_kandang')); ?>" required
                class="block w-full mt-1 text-xs text-gray-800 border-gray-200 rounded-md focus:ring-0 focus:border-green-500" />
        </div>

        
       <div class="flex w-full gap-2 pt-2 sticky bottom-0 bg-white z-20">
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


<?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/components/anggota/create-modal.blade.php ENDPATH**/ ?>