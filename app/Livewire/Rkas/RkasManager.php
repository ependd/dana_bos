<?php

namespace App\Livewire\Rkas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rkas;
use App\Models\RkasItem;
use App\Models\PeriodeBos;
use App\Models\KatalogBarang;
use App\Models\Kebutuhan;
use Illuminate\Support\Facades\DB;

class RkasManager extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $filterPeriodeId = null;
    public ?int $expandedRkasId = null;

    // Bulk selection fields
    public array $selectedRkas = [];
    public bool $selectAll = false;

    // Form fields
    public bool $isEdit = false;
    public ?int $rkasId = null;
    public ?int $periode_bos_id = null;
    public string $kode_rkas = '';
    public string $nama_program = '';
    public $total_anggaran = null;
    public string $catatan = '';

    // RKAS Item detail form fields
    public bool $showItemModal = false;
    public ?int $activeRkasId = null;
    public ?int $rkasItemId = null;
    public ?int $katalog_barang_id = null;
    public string $nama_kegiatan = '';
    public string $kategori = '';
    public int $volume = 1;
    public string $satuan = 'Pcs';
    public $harga_satuan = null;
    public int $prioritas = 3;

    // Item edit modal fields
    public string $uraian_belanja = '';
    public string $kode_rekening = '';
    public string $standar_komponen_s = '';
    public string $standar_komponen_k = '';
    public array $listKomponenS = [];
    public array $listKomponenK = [];

    // Delete confirmation modal state
    public ?int $deletingRkasId = null;
    public string $deletingRkasKode = '';

    // Submit modal state
    public ?int $submittingRkasId = null;
    public string $submittingRkasKode = '';

    // Existing RKAS modal state
    public string $existingRkasPeriode = '';
    public string $existingRkasKode = '';
    public string $existingRkasStatus = '';

    // Approve modal state
    public ?int $approvingRkasId = null;
    public string $approvingRkasKode = '';

    // Reject modal state
    public ?int $rejectingRkasId = null;
    public string $rejectingRkasKode = '';
    public string $catatan_penolakan_dropdown = 'Revisi Rincian Belanja';
    public array $rejectionOptions = [
        'Revisi Rincian Belanja',
        'Anggaran Melebihi Pagu',
        'Perlu Penyesuaian Komponen',
        'Lainnya'
    ];

    // QR Modal state
    public ?Rkas $viewingRkas = null;
    public string $qrCodeSvg = '';
    public string $qrVerifyUrl = '';

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedRkas = Rkas::query()
                ->when($this->search, fn ($q) => $q->where('kode_rkas', 'like', "%{$this->search}%")->orWhere('nama_program', 'like', "%{$this->search}%"))
                ->when($this->filterPeriodeId, fn ($q) => $q->where('periode_bos_id', $this->filterPeriodeId))
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedRkas = [];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedRkas = [];
        $this->selectAll = false;
    }

    public function updatingFilterPeriodeId()
    {
        $this->resetPage();
        $this->selectedRkas = [];
        $this->selectAll = false;
    }

    public function mount()
    {
        $activePeriode = PeriodeBos::where('is_active', true)->first();
        if ($activePeriode) {
            $this->filterPeriodeId = $activePeriode->id;
            $this->periode_bos_id = $activePeriode->id;
        }

        $this->listKomponenS = [
            '1' => 'Standar Isi (1)',
            '2' => 'Standar Proses (2)',
            '3' => 'Standar Kelulusan (3)',
            '4' => 'Standar Pendidik & Tenaga Kependidikan (4)',
            '5' => 'Standar Sarana & Prasarana (5)',
            '6' => 'Standar Pengelolaan (6)',
            '7' => 'Standar Pembiayaan (7)',
            '8' => 'Standar Penilaian Pendidikan (8)',
        ];

        $this->listKomponenK = [
            '1' => 'Penerimaan Peserta Didik Baru (1)',
            '2' => 'Pengembangan Perpustakaan (2)',
            '3' => 'Kegiatan Pembelajaran & Ekstrakurikuler (3)',
            '4' => 'Kegiatan Asesmen / Evaluasi Pembelajaran (4)',
            '5' => 'Pelaksanaan Administrasi Kegiatan Sekolah (5)',
            '6' => 'Pengembangan Profesi Guru & Tenaga Kependidikan (6)',
            '7' => 'Pemeliharaan Sarana & Prasarana Sekolah (7)',
            '8' => 'Penyediaan Alat Multi Media Pembelajaran (8)',
            '9' => 'Penyelenggaraan Kegiatan Kesehatan / Kebersihan (9)',
            '12' => 'Pembayaran Honor (12)',
        ];
    }

    public function createRkas()
    {
        if (!auth()->user()->isAdmin()) return;

        $activePeriode = PeriodeBos::where('is_active', true)->first();

        if (!$activePeriode) {
            $this->dispatch('swal:error', [
                'title' => 'Belum Ada Periode BOS Aktif',
                'text' => 'Silakan buat atau aktifkan salah satu Periode BOS terlebih dahulu sebelum membuat dokumen RKAS.'
            ]);
            session()->flash('error', 'Silakan buat atau aktifkan salah satu Periode BOS terlebih dahulu sebelum membuat dokumen RKAS.');
            return;
        }

        // Check if there are Pengajuan Kebutuhan in this active period
        $hasKebutuhan = Kebutuhan::where('periode_bos_id', $activePeriode->id)->exists();
        if (!$hasKebutuhan) {
            $this->dispatch('swal:error', [
                'title' => 'Belum Ada Pengajuan Kebutuhan',
                'text' => "Periode BOS {$activePeriode->tahun_ajaran} belum memiliki data pengajuan kebutuhan dari Guru. Dokumen RKAS tidak dapat dibuat karena belum ada item kebutuhan yang diajukan."
            ]);
            session()->flash('error', "Belum ada data pengajuan kebutuhan dari Guru untuk Periode BOS {$activePeriode->tahun_ajaran}. Dokumen RKAS tidak dapat dibuat.");
            return;
        }

        // Check if RKAS already exists for active period
        $existing = Rkas::where('periode_bos_id', $activePeriode->id)->first();
        if ($existing) {
            $this->existingRkasPeriode = $activePeriode->tahun_ajaran;
            $this->existingRkasKode = $existing->kode_rkas;
            $this->existingRkasStatus = match($existing->status) {
                'submitted' => 'Diajukan',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default => ucfirst($existing->status),
            };
            $this->dispatch('open-modal', 'confirm-create-rkas-modal');
            return;
        }

        // Generate RKAS automatically from Kebutuhan
        try {
            $service = new \App\Services\RkasService();
            $rkas = $service->generateFromKebutuhan($activePeriode);
            session()->flash('success', "Dokumen RKAS {$rkas->kode_rkas} berhasil dibuat otomatis dari pengajuan kebutuhan!");
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal Membuat RKAS',
                'text' => $e->getMessage()
            ]);
            session()->flash('error', $e->getMessage());
        }
    }

    public function openCreateModal()
    {
        $activePeriode = PeriodeBos::where('is_active', true)->first();

        if (!$activePeriode) {
            $this->dispatch('swal:error', [
                'title' => 'Belum Ada Periode BOS Aktif',
                'text' => 'Silakan buat atau aktifkan salah satu Periode BOS terlebih dahulu.'
            ]);
            session()->flash('error', 'Silakan buat atau aktifkan salah satu Periode BOS terlebih dahulu.');
            return;
        }

        $this->reset(['rkasId', 'kode_rkas', 'nama_program', 'total_anggaran', 'catatan', 'isEdit']);
        $this->kode_rkas = 'RKAS-' . date('Ym') . '-' . rand(100, 999);
        $this->periode_bos_id = $activePeriode->id;
        $this->resetValidation();
        $this->dispatch('open-modal', 'rkas-modal');
    }

    public function openEditModal(int $id)
    {
        $rkas = Rkas::findOrFail($id);
        $this->rkasId = $rkas->id;
        $this->periode_bos_id = $rkas->periode_bos_id;
        $this->kode_rkas = $rkas->kode_rkas;
        $this->nama_program = $rkas->nama_program;
        $this->total_anggaran = $rkas->total_anggaran;
        $this->catatan = $rkas->catatan ?? '';
        $this->isEdit = true;
        $this->resetValidation();
        $this->dispatch('open-modal', 'rkas-modal');
    }

    public function saveRkas()
    {
        if (!auth()->user()->isAdmin()) return;

        if ($this->total_anggaran !== null) {
            $this->total_anggaran = abs((int)preg_replace('/[^0-9]/', '', (string)$this->total_anggaran));
        }

        $this->validate([
            'periode_bos_id' => 'required|exists:periode_bos,id',
            'kode_rkas' => 'required|string|max:50|unique:rkas,kode_rkas,' . ($this->rkasId ?? 'NULL') . ',id',
            'nama_program' => 'required|string|max:255',
            'total_anggaran' => 'required|numeric|min:1',
            'catatan' => 'nullable|string',
        ]);

        Rkas::updateOrCreate(
            ['id' => $this->rkasId],
            [
                'periode_bos_id' => $this->periode_bos_id,
                'kode_rkas' => $this->kode_rkas,
                'nama_program' => $this->nama_program,
                'total_anggaran' => $this->total_anggaran,
                'catatan' => $this->catatan,
                'created_by' => auth()->id(),
            ]
        );

        $this->dispatch('close-modal', 'rkas-modal');
        session()->flash('success', $this->isEdit ? 'Dokumen RKAS berhasil diperbarui!' : 'Dokumen RKAS baru berhasil ditambahkan!');
    }

    public function openSubmitModal(int $id)
    {
        $rkas = Rkas::with('items')->findOrFail($id);

        $unfilledItems = $rkas->items->filter(function ($item) {
            $raw = trim($item->kode_rekening ?? '');
            return empty($raw) || in_array(strtolower($raw), ['atk', 'tik', 'buku', 'sapras', 'pemeliharaan', 'kegiatan', 'honorarium', 'honor', 'bahan ajar', '-']) || !preg_match('/^[0-9.]+$/', $raw);
        });

        if ($unfilledItems->count() > 0) {
            $count = $unfilledItems->count();
            $sampleNames = $unfilledItems->take(3)->pluck('uraian_belanja')->map(fn($n) => "'{$n}'")->implode(', ');
            if ($count > 3) $sampleNames .= ', dan item lainnya';

            $this->dispatch('swal:error', [
                'title' => 'No. Rekening Belum Lengkap',
                'text' => "Dokumen RKAS {$rkas->kode_rkas} tidak dapat diajukan karena terdapat {$count} item belanja ({$sampleNames}) yang belum diisi No. Rekening. Silakan klik tombol 'Edit RKAS' pada rincian item di bawah untuk melengkapinya terlebih dahulu."
            ]);
            session()->flash('error', "Dokumen RKAS {$rkas->kode_rkas} tidak dapat diajukan karena terdapat {$count} item yang belum diisi No. Rekening.");
            return;
        }

        $this->submittingRkasId = $rkas->id;
        $this->submittingRkasKode = $rkas->kode_rkas;
        $this->dispatch('open-modal', 'confirm-submit-rkas-modal');
    }

    public function confirmSubmitRkas()
    {
        if ($this->submittingRkasId) {
            $rkas = Rkas::with('items')->findOrFail($this->submittingRkasId);

            $unfilledItems = $rkas->items->filter(function ($item) {
                $raw = trim($item->kode_rekening ?? '');
                return empty($raw) || in_array(strtolower($raw), ['atk', 'tik', 'buku', 'sapras', 'pemeliharaan', 'kegiatan', 'honorarium', 'honor', 'bahan ajar', '-']) || !preg_match('/^[0-9.]+$/', $raw);
            });

            if ($unfilledItems->count() > 0) {
                $this->dispatch('close-modal', 'confirm-submit-rkas-modal');
                $this->dispatch('swal:error', [
                    'title' => 'No. Rekening Belum Lengkap',
                    'text' => "Pengajuan dibatalkan. Masih terdapat {$unfilledItems->count()} item belanja yang belum memiliki No. Rekening."
                ]);
                session()->flash('error', "Dokumen RKAS {$rkas->kode_rkas} tidak dapat diajukan karena No. Rekening belum lengkap.");
                return;
            }

            $rkas->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
            $this->dispatch('close-modal', 'confirm-submit-rkas-modal');
            session()->flash('success', "Dokumen RKAS {$rkas->kode_rkas} berhasil diajukan ke Kepala Sekolah!");
        }
    }

    public function openApproveModal(int $id)
    {
        $rkas = Rkas::findOrFail($id);
        $this->approvingRkasId = $rkas->id;
        $this->approvingRkasKode = $rkas->kode_rkas;
        $this->dispatch('open-modal', 'confirm-approve-rkas-modal');
    }

    public function confirmApproveRkas()
    {
        if ($this->approvingRkasId) {
            $rkas = Rkas::findOrFail($this->approvingRkasId);
            $rkas->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            try {
                $service = new \App\Services\RkasService();
                $service->generateQrCode($rkas);
            } catch (\Throwable $e) {}

            $this->dispatch('close-modal', 'confirm-approve-rkas-modal');
            session()->flash('success', "Dokumen RKAS {$rkas->kode_rkas} berhasil disetujui & disahkan!");
        }
    }

    public function openRejectModal(int $id)
    {
        $rkas = Rkas::findOrFail($id);
        $this->rejectingRkasId = $rkas->id;
        $this->rejectingRkasKode = $rkas->kode_rkas;
        $this->catatan_penolakan_dropdown = 'Revisi Rincian Belanja';
        $this->dispatch('open-modal', 'confirm-reject-rkas-modal');
    }

    public function confirmRejectRkas()
    {
        if ($this->rejectingRkasId) {
            $rkas = Rkas::findOrFail($this->rejectingRkasId);
            $rkas->update([
                'status' => 'rejected',
                'catatan_penolakan' => $this->catatan_penolakan_dropdown,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            $this->dispatch('close-modal', 'confirm-reject-rkas-modal');
            session()->flash('warning', "Dokumen RKAS {$rkas->kode_rkas} telah ditolak dengan alasan: {$this->catatan_penolakan_dropdown}");
        }
    }

    public function openQrModal(int $id)
    {
        $rkas = Rkas::with(['periodeBos', 'approver'])->findOrFail($id);
        $this->viewingRkas = $rkas;
        $this->qrVerifyUrl = route('rkas.verify', ['kode' => $rkas->kode_rkas]);
        $encodedUrl = urlencode($this->qrVerifyUrl);
        $this->qrCodeSvg = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . $encodedUrl . '" class="w-56 h-56 mx-auto rounded-xl shadow-xs" alt="QR Code Pengesahan RKAS">';
        $this->dispatch('open-modal', 'view-qr-modal');
    }

    public function confirmCreateNewRkas()
    {
        $activePeriode = PeriodeBos::where('is_active', true)->first();
        if ($activePeriode) {
            try {
                $service = new \App\Services\RkasService();
                $rkas = $service->generateFromKebutuhan($activePeriode);
                $this->dispatch('close-modal', 'confirm-create-rkas-modal');
                session()->flash('success', "Dokumen draf RKAS {$rkas->kode_rkas} berhasil diperbarui dari pengajuan kebutuhan!");
            } catch (\Exception $e) {
                $this->dispatch('close-modal', 'confirm-create-rkas-modal');
                $this->dispatch('swal:error', [
                    'title' => 'Gagal Membuat RKAS',
                    'text' => $e->getMessage()
                ]);
                session()->flash('error', $e->getMessage());
            }
        }
    }

    public function updatedKatalogBarangId($value)
    {
        if ($value) {
            $barang = KatalogBarang::find($value);
            if ($barang) {
                $this->nama_kegiatan = $barang->nama_barang;
                $this->kategori = $barang->kategori;
                $this->satuan = $barang->satuan;
                $this->harga_satuan = $barang->harga_referensi;
            }
        }
    }

    public function manageItems(int $rkasId)
    {
        $this->activeRkasId = $rkasId;
        $this->resetItemForm();
        $this->dispatch('open-modal', 'rkas-items-modal');
    }

    public function resetItemForm()
    {
        $this->reset(['rkasItemId', 'katalog_barang_id', 'nama_kegiatan', 'kategori', 'harga_satuan']);
        $this->volume = 1;
        $this->satuan = 'Pcs';
        $this->prioritas = 3;
        $this->resetValidation();
    }

    public function saveItem()
    {
        if (!auth()->user()->isAdmin()) return;

        // If editing an existing item from RKAS detail (item-modal)
        if ($this->rkasItemId) {
            $this->validate([
                'kode_rekening' => ['required', 'string', 'max:50', 'regex:/^[0-9.]+$/'],
                'standar_komponen_s' => 'required',
                'standar_komponen_k' => 'required',
            ], [
                'kode_rekening.required' => 'No. Rekening wajib diisi.',
                'kode_rekening.regex' => 'No. Rekening hanya boleh berupa digit angka dan titik (misal: 5.1.02.01.01.0025).',
                'standar_komponen_s.required' => 'Standar Komponen S (SNP) wajib dipilih.',
                'standar_komponen_k.required' => 'Komponen K (Program BOS) wajib dipilih.',
            ]);

            $item = RkasItem::findOrFail($this->rkasItemId);
            $item->update([
                'kode_rekening' => $this->kode_rekening,
                'standar_komponen_s_setelah' => $this->standar_komponen_s,
                'standar_komponen_k_setelah' => $this->standar_komponen_k,
            ]);

            $this->dispatch('close-modal', 'item-modal');
            session()->flash('success', 'Rincian No. Rekening dan Komponen RKAS berhasil disimpan!');
            return;
        }

        if (!$this->activeRkasId) return;

        if ($this->harga_satuan !== null) {
            $this->harga_satuan = abs((int)preg_replace('/[^0-9]/', '', (string)$this->harga_satuan));
        }

        if ($this->volume !== null) {
            $this->volume = abs((int)$this->volume);
        }

        $this->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'volume' => 'required|integer|min:1',
            'satuan' => 'required|string|max:50',
            'harga_satuan' => 'required|numeric|min:1',
            'prioritas' => 'required|integer|between:1,5',
        ]);

        $totalHarga = $this->volume * $this->harga_satuan;

        RkasItem::create([
            'rkas_id' => $this->activeRkasId,
            'katalog_barang_id' => $this->katalog_barang_id,
            'nama_kegiatan' => $this->nama_kegiatan,
            'kategori' => $this->kategori,
            'volume' => $this->volume,
            'satuan' => $this->satuan,
            'harga_satuan' => $this->harga_satuan,
            'total_harga' => $totalHarga,
            'prioritas' => $this->prioritas,
        ]);

        $this->resetItemForm();
        session()->flash('item_success', 'Item RKAS berhasil disimpan!');
    }

    public function editItem(int $itemId)
    {
        $item = RkasItem::findOrFail($itemId);
        $this->rkasItemId = $item->id;
        $this->uraian_belanja = $item->nama_kegiatan ?? $item->uraian_belanja ?? 'Item Belanja';
        $this->volume = (int) ($item->volume_setelah ?? $item->volume ?? 1);
        $this->satuan = $item->satuan_setelah ?? $item->satuan ?? 'Pcs';
        $this->harga_satuan = $item->harga_satuan_setelah ?? $item->harga_satuan ?? 0;

        $rawKode = trim($item->kode_rekening ?? '');
        $this->kode_rekening = (in_array(strtolower($rawKode), ['atk', 'tik', 'buku', 'sapras', 'pemeliharaan', 'kegiatan', 'honorarium', 'honor', 'bahan ajar', '-']) || !preg_match('/^[0-9.]+$/', $rawKode)) ? '' : $rawKode;

        $this->standar_komponen_s = (string) ($item->standar_komponen_s_setelah ?? $item->standar_komponen_s ?? '1');
        $this->standar_komponen_k = (string) ($item->standar_komponen_k_setelah ?? $item->standar_komponen_k ?? '1');

        $this->resetValidation();
        $this->dispatch('open-modal', 'item-modal');
    }

    public function deleteItem(int $itemId)
    {
        if (!auth()->user()->isAdmin()) return;

        $item = RkasItem::findOrFail($itemId);
        
        if ($item->bkuItems()->exists()) {
            $this->dispatch('swal:error', [
                'title' => 'Item Terikat Transaksi BKU',
                'text' => "Item '{$item->nama_kegiatan}' tidak dapat dihapus karena telah digunakan dalam transaksi Buku Kas Umum (BKU)."
            ]);
            session()->flash('item_error', "Item '{$item->nama_kegiatan}' tidak dapat dihapus karena terikat transaksi BKU.");
            return;
        }

        Kebutuhan::where('rkas_item_id', $itemId)->update(['rkas_item_id' => null]);
        $item->delete();
        session()->flash('item_success', 'Item RKAS berhasil dihapus!');
    }

    public function approveRkas(int $id)
    {
        if (!auth()->user()->isKepalaSekolah() && !auth()->user()->isAdmin()) return;

        $rkas = Rkas::findOrFail($id);
        $rkas->update([
            'status' => 'DISETUJUI',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', "Dokumen RKAS {$rkas->kode_rkas} berhasil disetujui!");
    }

    public function rejectRkas(int $id)
    {
        if (!auth()->user()->isKepalaSekolah() && !auth()->user()->isAdmin()) return;

        $rkas = Rkas::findOrFail($id);
        $rkas->update([
            'status' => 'DITOLAK',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', "Dokumen RKAS {$rkas->kode_rkas} telah ditolak.");
    }

    public function deleteRkas(int $id)
    {
        if (!auth()->user()->isAdmin()) return;

        $rkas = Rkas::findOrFail($id);
        
        if ($rkas->bku()->exists() || $rkas->items()->whereHas('bkuItems')->exists()) {
            $this->dispatch('swal:error', [
                'title' => 'RKAS Terikat Transaksi BKU',
                'text' => "Dokumen RKAS {$rkas->kode_rkas} tidak dapat dihapus karena sudah memiliki transaksi terikat di Buku Kas Umum (BKU)."
            ]);
            session()->flash('error', "Dokumen RKAS {$rkas->kode_rkas} tidak dapat dihapus karena sudah memiliki transaksi BKU.");
            return;
        }

        $itemIds = $rkas->items()->pluck('id');
        Kebutuhan::whereIn('rkas_item_id', $itemIds)->update(['rkas_item_id' => null]);

        $rkas->delete();

        $this->selectedRkas = array_diff($this->selectedRkas, [(string)$id]);
        session()->flash('success', 'Dokumen RKAS berhasil dihapus!');
    }

    public function deleteSelected()
    {
        if (!auth()->user()->isAdmin() || empty($this->selectedRkas)) return;

        $rkass = Rkas::whereIn('id', $this->selectedRkas)->get();
        $cannotDelete = [];
        $deletedCount = 0;

        foreach ($rkass as $rkas) {
            if ($rkas->bku()->exists() || $rkas->items()->whereHas('bkuItems')->exists()) {
                $cannotDelete[] = $rkas->kode_rkas;
            } else {
                $itemIds = $rkas->items()->pluck('id');
                Kebutuhan::whereIn('rkas_item_id', $itemIds)->update(['rkas_item_id' => null]);
                $rkas->delete();
                $deletedCount++;
            }
        }

        $this->selectedRkas = [];
        $this->selectAll = false;

        if (count($cannotDelete) > 0 && $deletedCount === 0) {
            $kodeList = implode(', ', $cannotDelete);
            $this->dispatch('swal:error', [
                'title' => 'Seluruh Pilihan Terikat Transaksi BKU',
                'text' => "Seluruh pilihan dokumen RKAS tidak dapat dihapus karena semua dokumen yang Anda pilih ({$kodeList}) sudah digunakan dalam transaksi BKU."
            ]);
            session()->flash('error', "Seluruh pilihan dokumen RKAS ({$kodeList}) tidak dapat dihapus karena sudah memiliki transaksi BKU.");
        } elseif (count($cannotDelete) > 0 && $deletedCount > 0) {
            $kodeList = implode(', ', $cannotDelete);
            $this->dispatch('swal:error', [
                'title' => 'Sebagian RKAS Terikat Transaksi BKU',
                'text' => "Berhasil menghapus {$deletedCount} dokumen RKAS. Namun, dalam pilihan Anda terdapat dokumen RKAS ({$kodeList}) yang sudah memiliki transaksi BKU sehingga tidak dapat dihapus."
            ]);
            session()->flash('warning', "Berhasil menghapus {$deletedCount} dokumen RKAS. Dokumen RKAS ({$kodeList}) tidak dapat dihapus karena terikat transaksi BKU.");
        } else {
            session()->flash('success', "Berhasil menghapus seluruh dokumen RKAS terpilih!");
        }
    }

    public function confirmDeleteRkas()
    {
        if ($this->deletingRkasId) {
            $this->deleteRkas($this->deletingRkasId);
            $this->reset(['deletingRkasId', 'deletingRkasKode']);
            $this->dispatch('close-modal', 'delete-rkas-modal');
        }
    }

    public function toggleExpand(int $id)
    {
        if ($this->expandedRkasId === $id) {
            $this->expandedRkasId = null;
        } else {
            $this->expandedRkasId = $id;
        }
    }

    public function render()
    {
        $periodes = PeriodeBos::orderBy('tahun_ajaran', 'desc')->get();

        $rkass = Rkas::with(['periodeBos', 'items', 'creator', 'approver'])
            ->when($this->search, function ($q) {
                $q->where('kode_rkas', 'like', "%{$this->search}%")
                  ->orWhere('nama_program', 'like', "%{$this->search}%");
            })
            ->when($this->filterPeriodeId, function ($q) {
                $q->where('periode_bos_id', $this->filterPeriodeId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        $katalogBarangs = KatalogBarang::where('is_active', true)->orderBy('nama_barang', 'asc')->get();

        $activeRkasModel = $this->activeRkasId ? Rkas::with('items')->find($this->activeRkasId) : null;

        return view('livewire.rkas.rkas-manager', compact('rkass', 'periodes', 'katalogBarangs', 'activeRkasModel'))
            ->layout('components.layouts.app', ['header' => 'Rencana Kegiatan & Anggaran Sekolah']);
    }
}
