<div class="max-w-4xl mx-auto py-6 px-4 space-y-6">

    @if(!$rkas)
    <!-- Document Not Found State -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-rose-200 dark:border-rose-900/50 p-8 text-center space-y-4 shadow-xl">
        <div class="w-20 h-20 rounded-full bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-inner">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Dokumen Tidak Ditemukan</h2>
        <p class="text-sm text-gray-600 dark:text-zinc-400 max-w-md mx-auto">
            Kode RKAS <strong class="font-mono text-rose-600 dark:text-rose-400">{{ $kode }}</strong> tidak terdaftar dalam database pengesahan resmi Sistem Dana BOS SMPN 40 Kota Bekasi.
        </p>
        <div class="pt-2">
            <a href="{{ route('rkas.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Aplikasi</span>
            </a>
        </div>
    </div>
    @elseif($rkas->status !== 'approved')
    <!-- Document Not Yet Approved State -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-amber-200 dark:border-amber-900/50 p-8 text-center space-y-4 shadow-xl">
        <div class="w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto shadow-inner">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Dokumen Belum Disetujui</h2>
        <p class="text-sm text-gray-600 dark:text-zinc-400 max-w-md mx-auto">
            Dokumen RKAS <strong class="font-mono text-amber-600 dark:text-amber-400">{{ $rkas->kode_rkas }}</strong> masih dalam status 
            <strong class="uppercase">{{ match($rkas->status) { 'draft' => 'Draf', 'submitted' => 'Diajukan', 'rejected' => 'Ditolak', default => $rkas->status } }}</strong> 
            dan belum mendapatkan persetujuan pengesahan digital resmi dari Kepala Sekolah.
        </p>
        <div class="pt-2">
            <a href="{{ route('rkas.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke RKAS</span>
            </a>
        </div>
    </div>
    @else
    <!-- Official Verified Document Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 p-6 md:p-8 text-white shadow-2xl">
        <!-- Background Pattern Decor -->
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-12 top-6 text-white/10 pointer-events-none">
            <svg class="w-44 h-44" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-5.45 9-12V5l-9-3zm-2 16l-4-4 1.41-1.41L10 15.17l6.59-6.59L18 10l-8 8z"/>
            </svg>
        </div>

        <div class="relative z-10 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-xs font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4 text-emerald-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <span>DOKUMEN RESMI TERVERIFIKASI</span>
                </div>

                <button onclick="window.print()" class="no-print inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 text-xs font-semibold transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Lembar Verifikasi</span>
                </button>
            </div>

            <div class="space-y-1">
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                    Pengesahan Digital RKAS
                </h1>
                <p class="text-xs md:text-sm text-emerald-100">
                    Sistem Pengelolaan Dana BOS — SMP Negeri 40 Kota Bekasi
                </p>
            </div>

            <div class="pt-2 flex flex-wrap items-center gap-4 text-xs font-mono text-emerald-100">
                <span class="px-3 py-1 rounded-lg bg-black/20 backdrop-blur-xs border border-white/10">
                    Kode: <strong>{{ $rkas->kode_rkas }}</strong>
                </span>
                <span class="px-3 py-1 rounded-lg bg-black/20 backdrop-blur-xs border border-white/10">
                    Periode: <strong>{{ $rkas->periodeBos?->tahun_ajaran }}</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Status Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status Dokumen</p>
                <p class="text-base font-bold text-emerald-600 dark:text-emerald-400">Disetujui & Disahkan</p>
            </div>
        </div>

        <!-- Penyetuju Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Disetujui Oleh</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $rkas->approver?->name ?? 'Kepala Sekolah' }}</p>
                <p class="text-[11px] text-gray-500">NIP: {{ $rkas->approver?->nip ?? '-' }}</p>
            </div>
        </div>

        <!-- Total Anggaran Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-100 dark:border-zinc-800 p-5 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Total Anggaran RKAS</p>
                <p class="text-base font-extrabold text-blue-600 dark:text-blue-400">Rp {{ number_format($rkas->total_anggaran, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Verification Metadata Details -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200 dark:border-zinc-800 p-6 space-y-4 shadow-sm">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Rincian Sertifikat Pengesahan Digital</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-800 space-y-1">
                <span class="text-gray-400 font-semibold uppercase text-[10px]">Waktu Pengesahan:</span>
                <p class="font-bold text-gray-900 dark:text-white">
                    {{ $rkas->approved_at ? $rkas->approved_at->translatedFormat('d F Y, H:i:s') . ' WIB' : '-' }}
                </p>
            </div>

            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-800 space-y-1">
                <span class="text-gray-400 font-semibold uppercase text-[10px]">Lembaga / Sekolah:</span>
                <p class="font-bold text-gray-900 dark:text-white">SMP Negeri 40 Kota Bekasi</p>
            </div>

            <div class="md:col-span-2 p-3.5 rounded-xl bg-gray-50 dark:bg-zinc-800/60 border border-gray-100 dark:border-zinc-800 space-y-1">
                <span class="text-gray-400 font-semibold uppercase text-[10px]">Digital Signature Hash (SHA-256):</span>
                <p class="font-mono text-[11px] text-emerald-600 dark:text-emerald-400 break-all bg-emerald-50 dark:bg-emerald-950/40 p-2 rounded-lg border border-emerald-200 dark:border-emerald-900/40 font-bold">
                    {{ $digitalHash }}
                </p>
            </div>
        </div>
    </div>

    <!-- Approved Item Breakdown Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200 dark:border-zinc-800 p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Rincian Item Anggaran Disetujui ({{ $rkas->items->count() }} Item)</span>
            </h3>
        </div>

        <div class="w-full overflow-x-auto rounded-2xl border border-gray-100 dark:border-zinc-800">
            <table class="w-full text-left text-xs text-gray-600 dark:text-zinc-300">
                <thead class="bg-gray-50 dark:bg-zinc-800/80 text-[11px] font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider border-b border-gray-100 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-3">No. Rekening</th>
                        <th class="px-4 py-3">Uraian Belanja</th>
                        <th class="px-4 py-3">Komponen S / K</th>
                        <th class="px-4 py-3 text-right">Volume</th>
                        <th class="px-4 py-3 text-right">Harga Satuan</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                    @forelse($rkas->items as $item)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-3 font-mono font-bold text-blue-600 dark:text-blue-400">
                            {{ $item->kode_rekening }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                            {{ $item->uraian_belanja }}
                        </td>
                        <td class="px-4 py-3 text-[10px] text-gray-500">
                            <div>S: {{ $listKomponenS[$item->standar_komponen_s_setelah] ?? $item->standar_komponen_s_setelah }}</div>
                            <div>K: {{ $listKomponenK[$item->standar_komponen_k_setelah] ?? $item->standar_komponen_k_setelah }}</div>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            {{ (float)$item->volume_setelah == (int)$item->volume_setelah ? (int)$item->volume_setelah : number_format($item->volume_setelah, 2, ',', '.') }} {{ $item->satuan_setelah }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap font-medium">
                            Rp {{ number_format($item->harga_satuan_setelah, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($item->jumlah_setelah, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada rincian item.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-zinc-800/80 font-bold border-t border-gray-200 dark:border-zinc-800">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right uppercase text-[11px] text-gray-500">Total Anggaran Disahkan:</td>
                        <td class="px-4 py-3 text-right text-sm text-blue-600 dark:text-blue-400">
                            Rp {{ number_format($rkas->total_anggaran, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Official Stamp Seal Box -->
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/30 border border-emerald-200 dark:border-emerald-800 rounded-3xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shrink-0 border-2 border-white dark:border-zinc-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-base">Pengesahan Digital Kepala Sekolah</h4>
                <p class="text-xs text-gray-600 dark:text-zinc-400">
                    Dokumen ini telah disetujui dan ditandatangani secara elektronik dengan QR Code resmi oleh <strong>{{ $rkas->approver?->name ?? 'Kepala Sekolah' }}</strong>.
                </p>
            </div>
        </div>

        <div class="no-print">
            <a href="{{ route('rkas.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition-all flex items-center gap-2 whitespace-nowrap">
                <span>Buka di Aplikasi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l7-7m-7 7H3"/></svg>
            </a>
        </div>
    </div>
    @endif

</div>
