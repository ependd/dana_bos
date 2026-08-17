<div class="space-y-6">

    <!-- Flash Notifications -->
    @if(session()->has('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
        <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-800 text-xs ml-4">✕</button>
    </div>
    @endif

    @if(session()->has('error'))
    <div x-data="{ show: true }" x-show="show" class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
        <button type="button" @click="show = false" class="text-rose-600 hover:text-rose-800 text-xs ml-4">✕</button>
    </div>
    @endif
    
    <!-- Status Overview Stat Cards (Data Wajar, Data Tidak Sesuai, Jumlah Data) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Data Wajar" :value="$countWajar . ' Baris'" color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Data Tidak Sesuai" :value="$countTidakSesuai . ' Baris'" color="rose">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Jumlah Data" :value="$countTotal . ' Dataset'" color="blue">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- Import Cards (Hanya Unggah RKAS & BKU + Download Template) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Import RKAS Card -->
        <x-card title="Unggah Data RKAS (.xlsx)" description="Unggah file historis Excel RKAS (Mengunggah file baru akan menimpa data lama)">
            <form wire:submit="importRkas" class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300">File Excel RKAS</label>
                        <button wire:click="downloadTemplate('rkas')" type="button" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-semibold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download Template RKAS</span>
                        </button>
                    </div>
                    <input type="file" wire:model="file_rkas" accept=".xlsx,.xls,.csv" class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('file_rkas') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg wire:loading wire:target="importRkas" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="importRkas">Unggah Data RKAS</span>
                    <span wire:loading wire:target="importRkas">Mengunggah RKAS...</span>
                </button>
            </form>
        </x-card>

        <!-- Import BKU Card -->
        <x-card title="Unggah Data BKU (.xlsx)" description="Unggah file historis Excel BKU (Mengunggah file baru akan menimpa data lama)">
            <form wire:submit="importBku" class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-zinc-300">File Excel BKU</label>
                        <button wire:click="downloadTemplate('bku')" type="button" class="text-xs text-purple-600 dark:text-purple-400 hover:underline font-semibold flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download Template BKU</span>
                        </button>
                    </div>
                    <input type="file" wire:model="file_bku" accept=".xlsx,.xls,.csv" class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    @error('file_bku') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg wire:loading wire:target="importBku" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="importBku">Unggah Data BKU</span>
                    <span wire:loading wire:target="importBku">Mengunggah BKU...</span>
                </button>
            </form>
        </x-card>

    </div>

    @if($countTotal > 0)
    <div class="flex justify-end pt-2">
        <button type="button" 
                @click="confirmDelete('Bersihkan Semua Data Training', 'Apakah Anda yakin ingin menghapus seluruh data training yang telah diunggah?', () => $wire.clearAllData())"
                class="px-4 py-2 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 dark:hover:bg-rose-900/60 rounded-xl border border-rose-200 dark:border-rose-900 transition-all flex items-center gap-2 cursor-pointer shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            <span>Bersihkan Semua Data Training</span>
        </button>
    </div>
    @endif

</div>
