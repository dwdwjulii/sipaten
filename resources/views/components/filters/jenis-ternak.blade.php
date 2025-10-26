<th class="w-28 px-2 py-3 border border-gray-200 text-center">
    <div x-data="createDropdown('Jenis Ternak', 80)"
        x-init="() => { window.addEventListener('resize', ()=> open && position()); window.addEventListener('scroll', ()=> open && position(), true); }"
        x-cloak class="relative flex items-center justify-center w-full h-full">
        <!-- Tombol -->
        <button x-ref="btn" type="button" @click="toggle()" class="inline-flex items-center space-x-1 text-gray-600 ">
            <span class="text-sm text-gray-500" x-text="label"></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-gray-500 hover:text-gray-900" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2l-7 7v7l-4-2v-5L3 6V4z" />
            </svg>
        </button>

        <!-- Dropdown teleport ke body -->
        <template x-teleport="body">
            <div x-show="open" @click.outside="close()" @keydown.escape.window="close()" x-transition x-cloak
                class="bg-white border border-gray-200 rounded-md shadow-md z-[2000] overflow-hidden min-w-[0px]"
                :style="`position: absolute; top: ${top}px; left: ${left}px; min-width: ${minWidth}px;`">

                <a href="#" @click.prevent="select('Semua')"
                    class="block px-3 py-2 text-xs text-gray-700 hover:bg-green-100 whitespace-nowrap">Semua</a>
                <a href="#" @click.prevent="select('Sapi')"
                    class="block px-3 py-2 text-xs text-gray-700 hover:bg-green-100 whitespace-nowrap">Sapi</a>
                <a href="#" @click.prevent="select('Kambing')"
                    class="block px-3 py-2 text-xs text-gray-700 hover:bg-green-100 whitespace-nowrap">Kambing</a>
            </div>
        </template>
    </div>
</th>