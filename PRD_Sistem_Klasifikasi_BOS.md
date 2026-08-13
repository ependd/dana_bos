# Product Requirements Document (PRD)
## Sistem Monitoring Pemanfaatan Dana BOS Berbasis Naive Bayes dan Whale Optimization Algorithm
**Studi Kasus: SMPN 40 Kota Bekasi**

---

## 1. Latar Belakang

Pengelolaan dana Bantuan Operasional Sekolah (BOS) di SMPN 40 Kota Bekasi masih menghadapi tantangan dalam hal pengawasan kesesuaian antara perencanaan anggaran (RKAS) dengan realisasi penggunaannya (BKU). Proses monitoring yang dilakukan secara manual berisiko luput mendeteksi transaksi yang menyimpang, seperti anomali nilai realisasi, *mark-up*, atau duplikasi pencatatan. Sistem ini dibangun untuk mengotomatisasi proses pencatatan RKAS-BKU sekaligus menyediakan mekanisme deteksi anomali pemanfaatan dana menggunakan model klasifikasi *Naive Bayes* yang dioptimasi dengan *Whale Optimization Algorithm* (WOA).

## 2. Tujuan Produk

1. Menyediakan alur digital terintegrasi untuk pengajuan kebutuhan, penyusunan RKAS, pencatatan BKU, dan persetujuan berjenjang.
2. Mengklasifikasikan setiap transaksi pemanfaatan dana BOS ke dalam kategori **Wajar** atau **Menyimpang** secara otomatis.
3. Menyediakan visibilitas hasil monitoring bagi Kepala Sekolah sebagai dasar pengambilan keputusan dan pelaporan.
4. Mengurangi ketergantungan pada pemeriksaan manual terhadap kesesuaian anggaran dan realisasi.

## 3. Ruang Lingkup

### Termasuk dalam ruang lingkup
- Manajemen data master (katalog barang), periode anggaran BOS.
- Pengajuan dan konsolidasi kebutuhan oleh Guru.
- Penyusunan, review, persetujuan, dan revisi RKAS.
- Pencatatan BKU dan realisasi anggaran.
- Klasifikasi otomatis transaksi menggunakan model Naive Bayes + WOA (fitur hasil seleksi bersifat tetap, ditentukan sekali pada fase penelitian).
- Dashboard dan laporan monitoring (ekspor PDF).
- Manajemen akun & hak akses tiga peran (Guru, Admin/Bendahara, Kepala Sekolah).

### Di luar ruang lingkup
- Pelatihan ulang (*retraining*) model secara otomatis di dalam sistem.
- Integrasi dengan sistem keuangan sekolah lain atau sistem pusat Kemendikbud.
- Ekspor laporan selain format PDF.
- Deployment cloud/multi-sekolah (sistem dirancang untuk deployment lokal single-instance).

## 4. Aktor & Peran

| Aktor | Deskripsi | Hak Akses Utama |
|---|---|---|
| **Guru** | Pengguna yang mengajukan kebutuhan barang/kegiatan | Ajukan kebutuhan, pantau status, konfirmasi penerimaan barang |
| **Admin (Bendahara)** | Pengelola operasional harian sistem | Kelola data master & periode, kelola request guru, kelola RKAS & BKU, jalankan klasifikasi, lihat hasil monitoring |
| **Kepala Sekolah** | Pemberi persetujuan & pengawas | Setujui/tolak RKAS & revisi RKAS, lihat hasil monitoring, unduh laporan |

## 5. Kebutuhan Fungsional

### 5.1 Autentikasi & Manajemen Akun (semua peran)
- FR-01: Sistem menyediakan login dengan username & password.
- FR-02: Sistem membatasi percobaan login gagal maksimal 3 kali, disertai pesan jeda waktu (*lockout* sementara).
- FR-03: Sistem menyediakan mekanisme logout dengan konfirmasi.
- FR-04: Hak akses tiap peran diatur melalui *role-based access control* (Laravel Shield).

### 5.2 Modul Guru
- FR-05: Guru dapat melihat status buka/tutup periode pengajuan sebelum mengajukan kebutuhan.
- FR-06: Guru dapat mengajukan kebutuhan dengan memilih kategori, barang dari katalog, volume, dan satuan.
- FR-07: Sistem memvalidasi kelengkapan data sebelum kebutuhan disimpan.
- FR-08: Guru dapat memantau status (riwayat) kebutuhan yang diajukan beserta detailnya.
- FR-09: Guru menerima notifikasi ketika barang yang diajukan tersedia (realisasi telah dicatat).
- FR-10: Guru dapat mengonfirmasi penerimaan barang.

### 5.3 Modul Admin — Data Master & Periode
- FR-11: Admin dapat menambah, melihat, mengubah, dan menghapus data master katalog barang.
- FR-12: Admin dapat menambah periode anggaran BOS baru serta mengaktifkan/menonaktifkan periode berjalan.

