<div class="space-y-6">
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari barang, kode, atau kategori..." 
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 shadow-xs">
        </div>

        @if(auth()->user()->isAdmin())
        <div class="w-full sm:w-auto flex items-center justify-end gap-3">
            @if(count($selectedBarangs) > 0)
            <button type="button"
                    @click="confirmDelete('Hapus {{ count($selectedBarangs) }} Barang', 'Apakah Anda yakin ingin menghapus {{ count($selectedBarangs) }} barang terpilih dari katalog?', () => $wire.deleteSelected())"
                    class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Terpilih ({{ count($selectedBarangs) }})</span>
            </button>
            @endif

            <button wire:click="openCreateModal" 
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center cursor-pointer">
                <span>Tambah</span>
            </button>
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
                        <th class="px-6 py-4">Kode Barang</th>
                        <th class="px-6 py-4">Nama Barang</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Satuan</th>
                        <th class="px-6 py-4">Harga Referensi</th>
                        <th class="px-6 py-4">Status</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/80">
                    @forelse($barangs as $b)
                    @php
                        $hasBarangData = $b->kebutuhans()->exists();
                    @endphp
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors {{ in_array((string)$b->id, $selectedBarangs) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}">
                        @if(auth()->user()->isAdmin())
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" wire:model.live="selectedBarangs" value="{{ $b->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>
                        @endif
                        <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-zinc-400">
                            {{ $b->kode_barang }}
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                            {{ $b->nama_barang }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-zinc-300">
                                {{ $b->kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            {{ $b->satuan }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($b->harga_referensi, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :color="$b->is_active ? 'success' : 'gray'">
                                {{ $b->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </x-badge>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <button wire:click="openEditModal({{ $b->id }})" title="Edit Barang" class="p-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-zinc-800 rounded-lg cursor-pointer transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" 
                                        @click="confirmDelete('Hapus Barang', 'Apakah Anda yakin ingin menghapus {{ addslashes($b->nama_barang) }} dari katalog?', () => $wire.delete({{ $b->id }}))" 
                                        title="Hapus Barang" 
                                        class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-zinc-800 rounded-lg cursor-pointer transition-colors">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                 </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 8 : 6 }}" class="px-6 py-8 text-center text-gray-500 dark:text-zinc-400 text-sm">
                            Belum ada data barang di katalog.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            {{ $barangs->links() }}
        </div>
    </x-card>

    <!-- Create/Edit Modal -->
    <x-modal name="katalog-modal" title="{{ $isEdit ? 'Edit Barang' : 'Tambah Barang Baru' }}" maxWidth="md">
        <form wire:submit.prevent="save" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Kode Barang <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       wire:model="kode_barang" 
                       required 
                       placeholder="misal: BRG-001" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                @error('kode_barang') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Nama Barang <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       wire:model="nama_barang" 
                       required 
                       placeholder="misal: Spidol Boardmarker Hitam" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                @error('nama_barang') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Kategori <span class="text-rose-500">*</span>
                </label>
                <select wire:model.live="kategori" 
                        required 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat->nama }}">{{ $kat->nama }}</option>
                    @endforeach
                </select>
                @error('kategori') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Satuan <span class="text-rose-500">*</span>
                </label>
                <select wire:model="satuan" 
                        required 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Satuan --</option>
                    @foreach($this->availableSatuans as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
                @error('satuan') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Harga Referensi (Rp) <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       wire:model="harga_referensi" 
                       required 
                       x-data
                       x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                       placeholder="misal: 15000" 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                @error('harga_referensi') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-medium text-gray-700 dark:text-zinc-300">Set Sebagai Barang Aktif</span>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'katalog-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md transition-all cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </x-modal>

</div>
