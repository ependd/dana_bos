<div class="space-y-6">
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari tahun anggaran..." 
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 shadow-xs">
        </div>

        @if(auth()->user()->isAdmin())
        <div class="w-full sm:w-auto flex items-center justify-end gap-3">
            @if(count($selectedPeriodes) > 0)
            <button type="button"
                    @click="confirmDelete('Hapus {{ count($selectedPeriodes) }} Periode BOS', 'Apakah Anda yakin ingin menghapus {{ count($selectedPeriodes) }} periode BOS terpilih?', () => $wire.deleteSelected())"
                    class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Terpilih ({{ count($selectedPeriodes) }})</span>
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
                        <th class="px-6 py-4">Tahun Anggaran</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Total Anggaran Pagu</th>
                        <th class="px-6 py-4">Status</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/80">
                    @forelse($periodes as $p)
                    @php
                        $hasRelatedData = $p->rkas()->exists() || $p->bkus()->exists() || $p->kebutuhans()->exists() || $p->hasilKlasifikasis()->exists();
                    @endphp
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors {{ in_array((string)$p->id, $selectedPeriodes) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}">
                        @if(auth()->user()->isAdmin())
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" wire:model.live="selectedPeriodes" value="{{ $p->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>
                        @endif
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                            {{ $p->tahun_ajaran }}
                        </td>
                        <td class="px-6 py-4 text-xs font-mono">
                            {{ $p->tanggal_mulai?->format('d M Y') }} - {{ $p->tanggal_selesai?->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($p->anggaran, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <!-- Single Unified ON/OFF Toggle Status Button -->
                            <button wire:click="toggleActive({{ $p->id }})" 
                                     @disabled(!auth()->user()->isAdmin())
                                     title="Klik untuk mengubah Status Periode (Aktif / Non-Aktif)"
                                     class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all cursor-pointer {{ $p->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800 shadow-xs' : 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700' }}">
                                <span class="w-2.5 h-2.5 rounded-full {{ $p->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                <span>{{ $p->is_active ? 'Aktif (ON)' : 'Non-Aktif (OFF)' }}</span>
                            </button>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEditModal({{ $p->id }})" title="Edit Periode" class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" 
                                        @click="if ({{ $hasRelatedData ? 'true' : 'false' }}) {
                                            Swal.fire({
                                                title: 'Periode Terikat Data',
                                                text: 'Periode BOS \'{{ addslashes($p->tahun_ajaran) }}\' tidak dapat dihapus karena sudah memiliki data transaksi RKAS, BKU, atau Pengajuan Kebutuhan.',
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
                                            confirmDelete('Hapus Periode BOS', 'Apakah Anda yakin ingin menghapus periode BOS {{ addslashes($p->tahun_ajaran) }}?', () => $wire.delete({{ $p->id }}));
                                        }" 
                                        title="Hapus Periode"
                                        class="p-1.5 text-rose-600 hover:text-rose-800 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 6 : 4 }}" class="px-6 py-8 text-center text-gray-500 dark:text-zinc-400 text-sm">
                            Belum ada data periode BOS.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            {{ $periodes->links() }}
        </div>
    </x-card>

    <!-- Create/Edit Modal -->
    <x-modal name="periode-modal" :title="$isEdit ? 'Edit Periode BOS' : 'Tambah Periode BOS Baru'">
        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Tahun Anggaran <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="tahun_ajaran" required placeholder="Contoh: 2026/2027" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('tahun_ajaran') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                    <input type="date" wire:model="tanggal_mulai" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('tanggal_mulai') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" wire:model="tanggal_selesai" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('tanggal_selesai') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Total Pagu Anggaran (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" wire:model="anggaran" required min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('anggaran') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="w-4 h-4 rounded text-blue-600">
                    <span class="text-xs text-gray-700 dark:text-zinc-300">Set Sebagai Periode Aktif</span>
                </label>
            </div>

            <!-- Modal Footer Buttons (Inside Form) -->
            <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'periode-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </x-modal>

</div>