### 5.4 Modul Admin — Pengelolaan Request Guru
- FR-13: Sistem melakukan konsolidasi otomatis atas seluruh kebutuhan yang diajukan Guru dalam satu periode.
- FR-14: Admin dapat meninjau (*review*) tiap request dan memberikan keputusan diterima atau ditolak disertai keterangan.

### 5.5 Modul Admin — RKAS
- FR-15: Admin dapat men-*generate* dokumen RKAS berdasarkan konsolidasi kebutuhan yang telah disetujui.
- FR-16: Admin dapat meninjau dan mengirimkan (*submit*) RKAS untuk persetujuan Kepala Sekolah.
- FR-17: Admin dapat mengajukan revisi RKAS di tengah periode berjalan.
- FR-18: RKAS yang ditolak Kepala Sekolah dikembalikan ke Admin untuk direvisi.

### 5.6 Modul Kepala Sekolah — Persetujuan RKAS
- FR-19: Kepala Sekolah dapat meninjau RKAS yang diajukan Admin.
- FR-20: Kepala Sekolah dapat menyetujui (ACC) atau menolak RKAS/revisi RKAS.
- FR-21: Persetujuan RKAS memicu sistem men-*generate* kode QR digital sebagai bukti pengesahan.

### 5.7 Modul Admin — BKU & Realisasi
- FR-22: Admin dapat men-*generate* dokumen BKU dari RKAS yang telah disahkan.
- FR-23: Admin dapat menginput realisasi aktual per pos anggaran.
- FR-24: Sistem memvalidasi data realisasi sebelum disimpan.
- FR-25: Sistem mengirim notifikasi otomatis ke Guru terkait saat realisasi/barang tersedia.

### 5.8 Modul Klasifikasi (Naive Bayes + WOA)
- FR-26: Admin dapat memilih data RKAS dan BKU pada periode tertentu untuk diklasifikasikan.
- FR-27: Sistem melakukan *preprocessing* otomatis (pembersihan data, *label encoding*, normalisasi *min-max*) terhadap data terpilih.
- FR-28: Sistem mengklasifikasikan setiap transaksi ke label **Wajar** atau **Menyimpang** menggunakan model Naive Bayes dengan subset fitur hasil seleksi WOA (X1, X2, X3, X4, X7, X8), yang bersifat tetap dan tidak dihitung ulang saat runtime.
- FR-29: Sistem menyimpan hasil klasifikasi (label prediksi & nilai probabilitas *posterior*) ke basis data.
- FR-30: Sistem menampilkan hasil klasifikasi kepada Admin setelah proses selesai.
- FR-31: Sistem menyediakan modul import khusus untuk memasukkan data historis (2022–2025) berlabel manual sebagai data pelatihan model (terpisah dari data RKAS/BKU periode berjalan).

### 5.9 Modul Monitoring & Pelaporan
- FR-32: Kepala Sekolah dan Admin dapat menyaring dan menampilkan hasil monitoring berdasarkan kebutuhan/periode tertentu.
- FR-33: Sistem menampilkan pesan jika data monitoring yang diminta belum tersedia.
- FR-34: Kepala Sekolah dapat mengunduh laporan hasil monitoring dalam format PDF.
- FR-35: Sistem memvalidasi ketersediaan data laporan sebelum proses unduh dijalankan.

## 6. Kebutuhan Non-Fungsional

| ID | Kategori | Deskripsi |
|---|---|---|
| NFR-01 | Akurasi Model | Model klasifikasi final (NB+WOA) mencapai akurasi ≥ 90% berdasarkan pengujian K-Fold Cross Validation (K=5) pada data historis. |
| NFR-02 | Konsistensi | Subset fitur hasil WOA bersifat tetap pasca fase pelatihan untuk menjamin hasil klasifikasi yang dapat direproduksi. |
| NFR-03 | Keamanan | Akses fitur dibatasi sesuai peran melalui RBAC; percobaan login dibatasi untuk mencegah *brute force* sederhana. |
| NFR-04 | Usability | Antarmuka dibangun dengan Filament 3, mengikuti pola UI yang konsisten antar peran. |
| NFR-05 | Portabilitas Data | Ekspor laporan terbatas pada format PDF sesuai kebutuhan dokumentasi sekolah. |
| NFR-06 | Deployment | Sistem berjalan pada lingkungan lokal (on-premise) sekolah, bukan infrastruktur cloud. |
| NFR-07 | Maintainability | Logika algoritma (preprocessing, Naive Bayes) dipisahkan ke *service class* independen dari *resource* Filament. |

## 7. Arsitektur & Tumpukan Teknologi

