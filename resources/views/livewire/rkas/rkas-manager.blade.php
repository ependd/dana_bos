<div class="space-y-6">

    <!-- Flash Messages (Notifications & Warnings) -->
    @if(session('success'))
    <div x-data="{ show: true }"
         x-init="setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-start justify-between gap-3 shadow-xs">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <div>
                <h4 class="font-bold text-sm">Berhasil</h4>
                <p class="text-xs mt-0.5 leading-relaxed">{{ session('success') }}</p>
            </div>
        </div>
        <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:hover:text-emerald-200 text-xs cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    @if(session('warning'))
    <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 flex items-start gap-3 shadow-xs">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <h4 class="font-bold text-sm">Pemberitahuan Batas Dokumen RKAS</h4>
            <p class="text-xs mt-0.5 leading-relaxed">{{ session('warning') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-start gap-3 shadow-xs">
        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <div>
            <h4 class="font-bold text-sm">Kesalahan</h4>
            <p class="text-xs mt-0.5 leading-relaxed">{{ session('error') }}</p>
        </div>
    </div>
    @endif
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari Kode RKAS..." 
                   class="w-full sm:w-64 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 shadow-xs">
            
            <select wire:model.live="filterStatus" class="w-full sm:w-44 px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="submitted">Diajukan</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </select>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="w-full sm:w-auto flex items-center justify-end gap-3">
            @if(count($selectedRkas) > 0)
            <button type="button"
                    @click="confirmDelete('Hapus {{ count($selectedRkas) }} Dokumen RKAS', 'Apakah Anda yakin ingin menghapus {{ count($selectedRkas) }} dokumen RKAS terpilih?', () => $wire.deleteSelected())"
                    class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Terpilih ({{ count($selectedRkas) }})</span>
            </button>
            @endif

            <button wire:click="createRkas" 
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center cursor-pointer">
                <span>Tambah</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Data Table -->
    <x-card class="p-0 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-zinc-300">
                <thead class="bg-gray-50 dark:bg-zinc-800/60 text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider border-b border-gray-100 dark:border-zinc-800">
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <th class="px-4 py-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        @endif
                        <th class="px-6 py-4">Kode RKAS</th>
                        <th class="px-6 py-4">Periode</th>
                        <th class="px-6 py-4">Total Anggaran</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Disetujui Oleh</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/80">
                    @forelse($rkass as $r)
                    @php
                        $hasBkuData = $r->bku()->exists() || $r->items()->whereHas('bkuItems')->exists();
                    @endphp
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors {{ in_array((string)$r->id, $selectedRkas) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}">
                        @if(auth()->user()->isAdmin())
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" wire:model.live="selectedRkas" value="{{ $r->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>
                        @endif
                        <td class="px-6 py-4 font-semibold text-blue-600 dark:text-blue-400">
                            <button wire:click="toggleExpand({{ $r->id }})" class="hover:underline flex items-center gap-1.5 text-left cursor-pointer">
                                <svg class="w-4 h-4 text-gray-400 transition-transform {{ $expandedRkasId === $r->id ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                <span>{{ $r->kode_rkas }}</span>
                            </button>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            {{ $r->periodeBos?->tahun_ajaran }}
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white whitespace-nowrap">
                            Rp {{ number_format($r->total_anggaran, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :color="$r->status_badge_color">
                                {{ match($r->status) {
                                    'submitted' => 'Diajukan',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => ucfirst($r->status),
                                } }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $r->approver?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                @if(auth()->user()->isAdmin() && $r->isEditable())
                                <button wire:click="openSubmitModal({{ $r->id }})" title="{{ $r->status === 'rejected' ? 'Ajukan Ulang / Submit Revisi ke Kepala Sekolah' : 'Ajukan ke Kepala Sekolah' }}" class="px-3 py-1.5 text-blue-700 dark:text-blue-300 bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 rounded-lg transition-colors cursor-pointer border border-blue-200 dark:border-blue-800 inline-flex items-center gap-1.5 font-bold text-xs">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3 21l18-9L3 3l3 9zm0 0h7.5"/></svg>
                                    <span>{{ $r->status === 'rejected' ? 'Ajukan Ulang' : 'Ajukan' }}</span>
                                </button>
                                @endif

                                @if(auth()->user()->isKepalaSekolah() && $r->status === 'submitted')
                                <button wire:click="openApproveModal({{ $r->id }})" title="Setujui RKAS & Terbitkan QR Digital" class="px-3 py-1.5 text-emerald-700 dark:text-emerald-300 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 rounded-lg cursor-pointer border border-emerald-200 dark:border-emerald-800 transition-colors inline-flex items-center gap-1.5 font-bold text-xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Setujui</span>
                                </button>

                                <button wire:click="openRejectModal({{ $r->id }})" title="Tolak RKAS untuk Direvisi" class="px-3 py-1.5 text-rose-700 dark:text-rose-300 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 rounded-lg cursor-pointer border border-rose-200 dark:border-rose-800 transition-colors inline-flex items-center gap-1.5 font-bold text-xs">
                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>Tolak</span>
                                </button>
                                @endif

                                @if($r->status === 'approved')
                                <button wire:click="openQrModal({{ $r->id }})" title="Lihat QR Code & Sertifikat Pengesahan" class="px-3 py-1.5 text-emerald-700 dark:text-emerald-300 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/60 rounded-lg cursor-pointer border border-emerald-200 dark:border-emerald-800 transition-all inline-flex items-center gap-1.5 font-bold text-xs shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 14v1m8-8h-1M5 12H4m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-11.314l.707.707m11.314 11.314l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                                    </svg>
                                    <span>QR Digital</span>
                                </button>
                                @endif

                                @if(auth()->user()->isAdmin())
                                <button type="button" 
                                        @click="if ({{ $hasBkuData ? 'true' : 'false' }}) {
                                            Swal.fire({
                                                title: 'RKAS Terikat BKU',
                                                text: 'Dokumen RKAS \'{{ addslashes($r->kode_rkas) }}\' tidak dapat dihapus karena sudah digunakan dalam transaksi Buku Kas Umum (BKU).',
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
                                            confirmDelete('Hapus Dokumen RKAS', 'Apakah Anda yakin ingin menghapus dokumen RKAS {{ addslashes($r->kode_rkas) }}?', () => $wire.deleteRkas({{ $r->id }}));
                                        }" 
                                        title="Hapus Dokumen RKAS" 
                                        class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-zinc-800 rounded-lg cursor-pointer transition-colors">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                 </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Expandable Item Breakdown Row -->
                    @if($expandedRkasId === $r->id)
                    <tr class="bg-blue-50/30 dark:bg-zinc-900/80 border-b border-blue-100 dark:border-zinc-800">
                        <td colspan="6" class="px-6 py-4">
                            <div class="space-y-3">
                                
                                @if($r->status === 'rejected' && $r->catatan_penolakan)
                                <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs space-y-1 shadow-xs">
                                    <div class="flex items-center gap-2 font-bold text-rose-800 dark:text-rose-300">
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Catatan Penolakan dari Kepala Sekolah:</span>
                                    </div>
                                    <p class="text-rose-700 dark:text-rose-400 font-medium pl-6 text-xs leading-relaxed">{{ $r->catatan_penolakan }}</p>
                                    @if(auth()->user()->isAdmin())
                                    <p class="text-[11px] text-gray-500 dark:text-zinc-400 pl-6 pt-1">
                                        Silakan lakukan penyesuaian No. Rekening atau Komponen RKAS pada item di bawah ini, lalu klik tombol <strong>Ajukan Ulang</strong> untuk mengirimkan revisi ke Kepala Sekolah.
                                    </p>
                                    @endif
                                </div>
                                @endif

                                <div class="flex items-center justify-between">
                                    <h5 class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Rincian Item Belanja RKAS:</h5>
                                </div>

                                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 divide-y divide-gray-100 dark:divide-zinc-800 shadow-xs">
                                    @forelse($r->items as $item)
                                    @php
                                        $rawRek = trim($item->kode_rekening ?? '');
                                        $isValidRekening = !empty($rawRek) && !in_array(strtolower($rawRek), ['atk', 'tik', 'buku', 'sapras', 'pemeliharaan', 'kegiatan', 'honorarium', 'honor', 'bahan ajar', '-']) && preg_match('/^[0-9.]+$/', $rawRek);
                                    @endphp
                                    <div class="py-3 flex flex-col md:flex-row md:items-center justify-between text-xs gap-3">
                                        <div class="flex-1 min-w-0 space-y-1">
                                            <div class="flex items-center gap-2">
                                                @if($isValidRekening)
                                                <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold shrink-0">
                                                    Rek: {{ $item->kode_rekening }}
                                                </span>
                                                @else
                                                <span class="px-2 py-0.5 rounded text-[11px] font-mono bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-bold shrink-0 border border-rose-300 dark:border-rose-800">
                                                    Rek: Belum diisi
                                                </span>
                                                @endif
                                                <p class="font-semibold text-gray-900 dark:text-white truncate text-sm">{{ $item->uraian_belanja }}</p>
                                            </div>
                                            
                                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400 pt-0.5">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 dark:bg-zinc-800">
                                                    <strong class="text-gray-700 dark:text-zinc-300">Komponen S:</strong> {{ $listKomponenS[$item->standar_komponen_s_setelah] ?? 'Standar ' . $item->standar_komponen_s_setelah }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 dark:bg-zinc-800">
                                                    <strong class="text-gray-700 dark:text-zinc-300">Komponen K:</strong> {{ $listKomponenK[$item->standar_komponen_k_setelah] ?? 'Komponen ' . $item->standar_komponen_k_setelah }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center justify-between md:justify-end gap-4 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-gray-100 dark:border-zinc-800">
                                            <div class="text-right">
                                                <p class="font-medium text-gray-700 dark:text-zinc-300">{{ (float)$item->volume_setelah == (int)$item->volume_setelah ? (int)$item->volume_setelah : number_format($item->volume_setelah, 2, ',', '.') }} {{ $item->satuan_setelah }} × Rp {{ number_format($item->harga_satuan_setelah, 0, ',', '.') }}</p>
                                                <p class="font-bold text-blue-600 dark:text-blue-400 text-sm">Subtotal: Rp {{ number_format($item->jumlah_setelah, 0, ',', '.') }}</p>
                                            </div>

                                            @if(auth()->user()->isAdmin() && $r->isEditable())
                                            <div class="border-l border-gray-200 dark:border-zinc-800 pl-3">
                                                <button wire:click="editItem({{ $item->id }})" title="Edit No. Rekening & Komponen RKAS" class="p-2 text-amber-600 hover:bg-amber-50 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer flex items-center gap-1 font-semibold text-xs border border-amber-200 dark:border-amber-900/60">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Edit RKAS</span>
                                                </button>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-xs text-gray-400 py-3 text-center">Belum ada item belanja dalam RKAS ini.</p>
                                    @endforelse
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif

                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-zinc-400 text-sm">
                            Belum ada dokumen RKAS.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rkass->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            {{ $rkass->links() }}
        </div>
        @endif
    </x-card>

    <!-- Modal Konfirmasi Ajukan RKAS ke Kepala Sekolah -->
    <x-modal name="confirm-submit-rkas-modal" title="Pengajuan RKAS ke Kepala Sekolah" maxWidth="md">
        <div class="text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3 21l18-9L3 3l3 9zm0 0h7.5"/>
                </svg>
            </div>

            <div class="space-y-2">
                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                    Ajukan Dokumen RKAS?
                </h4>
                <p class="text-xs text-gray-600 dark:text-zinc-400 leading-relaxed">
                    Dokumen <strong>{{ $submittingRkasKode }}</strong> akan diajukan ke <strong>Kepala Sekolah</strong> untuk diperiksa dan disetujui.
                </p>
                <p class="text-xs text-blue-700 dark:text-blue-300 font-medium leading-relaxed bg-blue-50 dark:bg-blue-950/40 p-3 rounded-xl border border-blue-200 dark:border-blue-800">
                    Setelah diajukan, status dokumen RKAS akan berubah menjadi <strong>Diajukan</strong> dan tidak dapat diedit lagi hingga mendapat tanggapan dari Kepala Sekolah.
                </p>
            </div>
        </div>

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'confirm-submit-rkas-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold transition-all cursor-pointer">
                Batal
            </button>
            <button type="button" wire:click="confirmSubmitRkas" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white text-sm font-semibold shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12L3 21l18-9L3 3l3 9zm0 0h7.5"/></svg>
                <span>Ya, Ajukan Sekarang</span>
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Konfirmasi Hapus RKAS -->
    <x-modal name="confirm-delete-rkas-modal" title="Konfirmasi Hapus RKAS" maxWidth="md">
        <div class="text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <div class="space-y-2">
                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                    Hapus Dokumen RKAS?
                </h4>
                <p class="text-xs text-gray-600 dark:text-zinc-400 leading-relaxed">
                    Apakah Anda yakin ingin menghapus dokumen <strong>{{ $deletingRkasKode }}</strong>?
                </p>
                <p class="text-xs text-rose-700 dark:text-rose-300 font-medium leading-relaxed bg-rose-50 dark:bg-rose-950/40 p-3 rounded-xl border border-rose-200 dark:border-rose-800">
                    Tindakan ini akan menghapus dokumen RKAS beserta rincian item belanjanya. Item pengajuan kebutuhan guru akan otomatis dilepas dari kuncian.
                </p>
            </div>
        </div>

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'confirm-delete-rkas-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold transition-all cursor-pointer">
                Batal
            </button>
            <button type="button" wire:click="confirmDeleteRkas" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white text-sm font-semibold shadow-md shadow-rose-500/20 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Ya, Hapus Dokumen</span>
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Konfirmasi Ganti / Hapus Draf RKAS Lama saat Buat Baru -->
    <x-modal name="confirm-create-rkas-modal" title="Konfirmasi Buat Draf RKAS Baru" maxWidth="md">
        <div class="text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <div class="space-y-2">
                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                    Dokumen RKAS Sudah Ada
                </h4>
                <p class="text-xs text-gray-600 dark:text-zinc-400 leading-relaxed">
                    Dokumen RKAS untuk Periode BOS <strong>{{ $existingRkasPeriode }}</strong> sudah tersedia (<strong>{{ $existingRkasKode }}</strong> - Status: <strong>{{ $existingRkasStatus }}</strong>).
                </p>
                <p class="text-xs text-amber-700 dark:text-amber-400 font-medium leading-relaxed bg-amber-50 dark:bg-amber-950/40 p-3 rounded-xl border border-amber-200 dark:border-amber-800">
                    Membuat draf baru akan <strong>menghapus dokumen RKAS lama beserta rincian itemnya secara otomatis</strong> dan menggantikannya dengan draf RKAS baru.
                </p>
            </div>
        </div>

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'confirm-create-rkas-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold transition-all cursor-pointer">
                Batal
            </button>
            <button type="button" wire:click="confirmCreateNewRkas" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white text-sm font-semibold shadow-md shadow-amber-500/20 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Ya, Hapus & Buat Draf Baru</span>
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Edit No. Rekening & Komponen RKAS (Terbatas untuk Admin) -->
    <x-modal name="item-modal" title="Edit No. Rekening & Komponen RKAS" maxWidth="lg">
        <form wire:submit.prevent="saveItem" class="space-y-4">
            
            <!-- Readonly Overview Barang -->
            <div class="p-3.5 rounded-2xl bg-gray-50 dark:bg-zinc-800/80 border border-gray-100 dark:border-zinc-700 space-y-1">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Item Barang Belanja:</p>
                <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $uraian_belanja }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 font-medium pt-1">
                    Vol: <strong>{{ (float)$volume == (int)$volume ? (int)$volume : $volume }} {{ $satuan }}</strong> × Rp {{ number_format($harga_satuan, 0, ',', '.') }} = <strong>Rp {{ number_format($volume * $harga_satuan, 0, ',', '.') }}</strong>
                </p>
            </div>

            <!-- Field 1: No. Rekening (Hanya Angka dan Titik) -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    No. Rekening (Hanya Angka dan Titik) <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       wire:model="kode_rekening" 
                       oninput="this.value = this.value.replace(/[^0-9.]/g, '')"
                       placeholder="misal: 5.1.02.01.01.0025" 
                       required
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm font-mono focus:ring-2 focus:ring-blue-500 @error('kode_rekening') border-rose-500 focus:ring-rose-500 @enderror">
                <p class="text-[11px] text-gray-400 mt-1">Wajib diisi dengan digit angka dan karakter titik (misal: 5.1.02.01.01.0025).</p>
                @error('kode_rekening') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Field 2: Standar Komponen S (X2, 1-8) -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Standar Komponen S (SNP)
                </label>
                <select wire:model="standar_komponen_s" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach($listKomponenS as $key => $val)
                    <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
                @error('standar_komponen_s') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Field 3: Komponen K (X3, 1-9 & 12) -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                    Komponen K (Program BOS)
                </label>
                <select wire:model="standar_komponen_k" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach($listKomponenK as $key => $val)
                    <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
                @error('standar_komponen_k') <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </form>

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'item-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold cursor-pointer">
                Batal
            </button>
            <button type="button" wire:click="saveItem" wire:loading.attr="disabled" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md transition-all cursor-pointer">
                Simpan Perubahan RKAS
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Konfirmasi Setujui RKAS oleh Kepala Sekolah -->
    <x-modal name="confirm-approve-rkas-modal" title="Pengesahan Digital RKAS oleh Kepala Sekolah" maxWidth="md">
        <div class="text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto shadow-inner border border-emerald-200 dark:border-emerald-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>

            <div class="space-y-2">
                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                    Setujui & Sahkan RKAS Secara Digital?
                </h4>
                <p class="text-xs text-gray-600 dark:text-zinc-400 leading-relaxed">
                    Dokumen <strong>{{ $approvingRkasKode }}</strong> akan disetujui secara resmi oleh <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->isKepalaSekolah() ? 'Kepala Sekolah' : 'Administrator' }}).
                </p>
                <p class="text-xs text-emerald-800 dark:text-emerald-300 font-medium leading-relaxed bg-emerald-50 dark:bg-emerald-950/40 p-3 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    <svg class="w-4 h-4 inline-block -mt-0.5 text-emerald-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 14v1m8-8h-1M5 12H4m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-11.314l.707.707m11.314 11.314l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                    Sistem akan menerbitkan <strong>QR Code Tanda Tangan Digital</strong> resmi yang menyimpan URL verifikasi keabsahan dokumen RKAS ini saat di-scan.
                </p>
            </div>
        </div>

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'confirm-approve-rkas-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold transition-all cursor-pointer">
                Batal
            </button>
            <button type="button" wire:click="confirmApproveRkas" wire:loading.attr="disabled" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-sm font-semibold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Setujui & Terbitkan QR Code</span>
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Tampilkan QR Code Pengesahan Digital -->
    <x-modal name="view-qr-modal" title="Sertifikat Pengesahan & QR Code Digital" maxWidth="lg">
        @if($viewingRkas)
        <div class="space-y-6 text-center">
            
            <!-- Header Status Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>DOKUMEN TERVERIFIKASI & DISAHKAN</span>
            </div>

            <!-- QR Code Display Card -->
            <div class="p-6 rounded-3xl bg-gray-50 dark:bg-zinc-800/80 border border-gray-200 dark:border-zinc-700 inline-block mx-auto shadow-inner relative group">
                <div class="w-64 h-64 mx-auto flex items-center justify-center p-2 bg-white rounded-2xl shadow-md border border-gray-200">
                    {!! $qrCodeSvg !!}
                </div>
                <p class="text-[11px] text-gray-500 dark:text-zinc-400 mt-3 font-mono">
                    Scan QR Code di atas menggunakan kamera smartphone untuk memverifikasi dokumen RKAS
                </p>
            </div>

            <!-- Metadata Info Breakdown -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-gray-200 dark:border-zinc-800 p-4 text-left divide-y divide-gray-100 dark:divide-zinc-800 text-xs space-y-2">
                <div class="pb-2 flex justify-between items-center">
                    <span class="text-gray-500">Kode RKAS:</span>
                    <span class="font-bold font-mono text-blue-600 dark:text-blue-400 text-sm">{{ $viewingRkas->kode_rkas }}</span>
                </div>
                <div class="py-2 flex justify-between items-center">
                    <span class="text-gray-500">Periode BOS:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $viewingRkas->periodeBos?->tahun_ajaran }}</span>
                </div>
                <div class="py-2 flex justify-between items-center">
                    <span class="text-gray-500">Total Anggaran Disetujui:</span>
                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm">Rp {{ number_format($viewingRkas->total_anggaran, 0, ',', '.') }}</span>
                </div>
                <div class="py-2 flex justify-between items-center">
                    <span class="text-gray-500">Disetujui Oleh:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $viewingRkas->approver?->name ?? 'Kepala Sekolah' }}</span>
                </div>
                <div class="pt-2 flex justify-between items-center">
                    <span class="text-gray-500">Waktu Persetujuan:</span>
                    <span class="font-semibold text-gray-700 dark:text-zinc-300">{{ $viewingRkas->approved_at?->format('d-m-Y H:i:s') }} WIB</span>
                </div>
            </div>

            <!-- Public Verification Link Button -->
            <div>
                <a href="{{ $qrVerifyUrl }}" target="_blank" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all">
                    <span>Buka Halaman Verifikasi (Hasil Scan QR)</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>

        </div>
        @endif

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'view-qr-modal')" class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-xs font-bold transition-all cursor-pointer w-full sm:w-auto">
                Tutup
            </button>
        </x-slot:footer>
    </x-modal>

    <!-- Modal Konfirmasi Tolak RKAS oleh Kepala Sekolah -->
    <x-modal name="confirm-reject-rkas-modal" title="Penolakan Dokumen RKAS oleh Kepala Sekolah" maxWidth="md">
        <form wire:submit.prevent="confirmRejectRkas" class="space-y-4">
            <div class="text-center space-y-2">
                <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto shadow-inner border border-rose-200 dark:border-rose-800">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 dark:text-white">
                    Tolak Dokumen {{ $rejectingRkasKode }}?
                </h4>
                <p class="text-xs text-gray-600 dark:text-zinc-400 leading-relaxed">
                    Dokumen RKAS akan dikembalikan ke Administrator untuk dilakukan penyesuaian/revisi sesuai catatan penolakan di bawah.
                </p>
            </div>

            <!-- Select Alasan Penolakan -->
            <div class="text-left space-y-1">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-zinc-300">
                    Pilih Alasan Penolakan:
                </label>
                <select wire:model="catatan_penolakan_dropdown" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-rose-500 shadow-xs">
                    @foreach($rejectionOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <x-slot:footer>
            <button type="button" @click="$dispatch('close-modal', 'confirm-reject-rkas-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold transition-all cursor-pointer">
                Batal
            </button>
            <button type="button" wire:click="confirmRejectRkas" wire:loading.attr="disabled" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white text-sm font-semibold shadow-md shadow-rose-500/20 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Tolak Dokumen RKAS</span>
            </button>
        </x-slot:footer>
    </x-modal>

</div>
