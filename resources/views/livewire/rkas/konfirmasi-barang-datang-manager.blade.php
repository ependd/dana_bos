<div class="space-y-6">
    
    <!-- Rule 4 Info Banner -->
    <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300 flex items-start gap-3 shadow-xs">
        <svg class="w-6 h-6 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <h4 class="font-bold text-sm">Ketentuan Persetujuan Barang Datang</h4>
            <p class="text-xs mt-0.5">Kepala Sekolah mengonfirmasi fisik barang dari RKAS yang telah benar-benar diterima di sekolah. Admin/Bendahara <strong>hanya dapat mencatat pengeluaran BKU Realisasi</strong> untuk barang yang telah dikonfirmasi disetujui di menu ini.</p>
        </div>
    </div>

    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari uraian barang..." 
                   class="w-full sm:w-64 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 shadow-xs">
            
            <select wire:model.live="filterStatus" class="w-full sm:w-44 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                <option value="semua">Semua Status</option>
                <option value="belum_datang">Belum Datang</option>
                <option value="diterima">Diterima / Datang</option>
            </select>
        </div>

        @if(!empty($selectedIds))
        <button wire:click="bulkConfirmArrival" 
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Konfirmasi Terpilih ({{ count($selectedIds) }} Item)</span>
        </button>
        @endif
    </div>

    <!-- Data Table -->
    <x-card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-zinc-300">
                <thead class="bg-gray-50 dark:bg-zinc-800/60 text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider border-b border-gray-100 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 dark:border-zinc-700 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </th>
                        <th class="px-6 py-4">Nama / Uraian Barang</th>
                        <th class="px-6 py-4">Kode RKAS</th>
                        <th class="px-6 py-4">Volume & Satuan</th>
                        <th class="px-6 py-4">Total Anggaran</th>
                        <th class="px-6 py-4">Status Penerimaan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/80">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                        <td class="px-4 py-4 text-center">
                            @if($item->status_barang_datang !== 'diterima')
                            <input type="checkbox" wire:model.live="selectedIds" value="{{ $item->id }}" class="rounded border-gray-300 dark:border-zinc-700 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            @else
                            <span class="text-gray-300 dark:text-zinc-600 text-xs font-mono">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $item->uraian_belanja }}</p>
                            <p class="text-xs text-gray-400 font-mono">Rek: {{ $item->kode_rekening }}</p>
                        </td>
                        <td class="px-6 py-4 text-xs font-semibold text-blue-600 dark:text-blue-400">
                            {{ $item->rkas?->kode_rkas }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                            {{ (float)$item->volume_setelah == (int)$item->volume_setelah ? (int)$item->volume_setelah : number_format($item->volume_setelah, 2, ',', '.') }} {{ $item->satuan_setelah }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                            Rp {{ number_format($item->jumlah_setelah, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($item->status_barang_datang === 'diterima')
                            <x-badge color="success">
                                Diterima Fisik ({{ $item->tanggal_barang_datang ? \Carbon\Carbon::parse($item->tanggal_barang_datang)->format('d M Y') : '-' }})
                            </x-badge>
                            @else
                            <x-badge color="amber">
                                Belum Datang
                            </x-badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($item->status_barang_datang !== 'diterima')
                            <button wire:click="confirmArrival({{ $item->id }})" 
                                    @disabled(!auth()->user()->isKepalaSekolah() && !auth()->user()->isAdmin())
                                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 ml-auto cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Konfirmasi Barang Datang</span>
                            </button>
                            @else
                            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold inline-flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Selesai Dikonfirmasi</span>
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-zinc-400 text-sm">
                            Belum ada item barang dari RKAS yang disetujui.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            {{ $items->links() }}
        </div>
    </x-card>

</div>
