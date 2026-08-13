<div class="space-y-6">
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari uraian transaksi BKU..." 
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 shadow-xs">
        </div>

        @if(auth()->user()->isAdmin())
        <div class="w-full sm:w-auto flex items-center justify-end gap-3">
            @if(count($selectedBkuItems) > 0)
            <button type="button"
                    @click="confirmDelete('Hapus {{ count($selectedBkuItems) }} Transaksi BKU', 'Apakah Anda yakin ingin menghapus {{ count($selectedBkuItems) }} transaksi BKU terpilih?', () => $wire.deleteSelected())"
                    class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Terpilih ({{ count($selectedBkuItems) }})</span>
            </button>
            @endif

            <a href="{{ route('bku.create') }}" 
               class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center cursor-pointer">
                <span>Tambah</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Data Table -->
    <x-card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-zinc-300">
                <thead class="bg-gray-50 dark:bg-zinc-800/60 text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider border-b border-gray-100 dark:border-zinc-800">
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <th class="px-4 py-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        @endif
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4">Tgl & No. Bukti</th>
                        <th class="px-6 py-4">Uraian Transaksi Realisasi</th>
                        <th class="px-6 py-4">Pengeluaran (Rp)</th>
                        <th class="px-6 py-4">Foto Bukti Barang</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/80">
                    @forelse($items as $index => $i)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors {{ in_array((string)$i->id, $selectedBkuItems) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}">
                        @if(auth()->user()->isAdmin())
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" wire:model.live="selectedBkuItems" value="{{ $i->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>
                        @endif
                        <td class="px-6 py-4 text-center font-bold text-gray-500 dark:text-zinc-400">
                            {{ $items->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($i->tanggal)->format('d M Y') }}</p>
                            <p class="text-xs font-mono text-gray-400 dark:text-zinc-500">{{ $i->nomor_bukti }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $i->uraian }}</p>
                            @if($i->kode_rekening)
                            <p class="text-xs font-mono text-gray-400 dark:text-zinc-500">Rek: {{ $i->kode_rekening }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-rose-600 dark:text-rose-400 whitespace-nowrap">
                            Rp {{ number_format($i->pengeluaran, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($i->foto_barang)
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('storage/' . $i->foto_barang) }}" alt="Bukti" class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-zinc-700 shadow-2xs">
                                <button wire:click="$set('previewPhotoUrl', '{{ asset('storage/' . $i->foto_barang) }}'); $dispatch('open-modal', 'photo-preview-modal')" 
                                        class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer" title="Lihat Foto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                @if(auth()->user()->isAdmin())
                                <button wire:click="openUploadPhotoModal({{ $i->id }})" 
                                        class="p-1.5 text-amber-600 hover:text-amber-800 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer" title="Ubah Foto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </button>
                                @endif
                            </div>
                            @else
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">Belum ada foto</span>
                                @if(auth()->user()->isAdmin())
                                <button wire:click="openUploadPhotoModal({{ $i->id }})" 
                                        class="px-2 py-1 bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300 text-xs font-semibold rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-colors cursor-pointer">
                                    Unggah
                                </button>
                                @endif
                            </div>
                            @endif
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <button type="button" 
                                    @click="confirmDelete('Hapus Transaksi BKU', 'Apakah Anda yakin ingin menghapus transaksi {{ addslashes($i->uraian) }}?', () => $wire.deleteItem({{ $i->id }}))" 
                                    title="Hapus Transaksi BKU" 
                                    class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-zinc-800 rounded-lg cursor-pointer transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 7 : 5 }}" class="px-6 py-8 text-center text-gray-500 dark:text-zinc-400 text-sm">
                            Belum ada transaksi realisasi BKU.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bku && $items->isNotEmpty())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            {{ $items->links() }}
        </div>
        @endif
    </x-card>

    <!-- Photo Preview Modal -->
    <x-modal name="photo-preview-modal" title="Foto Bukti Fisik Barang" maxWidth="lg">
        <div class="p-2 flex justify-center">
            @if($previewPhotoUrl)
            <img src="{{ $previewPhotoUrl }}" alt="Foto Bukti Barang" class="max-h-96 rounded-xl object-contain shadow-md">
            @endif
        </div>
        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'photo-preview-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-sm font-semibold cursor-pointer">
                Tutup
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Upload Photo Modal -->
    <x-modal name="upload-photo-modal" title="Unggah Foto Bukti Fisik Barang" maxWidth="md">
        <form wire:submit.prevent="saveItemPhoto" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Pilih Foto Bukti Barang (JPG/PNG, Max 2MB) <span class="text-rose-500">*</span>
                </label>
                <input type="file" wire:model="new_foto_barang" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                @error('new_foto_barang') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'upload-photo-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 text-sm font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md transition-all cursor-pointer">
                    Unggah Foto
                </button>
            </div>
        </form>
    </x-modal>

</div>
