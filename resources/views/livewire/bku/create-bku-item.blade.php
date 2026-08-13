<div class="max-w-4xl mx-auto space-y-6 pb-12">

    <!-- Top Action & Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200/60 dark:border-zinc-800">
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Formulir Catat Realisasi BKU</h2>
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Lengkapi rincian pengeluaran realisasi dan lampirkan foto barang bukti fisik.</p>
        </div>

        <a href="{{ route('bku.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700/80 font-semibold text-xs transition-all shadow-xs shrink-0">
            <svg class="w-4 h-4 text-gray-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke BKU</span>
        </a>
    </div>

    <!-- Rule Info Banner -->
    <div class="p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-start gap-3.5 shadow-xs">
        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-xs space-y-1">
            <h4 class="font-bold text-sm text-emerald-900 dark:text-emerald-200">Ketentuan Realisasi BKU</h4>
            <p class="leading-relaxed">
                Hanya item barang RKAS yang <strong>telah disetujui datang oleh Kepala Sekolah</strong> yang dapat dicatat pengeluarannya. Setiap transaksi wajib mengunggah foto bukti fisik barang.
            </p>
        </div>
    </div>

    <!-- Form Card Container -->
    <x-card class="p-6 sm:p-8 shadow-sm">
        <form wire:submit="save" class="space-y-6">

            <!-- 1. Select RKAS Item -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-2">
                    Item Barang RKAS (Disetujui Datang oleh Kepsek) <span class="text-rose-500">*</span>
                </label>
                <select wire:model.live="rkas_item_id" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">-- Pilih Barang Datang yang Disetujui Kepsek --</option>
                    @foreach($availableRkasItems as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->uraian_belanja }} (Rp {{ number_format($item->jumlah_setelah, 0, ',', '.') }}) - RKAS: {{ $item->rkas?->kode_rkas }}
                    </option>
                    @endforeach
                </select>
                @if($availableRkasItems->isEmpty())
                <p class="text-xs text-amber-600 dark:text-amber-400 mt-2 font-medium">Tidak ada barang yang siap dicatat. Pastikan Kepala Sekolah telah mengonfirmasi barang datang di menu "Konfirmasi Barang Datang".</p>
                @endif
                @error('rkas_item_id') <p class="text-xs text-rose-500 mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- 2. Tanggal & Nomor Bukti -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-2">
                        Tanggal Transaksi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" wire:model="tanggal" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    @error('tanggal') <p class="text-xs text-rose-500 mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-2">
                        Nomor Bukti Nota/Kwitansi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" wire:model="nomor_bukti" placeholder="Masukkan Nomor Bukti Nota/Kwitansi" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    @error('nomor_bukti') <p class="text-xs text-rose-500 mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- 3. Uraian Belanja (Read Only) -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-2">
                    Uraian Belanja / Transaksi <span class="text-rose-500">*</span>
                </label>
                <input type="text" wire:model="uraian" readonly placeholder="Otomatis terisi dari nama barang RKAS" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-zinc-700 bg-gray-100/80 dark:bg-zinc-800/60 text-gray-700 dark:text-zinc-300 text-sm cursor-not-allowed">
                <p class="text-[11px] text-gray-500 dark:text-zinc-400 mt-1.5">Deskripsi/rincian nama barang transaksi (Otomatis terisi dari RKAS saat barang dipilih di atas).</p>
                @error('uraian') <p class="text-xs text-rose-500 mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- 4. Jumlah Pengeluaran Realisasi -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-2">
                    Jumlah Pengeluaran Realisasi (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="number" step="any" wire:model.live="pengeluaran" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                @if($pengeluaran && is_numeric($pengeluaran))
                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-2 font-bold">
                    Format Nominal: Rp {{ number_format((float)$pengeluaran, floor((float)$pengeluaran) == (float)$pengeluaran ? 0 : 2, ',', '.') }}
                </p>
                @endif
                @error('pengeluaran') <p class="text-xs text-rose-500 mt-1.5 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- 5. Upload Foto Bukti Barang -->
            <div class="p-5 rounded-2xl bg-blue-50/40 dark:bg-zinc-900/60 border border-blue-100 dark:border-zinc-800 space-y-3">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-800 dark:text-zinc-200">
                    Upload Foto Bukti Barang Fisik <span class="text-rose-500">*</span>
                </label>
                
                <div x-data="{ fileName: '' }" class="space-y-3">
                    <div class="flex items-center gap-3">
                        <label class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-lg shadow-xs cursor-pointer transition-all flex items-center gap-2 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Pilih File</span>
                            <input type="file" 
                                   wire:model="foto_barang" 
                                   accept="image/*" 
                                   class="hidden" 
                                   @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                        </label>
                        <span class="text-xs text-gray-500 dark:text-zinc-400 font-medium truncate" 
                              x-text="fileName ? fileName : 'Tidak ada file yang dipilih'">
                            Tidak ada file yang dipilih
                        </span>
                    </div>
                </div>
                @error('foto_barang') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror

                @if ($foto_barang)
                <div class="mt-4 flex items-center gap-4 p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 shadow-xs">
                    <img src="{{ $foto_barang->temporaryUrl() }}" class="w-24 h-24 object-cover rounded-lg border border-gray-200 dark:border-zinc-700 shadow-xs">
                    <div>
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold block">Pratinjau foto siap diunggah</span>
                        <p class="text-[11px] text-gray-500 dark:text-zinc-400 mt-0.5">Pastikan barang bukti dan dokumen fisik terlihat dengan jelas.</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons Bar -->
            <div class="pt-6 mt-8 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-end gap-3">
                <a href="{{ route('bku.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                    <span wire:loading.remove wire:target="save">Simpan Realisasi</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>

        </form>
    </x-card>

</div>
