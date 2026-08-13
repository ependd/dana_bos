<div class="space-y-8">
    
    <!-- Filter Periode Bar -->
    <x-card class="p-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Monitoring Realisasi Anggaran BOS</h3>
                <p class="text-xs text-gray-500">Visualisasi daya serap anggaran dan komparasi pagu vs pengeluaran</p>
            </div>
            <div class="w-full sm:w-auto flex flex-wrap items-center gap-3">
                <select wire:model.live="selected_periode" class="w-full sm:w-64 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @foreach($periodes as $id => $tahun)
                    <option value="{{ $id }}">{{ $tahun }}</option>
                    @endforeach
                </select>

                @if(auth()->user()?->isKepalaSekolah() && $selected_periode)
                <a href="{{ route('monitoring.download-pdf', ['periode' => $selected_periode]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh Laporan</span>
                </a>
                @endif
            </div>
        </div>
    </x-card>

    <!-- Progress Bar Daya Serap -->
    <x-card title="Persentase Daya Serap Realisasi Anggaran" description="Daya serap anggaran berdasarkan pengeluaran resmi di BKU">
        <div class="space-y-4">
            <div class="flex items-center justify-between text-sm font-semibold">
                <span class="text-gray-700 dark:text-zinc-300">Tingkat Penyerapan Dana BOS</span>
                <span class="text-blue-600 dark:text-blue-400 font-bold text-base">{{ $persentaseRealisasi }}%</span>
            </div>

            <!-- Progress Bar Bar Track -->
            <div class="w-full h-5 rounded-full bg-gray-100 dark:bg-zinc-800 overflow-hidden p-0.5 border border-gray-200 dark:border-zinc-700">
                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500 shadow-sm" style="width: {{ $persentaseRealisasi }}%"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100 dark:border-zinc-800 text-center">
                <div>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Total Pagu Periode</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalPagu, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Total Terrealisasi (BKU)</p>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-zinc-400">Sisa Pagu Kas</p>
                    <p class="text-lg font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($sisaKas, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Breakdown Kategori Belanja Cards -->
    <x-card title="Breakdown Realisasi per Kategori Belanja">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($breakdownKategori as $katName => $data)
            <div class="p-4 rounded-xl bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-800 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">{{ $katName }}</span>
                    <span class="text-[11px] px-2 py-0.5 rounded bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-mono font-bold">{{ $data['count'] }} Item</span>
                </div>
                <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($data['total'], 0, ',', '.') }}</p>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-gray-500 dark:text-zinc-400 text-sm">
                Belum ada rincian transaksi realisasi untuk dikelompokkan per kategori.
            </div>
            @endforelse
        </div>
    </x-card>

</div>
