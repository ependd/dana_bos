<?php

namespace Database\Seeders;

use App\Models\KatalogBarang;
use Illuminate\Database\Seeder;

class KatalogBarangSeeder extends Seeder
{
    /**
     * Seed sample katalog barang sekolah.
     */
    public function run(): void
    {
        $items = [
            ['kode_barang' => 'ATK-001', 'nama_barang' => 'Kertas HVS A4 70gr', 'kategori' => 'ATK', 'satuan' => 'rim', 'harga_referensi' => 55000],
            ['kode_barang' => 'ATK-002', 'nama_barang' => 'Spidol Whiteboard', 'kategori' => 'ATK', 'satuan' => 'lusin', 'harga_referensi' => 84000],
            ['kode_barang' => 'ATK-003', 'nama_barang' => 'Tinta Printer', 'kategori' => 'ATK', 'satuan' => 'botol', 'harga_referensi' => 95000],
            ['kode_barang' => 'ATK-004', 'nama_barang' => 'Buku Tulis', 'kategori' => 'ATK', 'satuan' => 'lusin', 'harga_referensi' => 48000],
            ['kode_barang' => 'ATK-005', 'nama_barang' => 'Penggaris 30cm', 'kategori' => 'ATK', 'satuan' => 'buah', 'harga_referensi' => 8000],
            ['kode_barang' => 'PBM-001', 'nama_barang' => 'Buku Paket Matematika', 'kategori' => 'Bahan Ajar', 'satuan' => 'eksemplar', 'harga_referensi' => 65000],
            ['kode_barang' => 'PBM-002', 'nama_barang' => 'Buku Paket IPA', 'kategori' => 'Bahan Ajar', 'satuan' => 'eksemplar', 'harga_referensi' => 70000],
            ['kode_barang' => 'PBM-003', 'nama_barang' => 'Buku Paket Bahasa Indonesia', 'kategori' => 'Bahan Ajar', 'satuan' => 'eksemplar', 'harga_referensi' => 60000],
            ['kode_barang' => 'LAB-001', 'nama_barang' => 'Alat Praktik IPA (Kit)', 'kategori' => 'Peralatan Lab', 'satuan' => 'set', 'harga_referensi' => 1500000],
            ['kode_barang' => 'LAB-002', 'nama_barang' => 'Mikroskop', 'kategori' => 'Peralatan Lab', 'satuan' => 'unit', 'harga_referensi' => 2500000],
            ['kode_barang' => 'IT-001', 'nama_barang' => 'Laptop', 'kategori' => 'TIK', 'satuan' => 'unit', 'harga_referensi' => 8000000],
            ['kode_barang' => 'IT-002', 'nama_barang' => 'Printer', 'kategori' => 'TIK', 'satuan' => 'unit', 'harga_referensi' => 3500000],
            ['kode_barang' => 'IT-003', 'nama_barang' => 'Proyektor', 'kategori' => 'TIK', 'satuan' => 'unit', 'harga_referensi' => 6000000],
            ['kode_barang' => 'MBL-001', 'nama_barang' => 'Meja Siswa', 'kategori' => 'Mebeler', 'satuan' => 'unit', 'harga_referensi' => 450000],
            ['kode_barang' => 'MBL-002', 'nama_barang' => 'Kursi Siswa', 'kategori' => 'Mebeler', 'satuan' => 'unit', 'harga_referensi' => 350000],
            ['kode_barang' => 'PML-001', 'nama_barang' => 'Jasa Pemeliharaan AC', 'kategori' => 'Pemeliharaan', 'satuan' => 'kegiatan', 'harga_referensi' => 500000],
            ['kode_barang' => 'PML-002', 'nama_barang' => 'Jasa Pemeliharaan Komputer', 'kategori' => 'Pemeliharaan', 'satuan' => 'kegiatan', 'harga_referensi' => 350000],
            ['kode_barang' => 'KGT-001', 'nama_barang' => 'Kegiatan Ekstrakurikuler', 'kategori' => 'Kegiatan', 'satuan' => 'kegiatan', 'harga_referensi' => 2000000],
            ['kode_barang' => 'KGT-002', 'nama_barang' => 'Kegiatan OSIS', 'kategori' => 'Kegiatan', 'satuan' => 'kegiatan', 'harga_referensi' => 1500000],
            ['kode_barang' => 'KGT-003', 'nama_barang' => 'Ujian Akhir Semester', 'kategori' => 'Kegiatan', 'satuan' => 'kegiatan', 'harga_referensi' => 5000000],
        ];

        foreach ($items as $item) {
            KatalogBarang::create(array_merge($item, [
                'is_active' => true,
                'deskripsi' => null,
            ]));
        }
    }
}
