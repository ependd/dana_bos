<div class="space-y-6">

    @if($viewMode === 'create')
    <!-- Dedicated Page Form Pengajuan Kebutuhan (Tanpa Modal) -->
    <div class="space-y-6">
        
        <!-- Page Header with Back Button -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-zinc-800">
            <div class="flex items-center gap-3">
                <button wire:click="backToList" 
                        class="p-2.5 rounded-xl bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 text-gray-600 dark:text-zinc-300 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors shadow-xs cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Form Pengajuan Kebutuhan Barang</h1>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">Tambah item barang kebutuhan yang diperlukan untuk disetujui Admin/Bendahara Sekolah</p>
                </div>
            </div>

            <button wire:click="backToList" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-xs font-semibold transition-colors cursor-pointer">
                Kembali ke Daftar
            </button>
        </div>

        <!-- Section 1: Form Input Item -->
        <x-card title="Tambah Item Barang" description="Pilih kategori dan barang dari katalog yang sesuai">
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Kategori Belanja</label>
                        <select wire:model.live="kategori_belanja" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                        @error('kategori_belanja') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Barang dari Katalog</label>
                        <select wire:model.live="katalog_barang_id" @disabled(!$kategori_belanja) class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_barang }} ({{ $b->satuan }})</option>
                            @endforeach
                        </select>
                        @error('katalog_barang_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Volume / Jumlah</label>
                        <input type="number" wire:model="volume" min="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        @error('volume') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Satuan</label>
                        <input type="text" wire:model="satuan" readonly class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-900 text-gray-500 text-sm font-semibold">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" wire:click="addItem" 
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span>Tambah ke Daftar</span>
                    </button>
                </div>
            </div>
        </x-card>

        <!-- Section 2: Daftar Item Pengajuan -->
        <x-card title="Daftar Item Pengajuan" description="Daftar seluruh item barang yang dimasukkan dalam pengajuan ini">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 text-xs font-bold">{{ count($items) }} item dalam daftar</span>

                    @if(count($selectedCartIndexes) > 0)
                    <button type="button" wire:click="removeSelectedCartItems" class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Terpilih ({{ count($selectedCartIndexes) }})
                    </button>
                    @endif
                </div>

                @if(count($items) > 0)
                <div class="rounded-2xl border border-gray-200 dark:border-zinc-700 overflow-hidden shadow-xs">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-zinc-800/60 text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider">
                            <tr>
                                <th class="w-12 px-6 py-4 text-center">
                                    <input type="checkbox" wire:model.live="selectAllCart" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-zinc-700 cursor-pointer">
                                </th>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Barang</th>
                                <th class="px-6 py-4 text-left">Kategori</th>
                                <th class="px-6 py-4 text-center">Volume</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                            @foreach($items as $index => $item)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors {{ in_array((string)$index, $selectedCartIndexes) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}">
                                <td class="px-6 py-4 text-center align-middle">
                                    <input type="checkbox" wire:model.live="selectedCartIndexes" value="{{ (string)$index }}" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-zinc-700 cursor-pointer">
                                </td>
                                <td class="px-6 py-4 text-gray-400 font-mono text-xs align-middle">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white align-middle">{{ $item['nama_barang'] }}</td>
                                <td class="px-6 py-4 align-middle">
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-xs bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 font-mono">
                                        {{ $item['kategori_belanja'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center align-middle font-semibold text-gray-900 dark:text-white">
                                    {{ $item['volume'] }} {{ $item['satuan'] }}
                                </td>
                                <td class="px-6 py-4 text-center align-middle">
                                    <button wire:click="removeItem({{ $index }})" type="button" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="py-12 flex flex-col items-center justify-center text-center text-gray-400 dark:text-zinc-500 text-sm border-2 border-dashed border-gray-200 dark:border-zinc-700 rounded-2xl">
                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="font-medium text-gray-500 dark:text-zinc-400">Belum ada item dalam daftar.</span>
                    <span class="text-xs text-gray-400 dark:text-zinc-500 mt-0.5">Gunakan form "Tambah Item Barang" di atas untuk menambahkan barang.</span>
                </div>
                @endif

                <!-- Submit Action Footer -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-zinc-800">
                    <button type="button" wire:click="backToList" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="save" @disabled(count($items) === 0) 
                            class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kirim Pengajuan ({{ count($items) }} Item)</span>
                    </button>
                </div>
            </div>
        </x-card>

    </div>
    @else



    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari pengaju / nama barang..." 
                   class="w-full sm:w-64 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 shadow-xs">
            
            <select wire:model.live="filterStatus" class="w-full sm:w-44 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                <option value="">Semua Status</option>
                <option value="diajukan">Diajukan</option>
                <option value="diterima">Disetujui</option>
                <option value="ditolak">Ditolak</option>
                <option value="tersedia">Barang Tersedia</option>
            </select>
        </div>

        @if(auth()->user()->isGuru())
            @if(!$hasSubmitted)
            <button wire:click="openCreatePage" 
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center cursor-pointer">
                <span>Tambah</span>
            </button>
            @else
            <button disabled class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gray-200 dark:bg-zinc-800 text-gray-400 font-semibold text-sm cursor-not-allowed flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Sudah Mengajukan</span>
            </button>
            @endif
        @endif
    </div>

    <!-- Bulk Action Toolbar (For Admin when items are selected) -->
    @if(auth()->user()->isAdmin() && count($selectedIds) > 0)
    <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 flex flex-wrap items-center justify-between gap-4 shadow-sm animate-fade-in">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-blue-600 text-white text-xs font-bold shadow-xs">
                {{ count($selectedIds) }} Terpilih
            </span>
            <p class="text-xs text-blue-900 dark:text-blue-200 font-medium">Pilih aksi massal untuk item yang ditandai:</p>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="bulkApprove" 
                    class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Setujui Terpilih
            </button>

            <button wire:click="openBulkRejectModal" 
                    class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Tolak Terpilih
            </button>

            <button type="button" 
                    @click="confirmDelete('Hapus {{ count($selectedIds) }} Pengajuan', 'Apakah Anda yakin ingin menghapus {{ count($selectedIds) }} pengajuan kebutuhan terpilih?', () => $wire.deleteSelectedKebutuhan())" 
                    class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus Terpilih
            </button>
        </div>
    </div>
    @endif

    <!-- Data Table Card -->
    <x-card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-zinc-800/60 text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider border-b border-gray-100 dark:border-zinc-800">
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <th class="w-10 px-6 py-4 text-center">
                            <input type="checkbox" 
                                   wire:model.live="selectAll" 
                                   class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-zinc-700 cursor-pointer">
                        </th>
                        @endif
                        <th class="px-6 py-4">Barang</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Volume</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4">Pengaju / Unit</th>
                        <th class="px-6 py-4">Estimasi Total</th>
                        @endif
                        <th class="px-6 py-4">Status</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                    @forelse($kebutuhans as $k)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition-colors {{ in_array((string)$k->id, $selectedIds) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}">
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 text-center align-middle">
                            <input type="checkbox" 
                                   wire:model.live="selectedIds" 
                                   value="{{ (string)$k->id }}" 
                                   class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-zinc-700 cursor-pointer">
                        </td>
                        @endif
                        <td class="px-6 py-4 align-middle">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $k->nama_barang ?? $k->katalogBarang?->nama_barang ?? 'Barang' }}</p>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-zinc-400 font-mono">
                                {{ $k->kategori_belanja }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-middle font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $k->volume }} {{ $k->satuan }}
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 align-middle">
                            <p class="font-medium text-gray-900 dark:text-white leading-snug">{{ $k->user?->name }}</p>
                            <p class="text-[11px] text-gray-400 capitalize">{{ str_replace('_', ' ', $k->user?->role ?? '') }}</p>
                        </td>
                        <td class="px-6 py-4 align-middle font-semibold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                            Rp {{ number_format($k->total_harga_estimasi, 0, ',', '.') }}
                        </td>
                        @endif
                        <td class="px-6 py-4 align-middle">
                            <div class="space-y-1.5">
                                <x-badge :color="$k->status_badge_color">
                                    {{ $k->status_label }}
                                </x-badge>
                                @if($k->catatan_admin)
                                    <div class="mt-1.5 p-2 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-900/40 text-[11px] text-rose-600 dark:text-rose-400 font-medium leading-relaxed max-w-xs">
                                        {{ $k->catatan_admin }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 align-middle text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1">
                                @if($k->status === 'diajukan')
                                <button wire:click="openEditModal({{ $k->id }})" 
                                        class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-colors cursor-pointer"
                                        title="Edit Volume & Setujui (ACC)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <button wire:click="approve({{ $k->id }})" 
                                        class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-colors cursor-pointer"
                                        title="Setujui langsung">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>

                                <button wire:click="openRejectModal({{ $k->id }})" 
                                        class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-colors cursor-pointer"
                                        title="Tolak">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                @endif

                                <button type="button" 
                                        @click="confirmDelete('Hapus Pengajuan', 'Apakah Anda yakin ingin menghapus pengajuan kebutuhan {{ addslashes($k->nama_barang ?? $k->katalogBarang?->nama_barang ?? '') }}?', () => $wire.deleteKebutuhan({{ $k->id }}))" 
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                                        title="Hapus Permanent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 8 : 4 }}" class="px-6 py-12 text-center text-gray-400 dark:text-zinc-500">
                            Belum ada pengajuan kebutuhan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kebutuhans->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            {{ $kebutuhans->links() }}
        </div>
        @endif
    </x-card>

    <!-- Modal Penolakan Admin (Rule 2: Select Dropdown Formal) -->
    <x-modal name="reject-modal" title="Konfirmasi Penolakan Pengajuan">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Pilih Alasan Penolakan Resmi</label>
                <select wire:model="catatan_admin_dropdown" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @foreach($rejectionOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Alasan resmi akan dicatat dan dapat dilihat oleh pengaju.</p>
            </div>
        </div>

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'reject-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold">
                Batal
            </button>
            <button type="button" wire:click="rejectConfirm" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold shadow-md">
                Tolak Pengajuan
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Custom Konfirmasi Hapus -->
    <x-modal name="delete-confirm-modal" title="Konfirmasi Hapus Pengajuan" maxWidth="md">
        <div class="text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <div class="space-y-1">
                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                    @if($isBulkDelete)
                        Hapus {{ count($selectedIds) }} Data Pengajuan?
                    @else
                        Hapus Data Pengajuan Ini?
                    @endif
                </h4>
                <p class="text-xs text-gray-500 dark:text-zinc-400 leading-relaxed">
                    @if($isBulkDelete)
                        Seluruh <strong>{{ count($selectedIds) }} data pengajuan</strong> yang dipilih akan dihapus permanen dari sistem. Tindakan ini tidak dapat dibatalkan.
                    @else
                        Data pengajuan kebutuhan ini akan dihapus dari sistem secara permanen. Tindakan ini tidak dapat dibatalkan.
                    @endif
                </p>
            </div>
        </div>

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'delete-confirm-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold transition-all">
                Batal
            </button>
            <button type="button" wire:click="confirmDelete" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white text-sm font-semibold shadow-md shadow-rose-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Ya, Hapus
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Edit Kebutuhan -->
    <x-modal name="edit-kebutuhan-modal" title="Edit & Setujui Data Kebutuhan Barang" maxWidth="2xl">
        <form wire:submit.prevent="update" class="space-y-4">
            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Perubahan rincian kebutuhan oleh Admin ini akan sekaligus menyetujui (ACC) pengajuan barang.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-zinc-400 mb-1">Kategori Belanja</label>
                <select disabled wire:model="kategori_belanja" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-900 text-gray-500 text-sm font-semibold cursor-not-allowed">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-zinc-400 mb-1">Barang dari Katalog</label>
                <select disabled wire:model="katalog_barang_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-900 text-gray-500 text-sm font-semibold cursor-not-allowed">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $b)
                    <option value="{{ $b->id }}">{{ $b->nama_barang }} (Rp {{ number_format($b->harga_referensi, 0, ',', '.') }} / {{ $b->satuan }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Volume / Kuantitas (Jumlah)</label>
                    <input type="number" wire:model="volume" min="1" class="w-full px-4 py-2.5 rounded-xl border border-amber-300 dark:border-amber-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm font-bold focus:ring-2 focus:ring-amber-500">
                    @error('volume') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-zinc-400 mb-1">Satuan</label>
                    <input type="text" wire:model="satuan" readonly class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-900 text-gray-500 text-sm font-semibold">
                </div>
            </div>

            <x-slot:footer>
                <button type="button" @click="$dispatch('close-modal', 'edit-kebutuhan-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="update" wire:loading.attr="disabled" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold shadow-md transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan & Setujui (ACC)</span>
                </button>
            </x-slot:footer>
        </form>
    </x-modal>

    @endif

</div>
