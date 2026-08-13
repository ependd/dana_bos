<div class="space-y-6">
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari Kategori..." 
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 shadow-xs">
        </div>

        @if(auth()->user()->isAdmin())
        <div class="w-full sm:w-auto flex items-center justify-end gap-3">
            @if(count($selectedKategori) > 0)
            <button type="button"
                    @click="confirmDelete('Hapus {{ count($selectedKategori) }} Kategori', 'Apakah Anda yakin ingin menghapus {{ count($selectedKategori) }} kategori terpilih?', () => $wire.deleteSelected())"
                    class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Terpilih ({{ count($selectedKategori) }})</span>
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
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4">Kelompok / Bagian</th>
                        <th class="px-6 py-4">Jumlah Item di Katalog</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/80">
                    @forelse($kategoris as $k)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors {{ in_array((string)$k->id, $selectedKategori) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}">
                        @if(auth()->user()->isAdmin())
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" wire:model.live="selectedKategori" value="{{ $k->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>
                        @endif
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white text-sm">
                            {{ $k->nama }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClass = match($k->kelompok ?? '') {
                                    'Pembayaran Honorarium' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
                                    'Buku & Perpustakaan' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
                                    'Pemeliharaan Sarana & Prasarana' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
                                    'Operasional & Kegiatan Sekolah' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                                    default => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $badgeClass }}">
                                {{ $k->kelompok ?? 'Lainnya' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 dark:bg-zinc-800 dark:text-zinc-300 border border-gray-200 dark:border-zinc-700">
                                {{ $k->katalog_barangs_count }} Item Barang
                            </span>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <button wire:click="openEditModal({{ $k->id }})" title="Edit Kategori" class="p-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-zinc-800 rounded-lg cursor-pointer transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" 
                                        @click="if ({{ $k->katalog_barangs_count }} > 0) {
                                            Swal.fire({
                                                title: 'Kategori Berisi Item Barang',
                                                text: 'Kategori \'{{ addslashes($k->nama) }}\' tidak dapat dihapus karena masih terikat dengan {{ $k->katalog_barangs_count }} item barang pada Katalog Barang.',
                                                icon: 'warning',
                                                confirmButtonColor: '#ef4444',
                                                confirmButtonText: 'Mengerti',
                                                customClass: {
                                                    popup: 'rounded-3xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:bg-zinc-900 dark:text-white',
                                                    title: 'text-lg font-bold text-gray-900 dark:text-white',
                                                    confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md cursor-pointer'
                                                }
                                            });
                                        } else {
                                            confirmDelete('Hapus Kategori', 'Apakah Anda yakin ingin menghapus kategori {{ addslashes($k->nama) }}?', () => $wire.delete({{ $k->id }}));
                                        }" 
                                        title="Hapus Kategori" 
                                        class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-zinc-800 rounded-lg cursor-pointer transition-colors">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                 </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 5 : 3 }}" class="px-6 py-8 text-center text-gray-500 dark:text-zinc-400 text-sm">
                            Belum ada data kategori.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            {{ $kategoris->links() }}
        </div>
    </x-card>

    <!-- Modal Form Kategori -->
    <x-modal name="kategori-modal" title="{{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori Baru' }}" maxWidth="md">
        <form wire:submit.prevent="save" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       wire:model="nama" 
                       placeholder="misal: ATK, Bahan Ajar, Buku Teks..." 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                @error('nama') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Kelompok / Bagian Kategori <span class="text-rose-500">*</span>
                </label>
                <select wire:model="kelompok" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach($kelompokOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </select>
                @error('kelompok') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'kategori-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md transition-all cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </x-modal>

</div>
