
<div class="flex items-center text-sm gap-1">
    <label class="text-gray-600 font-medium">Tampilkan</label>

    <div x-data="{
        open: false,
        // Ambil nilai 'per_page' dari URL saat ini, jika tidak ada, default ke '10'
        selected: new URLSearchParams(window.location.search).get('per_page') || '10',
    
        // Fungsi untuk mengubah jumlah entri
        changeEntries(value) {
            // Buat objek URL dari URL halaman saat ini
            const url = new URL(window.location.href);
            // Atur atau tambahkan parameter 'per_page' dengan nilai yang baru
            url.searchParams.set('per_page', value);
            // Arahkan browser ke URL yang baru (ini akan me-reload halaman)
            window.location.href = url.toString();
        }
    }" class="relative inline-block text-left">

        
        <button @click="open = !open"
            class="flex items-center justify-between space-x-1 px-3 w-20 py-1.5 text-xs font-medium text-gray-800 bg-white border border-gray-200 rounded-md focus:ring-0 focus:ring-green-500 focus:border-green-500">
            <span x-text="selected"></span>
            <svg class="w-4 h-4 text-gray-500 transform transition-transform duration-200"
                :class="open ? 'rotate-180' : ''" xmlns="http://www.w.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        
        <div x-show="open" x-transition x-cloak @click.outside="open = false"
            class="absolute bottom-full mb-1 z-50 w-20 bg-white border border-gray-200 rounded-md shadow-md">
            <ul class="text-xs text-gray-700 max-h-40 overflow-y-auto">
                
                <template x-for="option in ['10', '25', '40', '55', 'Semua']" :key="option">
                    
                    <li @click="changeEntries(option)"
                        class="px-3 py-2 hover:bg-green-100 cursor-pointer" x-text="option">
                    </li>
                </template>
            </ul>
        </div>
    </div>
</div><?php /**PATH C:\MAHENDRA\Project\sipaten\resources\views/components/show-entries.blade.php ENDPATH**/ ?>