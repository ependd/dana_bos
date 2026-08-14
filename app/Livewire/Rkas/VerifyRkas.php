<?php

namespace App\Livewire\Rkas;

use Livewire\Component;
use App\Models\Rkas;

class VerifyRkas extends Component
{
    public string $kode;
    public ?Rkas $rkas = null;
    public string $digitalHash = '';

    // Lists for formatting labels
    public array $listKomponenS = [
        '1' => '1 - Standar Isi',
        '2' => '2 - Standar Proses',
        '3' => '3 - Standar Kompetensi Lulusan',
        '4' => '4 - Standar Pendidik dan Tenaga Kependidikan',
        '5' => '5 - Standar Sarana dan Prasarana',
        '6' => '6 - Standar Pengelolaan',
        '7' => '7 - Standar Pembiayaan',
        '8' => '8 - Standar Penilaian Pendidikan',
    ];

    public array $listKomponenK = [
        '1' => '1 - Penerimaan Peserta Didik Baru',
        '2' => '2 - Pengembangan Perpustakaan',
        '3' => '3 - Pelaksanaan Kegiatan Pembelajaran dan Ekstrakurikuler',
        '4' => '4 - Pelaksanaan Asesmen dan Evaluasi Pembelajaran',
        '5' => '5 - Pelaksanaan Administrasi Kegiatan Sekolah',
        '6' => '6 - Pengembangan Profesi Guru dan Tenaga Kependidikan',
        '7' => '7 - Pembiayaan Langganan Daya dan Jasa',
        '8' => '8 - Pemeliharaan Sarana dan Prasarana Sekolah',
        '9' => '9 - Penyediaan Alat Multimedia Pembelajaran',
        '12' => '12 - Pembayaran Honor',
    ];

    public function mount(string $kode)
    {
        $this->kode = $kode;
        $this->rkas = Rkas::with(['periodeBos', 'approver', 'items'])
            ->where('kode_rkas', $kode)
            ->first();

        if ($this->rkas && $this->rkas->status === Rkas::STATUS_APPROVED) {
            $this->digitalHash = hash('sha256', "{$this->rkas->id}-{$this->rkas->kode_rkas}-{$this->rkas->total_anggaran}-{$this->rkas->approved_at}");
        }
    }

    public function render()
    {
        return view('livewire.rkas.verify-rkas')
            ->layout('components.layouts.app', ['header' => 'Verifikasi Pengesahan Digital RKAS']);
    }
}