- **Backend Framework:** Laravel 12
- **Admin Panel / UI:** Filament 3
- **Otorisasi:** Laravel Shield (RBAC tiga peran)
- **Import/Export Data:** Laravel Excel (import data training), export PDF (laporan monitoring)
- **Pola Arsitektur:** Tiga lapis — *Presentation Layer* (Filament Resource/Page), *Service Layer* (PreprocessingPipeline, NaiveBayesClassifier, dan service pengorkestrasi lainnya), *Data Layer* (basis data relasional)

## 8. Ringkasan Alur Proses Bisnis

```
Guru: Ajukan Kebutuhan
   → Admin: Konsolidasi & Review Request
   → Admin: Generate RKAS → Submit
   → Kepala Sekolah: Review RKAS → ACC/Tolak
        (Tolak → Admin revisi → ulangi review)
   → [ACC] Generate QR Digital
   → Admin: Generate BKU → Input Realisasi
   → Sistem: Notifikasi ke Guru (barang tersedia)
   → Guru: Konfirmasi Penerimaan Barang
   → Admin: Jalankan Klasifikasi (Preprocessing → Naive Bayes + fitur WOA tetap)
   → Sistem: Simpan & Tampilkan Hasil Klasifikasi
   → Kepala Sekolah: Lihat Monitoring → Unduh Laporan PDF
```

## 9. Spesifikasi Model Klasifikasi (Ringkas)

| Aspek | Keterangan |
|---|---|
| Algoritma | Naive Bayes (Mixed/Hybrid: Kategorikal, Gaussian, Bernoulli) dioptimasi Whale Optimization Algorithm |
| Fitur Input Awal | X1–X8 (8 variabel) |
| Fitur Terpilih (hasil WOA) | X1, X2, X3, X4, X7, X8 (6 variabel) |
| Kelas Output | Wajar / Menyimpang |
| Data Pelatihan | Data historis 2022–2025, berlabel manual oleh bendahara |
| Validasi | K-Fold Cross Validation, K = 5 |
| Frekuensi Eksekusi WOA | Satu kali (fase penelitian/pelatihan), tidak dijalankan ulang saat runtime |
| Akurasi Model Final | 100% (pada pengujian K-Fold terhadap dataset penelitian) |

### 9.1 Perbandingan Model Baseline vs Model Optimasi

Optimasi fitur menggunakan WOA terbukti meningkatkan performa model dibandingkan Naive Bayes tanpa seleksi fitur (baseline, menggunakan seluruh 8 variabel input):

| Metrik | NB Baseline (8 Fitur) | NB + WOA (6 Fitur) | Peningkatan |
|---|---|---|---|
| Accuracy | 90.00% | 100.00% | +10.00% |
| Precision | 86.67% | 100.00% | +13.33% |
| Recall | 100.00% | 100.00% | 0.00% |
| F1-Score | 92.00% | 100.00% | +8.00% |
| Jumlah Fitur | 8 | 6 | −2 fitur |

Perbandingan ini menjadi justifikasi utama penggunaan WOA dalam sistem: selain meningkatkan seluruh metrik evaluasi (kecuali Recall yang sudah maksimal pada baseline), WOA juga menyederhanakan model dengan mengeliminasi 2 fitur (X5 dan X6) yang terbukti tidak berkontribusi signifikan terhadap akurasi klasifikasi.

## 10. Entitas Data Utama (Non-Eksh­austif)

- **Pengguna** (Guru, Admin, Kepala Sekolah)
- **Periode BOS**
- **Katalog Barang** (data master)
- **Kebutuhan** (pengajuan Guru)
- **RKAS**
- **BKU / Realisasi**
- **Data Training Historis** (berlabel manual)
- **Hasil Klasifikasi**

## 11. Batasan Produk

1. Tidak ada mekanisme *retraining* otomatis — pembaruan fitur model harus dilakukan manual di luar sistem oleh peneliti/pengembang.
2. Ekspor laporan hanya format PDF.
3. Sistem untuk deployment lokal, bukan multi-tenant/cloud.
4. Import data hanya berlaku untuk data training historis, bukan untuk data transaksi periode berjalan (yang diinput manual melalui alur RKAS/BKU reguler).

## 12. Metrik Keberhasilan

- Model klasifikasi mencapai akurasi ≥ 90% (tercapai: 100% pada pengujian akhir).
- Model NB+WOA menunjukkan peningkatan performa terukur dibandingkan NB Baseline pada seluruh metrik evaluasi (Accuracy +10%, Precision +13.33%, F1-Score +8%), sekaligus mengurangi jumlah fitur dari 8 menjadi 6, membuktikan efektivitas WOA sebagai metode seleksi fitur.
- Seluruh alur bisnis (pengajuan → persetujuan → realisasi → klasifikasi → pelaporan) dapat dijalankan end-to-end tanpa intervensi manual di luar sistem.
- Kepala Sekolah dapat mengakses hasil monitoring dan laporan PDF kapan pun dibutuhkan.