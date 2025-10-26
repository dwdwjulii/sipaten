<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pagePadding' => 'sm:px-24']); ?>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">

        
        <a href="<?php echo e(route('pengguna.index')); ?>" class="flex items-center p-3 bg-white rounded-md shadow-md border border-gray-100 
           hover:shadow-lg transition-shadow duration-300 ease-in-out">
            <div class="p-2 mr-4 mx-2 text-white bg-green-700 rounded-md ">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-circle-user-round-icon lucide-circle-user-round">
                    <path d="M18 20a6 6 0 0 0-12 0" />
                    <circle cx="12" cy="10" r="4" />
                    <circle cx="12" cy="12" r="10" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-green-700"><?php echo e($totalPengguna); ?></p>
                <p class="text-sm font-medium text-gray-500">Kelola Pengguna</p>
            </div>
        </a>

        
        <a href="<?php echo e(route('anggota.index')); ?>" class="flex items-center p-3 bg-white rounded-md shadow-md border border-gray-100 
           hover:shadow-lg transition-shadow duration-300 ease-in-out">
            <div class="p-2 mr-4 mx-2 text-white bg-green-700 rounded-md ">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-layers-icon lucide-layers">
                    <path
                        d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z" />
                    <path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12" />
                    <path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17" />
                </svg>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-green-700"><?php echo e($totalAnggota); ?></p>
                <p class="text-sm font-medium text-gray-500">Kelola Anggota</p>
            </div>
        </a>

        
        <a href="<?php echo e(route('pencatatan.index')); ?>" class="flex items-center p-3 bg-white rounded-md shadow-md border border-gray-100 
           hover:shadow-lg transition-shadow duration-300 ease-in-out">
            <div class="p-2 mr-4 mx-2 text-white bg-green-700 rounded-md ">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-file-text-icon lucide-file-text">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
                    <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                    <path d="M10 9H8" />
                    <path d="M16 13H8" />
                    <path d="M16 17H8" />
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold text-gray-500">Kelola Catatan</p>
            </div>
        </a>

        
        <a href="<?php echo e(route('arsip.index')); ?>" class="flex items-center p-3 bg-white rounded-md shadow-md border border-gray-100 
           hover:shadow-lg transition-shadow duration-300 ease-in-out">
            <div class="p-2 mr-4 mx-2 text-white bg-green-700 rounded-md ">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-archive-icon lucide-archive">
                    <rect width="20" height="5" x="2" y="3" rx="1" />
                    <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                    <path d="M10 12h4" />
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold text-gray-500">
                    Arsip Laporan
                </p>
            </div>
        </a>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        
        <div class="lg:col-span-2 space-y-4">

            
            <div class="bg-white p-3 rounded-md shadow-sm text-center border border-gray-50">
                <h3 class="font-medium text-3xl text-green-700">
                    Statistik Populasi Ternak Gopala Dwi Amerta Sari
                </h3>
            </div>

            
            <div class="bg-white p-6 rounded-md shadow-sm border border-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium text-gray-500">Perkembangan Populasi Ternak</h4>
                    <div x-data="{open: false, selected: 'Semua Anggota', search: ''}" class="relative w-56">
                        
                        <button @click="open = !open"
                            class="w-full flex justify-between items-center border border-gray-300 rounded-lg px-2 py-1.5 bg-white text-gray-700 text-sm focus:ring-0 focus:ring-green-500 focus:border-green-500">
                            <span x-text="selected" class="truncate block w-full text-left"></span>
                            <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        
                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg">

                            
                            <div class="p-2 border-b border-gray-100">
                                <div class="relative">
                                    <input type="text" placeholder="Search" x-model="search"
                                        class="w-full pl-4 pr-8 py-1.5 text-sm border placeholder:text-gray-800 border-gray-200 rounded-lg focus:ring-green-500 focus:border-green-500 focus:ring-0">

                                    
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <ul class="max-h-60 overflow-y-auto scrollbar-hide text-sm">
                                
                                <li>
                                    <button type="button"
                                        @click="selected = 'Semua Anggota'; open = false; loadChartData('')"
                                        class="w-full text-left px-4 py-2 text-slate-700 hover:bg-green-100 truncate">
                                        Semua Anggota
                                    </button>
                                </li>

                                
                                <?php $__currentLoopData = $semuaAnggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li x-show="'<?php echo e(strtolower($anggota->nama)); ?>'.includes(search.toLowerCase())">
                                    <button type="button"
                                        @click="selected = '<?php echo e($anggota->nama); ?>'; open = false; loadChartData('<?php echo e($anggota->id); ?>')"
                                        class="w-full text-left px-4 py-2 text-gray-700 hover:bg-green-100 truncate">
                                        <?php echo e($anggota->nama); ?>

                                    </button>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>

                
                <div class="h-80">
                    <div id="chart-loading" class="flex items-center justify-center h-full text-gray-500" style="display:none;">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-700 mx-auto mb-2" id="spinner"></div>
                            <p id="loading-text">Memuat data...</p>
                        </div>
                    </div>
                    <canvas id="historicalChart" class="w-full h-full"></canvas>
                </div>

                
                <div class="flex flex-col space-y-1 mt-3" id="chart-legend">
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-green-800 rounded-sm"></span>
                        <span class="text-gray-500 text-sm">Sapi</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 bg-gray-800 rounded-sm"></span>
                        <span class="text-gray-500 text-sm">Kambing</span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-md shadow-sm border border-gray-50 p-6 space-y-4">

            
            <div class="border rounded-md p-4">
                <h4 class="text-md font-medium text-gray-500 mb-2">Total Harga Awal Ternak</h4>
                <p class="text-2xl font-extrabold text-green-700">Rp <?php echo e(number_format($totalHargaTernak, 0, ',', '.')); ?></p>
            </div>

            
            <div class="border rounded-md p-4">
                <h4 class="text-md font-medium text-gray-500 mb-2">Total Jumlah Ternak</h4>
                <p class="text-2xl font-extrabold text-green-700"><?php echo e($totalJumlahTernak); ?></p>
            </div>

            
            <div class="border rounded-md p-4">
                <h4 class="text-md font-medium text-gray-500 mb-4">Persebaran Lokasi Kandang</h4>
                <div class="h-48 relative">
                    <canvas id="kandangPieChart" class="w-full h-full"></canvas>
                </div>

                
                <div id="kandang-legend" class="mt-3 space-y-2">
                    
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // =====================================================================
    // BAGIAN PIE CHART (Persebaran Kandang)
    // =====================================================================
    const persebaranKandang = <?php echo json_encode($persebaranKandang, 15, 512) ?>;
    const pieCtx = document.getElementById('kandangPieChart');

    if (pieCtx && Object.keys(persebaranKandang).length > 0) {
        const labels = Object.keys(persebaranKandang);
        const data = Object.values(persebaranKandang);
        const bgColors = ['#16a34a', '#1f2937', '#f97316', '#3b82f6', '#6d28d9', '#dc2626', '#eab308', '#0891b2'];
        
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: bgColors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (c) => `${c.label}: ${c.parsed} Anggota`
                        }
                    }
                }
            }
        });
    } else if (pieCtx) {
        const noDataMsg = document.createElement('p');
        noDataMsg.className = 'text-sm text-gray-500 text-center';
        noDataMsg.textContent = 'Tidak ada data lokasi kandang';
        pieCtx.parentElement.appendChild(noDataMsg);
    }

    // =====================================================================
    // BAGIAN BAR CHART (Historical Data)
    // =====================================================================

    // 1. Definisikan semua elemen DOM dan variabel di awal untuk efisiensi.
    let historicalChart = null;
    const historicalCtx = document.getElementById('historicalChart').getContext('2d');
    const chartLoading = document.getElementById('chart-loading');
    const chartCanvas = document.getElementById('historicalChart');
    const chartLegend = document.getElementById('chart-legend');
    const spinner = document.getElementById('spinner');
    const loadingText = document.getElementById('loading-text');
    
    // Helper function untuk menampilkan pesan (DRY - Don't Repeat Yourself)
    function showMessage(message, isError = false) {
        if (historicalChart) {
            historicalChart.destroy();
            historicalChart = null;
        }
        chartCanvas.style.display = 'none';
        chartLegend.style.display = 'none';
        chartLoading.style.display = 'flex';
        spinner.style.display = 'none';
        loadingText.textContent = message;
        loadingText.className = isError ? 'text-red-500' : 'text-gray-500';
    }
    
    // Fungsi untuk merender chart
    function renderHistoricalChart(chartData) {
        if (historicalChart) {
            historicalChart.destroy();
        }

        chartCanvas.style.display = 'block';
        chartLegend.style.display = 'flex';
        chartLoading.style.display = 'none';

        historicalChart = new Chart(historicalCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Sapi',
                    data: chartData.datasets[0].data,
                    backgroundColor: '#166534',
                    borderColor: '#166534',
                    borderWidth: 1
                }, {
                    label: 'Kambing',
                    data: chartData.datasets[1].data,
                    backgroundColor: '#1f2937', // Warna konsisten dengan legenda
                    borderColor: '#1f2937',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { display: true, title: { display: true, text: 'Bulan Pencatatan' }},
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, title: { display: true, text: 'Jumlah Ternak' }}
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (ctx) => `Pencatatan: ${ctx[0].label}`,
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.parsed.y} ekor`
                        }
                    }
                }
            }
        });
    }

    // 2. Definisikan fungsi utama sebagai properti 'window' agar bisa diakses oleh Alpine.js.
    window.loadChartData = function(anggotaId) {
        chartLoading.style.display = 'flex';
        chartCanvas.style.display = 'none';
        chartLegend.style.display = 'none';
        spinner.style.display = 'block';
        loadingText.textContent = 'Memuat data...';
        loadingText.className = 'text-gray-500';

        fetch(`/dashboard/chart-data?anggota_id=${anggotaId}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                if (data.labels && data.labels.length > 0) {
                    renderHistoricalChart(data);
                } else {
                    showMessage('Belum ada data pencatatan untuk ditampilkan');
                }
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                showMessage('Gagal memuat data chart', true);
            });
    }

    // 3. Panggil data awal untuk "Semua Anggota".
    loadChartData('');
});
</script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\Dewa Juli\Documents\My Project\Proyek Sipaten\sipaten\resources\views/dashboard.blade.php ENDPATH**/ ?>