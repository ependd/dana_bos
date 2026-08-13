<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Monitoring Pemanfaatan Dana BOS</title>
    <style>
        @page {
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            color: #000000;
            line-height: 1.4;
            padding: 1.5cm 1.5cm 2cm 1.5cm;
        }

        .header { text-align: center; margin-bottom: 16px; border-bottom: 3px double #000000; padding-bottom: 8px; margin-left: -1.5cm; margin-right: -1.5cm; padding-left: 1.5cm; padding-right: 1.5cm; }
        .header h1 { font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        .header h2 { font-size: 16px; margin-top: 2px; font-weight: bold; }
        .header p { font-size: 10.5px; margin-top: 2px; color: #000000; }

        .title-section { text-align: center; margin-top: 12px; margin-bottom: 14px; }
        .title-section h2 { font-size: 13.5px; text-decoration: underline; font-weight: bold; text-transform: uppercase; }
        .title-section p { font-size: 11px; margin-top: 2px; }

        .info-box { margin-bottom: 14px; padding: 8px 0; background: #ffffff; }
        .info-box table { width: 100%; border-collapse: collapse; }
        .info-box td { padding: 3px 4px; vertical-align: top; font-size: 10.5px; color: #000000; }
        .info-box td.label { font-weight: bold; width: 130px; }

        .summary { margin-bottom: 16px; text-align: left; }
        .summary table { width: 100%; max-width: 450px; margin: 0; border-collapse: collapse; text-align: left; }
        .summary td { padding: 3px 0; text-align: left; vertical-align: middle; font-size: 11px; color: #000000; }
        .summary .label { font-weight: bold; width: 210px; color: #000000; }
        .summary .value { font-weight: bold; color: #000000; }
        .summary .wajar { color: #000000; font-weight: normal; }
        .summary .menyimpang { color: #000000; font-weight: normal; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10px; }
        table.data th, table.data td { border: 1px solid #000000; padding: 6px 8px; text-align: left; word-wrap: break-word; color: #000000; }
        table.data th { background: #ffffff; color: #000000; text-align: center; font-weight: bold; font-size: 10.5px; }
        table.data td.center { text-align: center; }
        table.data tr:nth-child(even) { background: #ffffff; }

        .badge { padding: 0; font-weight: normal; font-size: 10px; color: #000000; }
        .badge-wajar { color: #000000; }
        .badge-menyimpang { color: #000000; }

        .footer { margin-top: 20px; page-break-inside: avoid; }
        .footer table { width: 100%; }
        .footer .ttd { text-align: center; width: 45%; vertical-align: top; float: right; color: #000000; }
    </style>
</head>
<body style="padding: 1.5cm 1.5cm 2cm 1.5cm;">
    {{-- Header Kop Surat --}}
    <div class="header">
        <h1>Pemerintah Kota Bekasi</h1>
        <h1>Dinas Pendidikan</h1>
        <h2>SMPN 40 Kota Bekasi</h2>
        <p>Jl. Rawa Mulya, RT.002/RW.003, Kel. Mustikajaya, Kec. Mustikajaya, Kota Bekasi, Jawa Barat 17158</p>
    </div>

    {{-- Judul Laporan --}}
    <div class="title-section">
        <h2>Laporan Hasil Monitoring Pemanfaatan Dana BOS</h2>
        <p>Periode: {{ $periode?->tahun_ajaran ?? '-' }}</p>
    </div>

    @php
        $bulanList = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $approvedAtIndo = '-';
        if ($rkas && $rkas->approved_at) {
            $approvedAt = \Carbon\Carbon::parse($rkas->approved_at);
            $approvedAtIndo = $approvedAt->day . ' ' . $bulanList[(int)$approvedAt->month] . ' ' . $approvedAt->year;
        }
    @endphp

    {{-- Info Box --}}
    <div class="info-box">
        <table>
            <tr>
                <td class="label">Kode RKAS</td>
                <td style="width: 35%;">: {{ $rkas->kode_rkas ?? '-' }}</td>
                <td class="label">Total Anggaran</td>
                <td>: Rp {{ number_format($rkas->total_anggaran ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Persetujuan</td>
                <td>: {{ $approvedAtIndo }}</td>
                <td class="label">Disetujui Oleh</td>
                <td>: {{ $rkas && $rkas->approver ? $rkas->approver->name : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td colspan="3">: {{ $tanggalCetak }}</td>
            </tr>
        </table>
    </div>

    {{-- Summary (Rata Kiri, Warna Hitam Polos) --}}
    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Transaksi Diklasifikasi</td>
                <td class="value">: {{ $totalTransaksi }}</td>
            </tr>
            <tr>
                <td class="label wajar">Transaksi Wajar</td>
                <td class="value wajar">: {{ $totalWajar }} ({{ $persentaseWajar }}%)</td>
            </tr>
            <tr>
                <td class="label menyimpang">Transaksi Menyimpang</td>
                <td class="value menyimpang">: {{ $totalMenyimpang }} ({{ $totalTransaksi > 0 ? round(100 - $persentaseWajar, 2) : 0 }}%)</td>
            </tr>
        </table>
    </div>

    {{-- Detail Table (No, Nama Barang, Prediksi, Keterangan) --}}
    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 42%;">Nama Barang</th>
                <th style="width: 20%;">Prediksi</th>
                <th style="width: 30%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hasil as $index => $item)
            @php
                $namaBarang = $item->bkuItem?->uraian 
                    ?? $item->rkasItem?->uraian_belanja 
                    ?? $item->rkasItem?->kebutuhan?->nama_barang 
                    ?? 'Barang / Uraian Belanja (' . $item->kode_rekening . ')';
            @endphp
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $namaBarang }}</td>
                <td class="center">
                    <span class="badge badge-{{ strtolower($item->label_prediksi) }}">
                        {{ $item->label_prediksi }}
                    </span>
                </td>
                <td class="{{ $item->isWajar() ? 'center' : '' }}">{{ $item->keterangan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="center">Tidak ada data hasil klasifikasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer / TTD --}}
    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="width: 55%;"></td>
                <td class="ttd" style="width: 45%; text-align: center; font-size: 10px;">
                    <p style="font-size: 10px;">Bekasi, {{ $tanggalCetak }}</p>
                    <p style="font-size: 10px;">Kepala SMPN 40 Kota Bekasi</p>
                    
                    @php
                        $qrBase64 = null;
                        if ($rkas) {
                            try {
                                if ($rkas->qr_code_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($rkas->qr_code_path)) {
                                    $imagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($rkas->qr_code_path);
                                    $svgContent = file_get_contents($imagePath);
                                    $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                } else {
                                    $service = new \App\Services\RkasService();
                                    $path = $service->generateQrCode($rkas);
                                    $imagePath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
                                    $svgContent = file_get_contents($imagePath);
                                    $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                }
                            } catch (\Throwable $e) {}
                        }
                    @endphp

                    @if($rkas && $qrBase64)
                        <div style="margin: 4px 0; text-align: center;">
                            <img src="{{ $qrBase64 }}" style="width: 55px; height: 55px; display: inline-block;" alt="QR Code" />
                            <br>
                            <span style="font-size: 6.5px; color: #333333; font-style: italic; display: block; margin-top: 1px;">Disahkan secara digital</span>
                        </div>
                    @else
                        <div style="height: 60px; margin: 4px 0;"></div>
                    @endif

                    <p style="font-size: 10px;"><strong>{{ $rkas && $rkas->approver ? $rkas->approver->name : 'Kepala Sekolah' }}</strong></p>
                    <p style="font-size: 9px;">NIP. {{ $rkas && $rkas->approver ? $rkas->approver->nip ?? '.........................' : '.........................' }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
