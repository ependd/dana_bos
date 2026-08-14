<div class="space-y-8">
    
    <!-- Stat Cards Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @if(auth()->user()?->isGuru())
        <x-stat-card title="Total Item Diajukan" :value="$totalItemDiajukan . ' Item'" color="blue">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Disetujui" :value="$totalDisetujui . ' Item'" color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Ditolak" :value="$totalDitolak . ' Item'" color="rose">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Barang Tersedia" :value="$totalTersedia . ' Item'" color="purple">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </x-slot:icon>
        </x-stat-card>
        @else
        <x-stat-card title="Total Pagu Anggaran" :value="'Rp ' . number_format($totalPagu, 0, ',', '.')" :description="$activePeriode ? 'Periode ' . $activePeriode->tahun_ajaran : 'Belum Ada Periode Aktif'" color="blue">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Pengajuan Kebutuhan" :value="$totalPengajuanKebutuhan . ' Data'" description="Pengajuan Kebutuhan Sekolah" color="purple">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Realisasi BKU" :value="'Rp ' . number_format($totalRealisasiBku, 0, ',', '.')" :description="$persentaseRealisasi . '% Anggaran Terpakai'" color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card title="Sisa Pagu Kas" :value="'Rp ' . number_format($sisaPagu, 0, ',', '.')" :description="$persentaseSisa . '% Sisa Pagu Kas'" color="amber">
            <x-slot:icon>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </x-slot:icon>
        </x-stat-card>
        @endif
    </div>

    @if(!auth()->user()?->isGuru() && $paguInfo)
    <!-- Executive Budget Posture Analysis Card -->
    <x-card class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between gap-4 pb-4 border-b border-gray-100 dark:border-zinc-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                <span>Evaluasi Alokasi Anggaran BOS (RKAS)</span>
            </h3>

            <div class="flex items-center gap-3 shrink-0">
                @if($paguInfo['total_pagu'] <= 0)
                <x-badge color="warning">Pagu Belum Diatur</x-badge>
                @elseif($paguInfo['total_rkas_status'] === 'danger')
                <x-badge color="danger">Melebihi Total Pagu</x-badge>
                @else
                <x-badge color="success">Sesuai Aturan</x-badge>
                @endif
            </div>
        </div>

        <!-- Stacked Visual Allocation Progress Bar -->
        <div class="pt-4 pb-2 space-y-1.5">
            <div class="flex items-center justify-end text-xs font-bold text-gray-700 dark:text-zinc-300">
                <span>{{ $paguInfo['total_rkas_pct'] }}% dari Pagu</span>
            </div>
            <div class="w-full h-3 bg-gray-100 dark:bg-zinc-800 rounded-full overflow-hidden flex shadow-inner">
                <div class="bg-indigo-500 h-full transition-all" style="width: {{ min(100, $paguInfo['honor']['pct']) }}%" title="Honorarium: {{ $paguInfo['honor']['pct'] }}%"></div>
                <div class="bg-blue-500 h-full transition-all" style="width: {{ min(100, $paguInfo['buku']['pct']) }}%" title="Buku & Perpus: {{ $paguInfo['buku']['pct'] }}%"></div>
                <div class="bg-purple-500 h-full transition-all" style="width: {{ min(100, $paguInfo['sarpras']['pct']) }}%" title="Sarpras: {{ $paguInfo['sarpras']['pct'] }}%"></div>
                <div class="bg-emerald-500 h-full transition-all" style="width: {{ min(100, $paguInfo['operasional']['pct']) }}%" title="Operasional: {{ $paguInfo['operasional']['pct'] }}%"></div>
            </div>
        </div>

        <!-- Spacious Clean Table Grid -->
        <div class="mt-2 overflow-x-auto rounded-xl border border-gray-100 dark:border-zinc-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50/80 dark:bg-zinc-800/60 text-gray-500 dark:text-zinc-400 font-semibold uppercase tracking-wider text-[11px] border-b border-gray-100 dark:border-zinc-800">
                    <tr>
                        <th class="py-3.5 px-6">Komponen Alokasi</th>
                        <th class="py-3.5 px-6">Ketentuan Target</th>
                        <th class="py-3.5 px-6">Realisasi RKAS</th>
                        <th class="py-3.5 px-6">Persentase</th>
                        <th class="py-3.5 px-6 text-right">Status Kepatuhan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/60 font-medium">
                    <!-- 1. Honorarium -->
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                        <td class="py-4 px-6 font-semibold text-gray-900 dark:text-white">
                            Pembayaran Honorarium
                        </td>
                        <td class="py-4 px-6 text-gray-500 dark:text-zinc-400">Maksimal 20%</td>
                        <td class="py-4 px-6 text-gray-900 dark:text-white font-bold">Rp {{ number_format($paguInfo['honor']['nominal'], 0, ',', '.') }}</td>
                        <td class="py-4 px-6 font-bold text-gray-700 dark:text-zinc-300">{{ $paguInfo['honor']['pct'] }}%</td>
                        <td class="py-4 px-6 text-right">
                            @if($paguInfo['honor']['status'] === 'danger')
                            <span class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Melebihi Limit 20%
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Sesuai (≤ 20%)
                            </span>
                            @endif
                        </td>
                    </tr>

                    <!-- 2. Buku & Perpustakaan -->
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                        <td class="py-4 px-6 font-semibold text-gray-900 dark:text-white">
                            Buku & Perpustakaan
                        </td>
                        <td class="py-4 px-6 text-gray-500 dark:text-zinc-400">Minimal 20%</td>
                        <td class="py-4 px-6 text-gray-900 dark:text-white font-bold">Rp {{ number_format($paguInfo['buku']['nominal'], 0, ',', '.') }}</td>
                        <td class="py-4 px-6 font-bold text-gray-700 dark:text-zinc-300">{{ $paguInfo['buku']['pct'] }}%</td>
                        <td class="py-4 px-6 text-right">
                            @if($paguInfo['buku']['status'] === 'warning')
                            <span class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-400 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Kurang dari Minimal 20%
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Memenuhi Minimal (≥ 20%)
                            </span>
                            @endif
                        </td>
                    </tr>

                    <!-- 3. Sarana & Prasarana -->
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                        <td class="py-4 px-6 font-semibold text-gray-900 dark:text-white">
                            Pemeliharaan Sarana & Prasarana
                        </td>
                        <td class="py-4 px-6 text-gray-500 dark:text-zinc-400">Maksimal 20%</td>
                        <td class="py-4 px-6 text-gray-900 dark:text-white font-bold">Rp {{ number_format($paguInfo['sarpras']['nominal'], 0, ',', '.') }}</td>
                        <td class="py-4 px-6 font-bold text-gray-700 dark:text-zinc-300">{{ $paguInfo['sarpras']['pct'] }}%</td>
                        <td class="py-4 px-6 text-right">
                            @if($paguInfo['sarpras']['status'] === 'danger')
                            <span class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Melebihi Limit 20%
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Sesuai (≤ 20%)
                            </span>
                            @endif
                        </td>
                    </tr>

                    <!-- 4. Operasional & Kegiatan -->
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors">
                        <td class="py-4 px-6 font-semibold text-gray-900 dark:text-white">
                            Operasional & Kegiatan Sekolah
                        </td>
                        <td class="py-4 px-6 text-gray-500 dark:text-zinc-400">Fleksibel (± 40%)</td>
                        <td class="py-4 px-6 text-gray-900 dark:text-white font-bold">Rp {{ number_format($paguInfo['operasional']['nominal'], 0, ',', '.') }}</td>
                        <td class="py-4 px-6 font-bold text-gray-700 dark:text-zinc-300">{{ $paguInfo['operasional']['pct'] }}%</td>
                        <td class="py-4 px-6 text-right text-gray-500 dark:text-zinc-400 font-medium">
                            Alokasi Operasional
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>
    @endif

    @if(auth()->user()?->isGuru())
    <!-- Recent Activity Table (For Guru Role Only) -->
    <div class="space-y-4">
        <x-card title="Pengajuan Kebutuhan Terbaru">
            <div class="divide-y divide-gray-100 dark:divide-zinc-800">
                @forelse($recentKebutuhans as $k)
                <div class="py-3 flex items-center justify-between text-sm">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $k->nama_barang ?? $k->katalogBarang?->nama_barang ?? '-' }}</p>
                        <p class="text-xs text-gray-500">Oleh: {{ $k->user?->name }} • {{ $k->volume }} {{ $k->satuan }}</p>
                    </div>
                    <x-badge :color="$k->status_badge_color">
                        {{ $k->status_label }}
                    </x-badge>
                </div>
                @empty
                <p class="text-sm text-gray-500 py-4 text-center">Belum ada pengajuan kebutuhan.</p>
                @endforelse
            </div>
        </x-card>
    </div>
    @endif

</div>
