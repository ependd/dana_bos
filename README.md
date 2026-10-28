# Dana BOS: Sistem Pendamping ARKAS dengan Klasifikasi Pengajuan Kebutuhan

Aplikasi web untuk mencatat pengajuan kebutuhan sekolah dan menilai kewajarannya sebelum dimasukkan ke ARKAS. Setiap pengajuan diklasifikasikan sebagai **Wajar** atau **Menyimpang** menggunakan algoritma *Naive Bayes* yang dioptimasi dengan *Whale Optimization Algorithm* (WOA).

Dikembangkan sebagai skripsi Sistem Informasi, studi kasus **SMPN 40 Kota Bekasi** (2026).

## Latar Belakang

1. Laporan keuangan di ARKAS tidak bisa dilihat kembali setelah disubmit, sehingga menyulitkan saat audit jika bendahara tidak membuat catatan sendiri.
2. Penentuan barang yang layak diajukan masih bersifat subjektif.
3. Pengajuan kebutuhan oleh guru dan staf tata usaha masih dilakukan secara konvensional.

## Fitur Utama

- **Pengajuan kebutuhan digital**: guru dan staf tata usaha mengajukan kebutuhan lewat akun masing-masing dan memantau status pengajuannya.
- **Klasifikasi otomatis**: setiap pengajuan dinilai Wajar atau Menyimpang dengan Naive Bayes + WOA.
- **Acuan bagi bendahara**: hasil klasifikasi menjadi bahan objektif sebelum kebutuhan dimasukkan ke ARKAS.
- **Riwayat tercatat per akun**: seluruh pengajuan tersimpan sehingga dapat ditunjukkan saat audit.
- **Manajemen data RKAS dan BKU** serta laporan.
- **Tiga peran pengguna**: Guru/Staf TU, Admin/Bendahara, dan Kepala Sekolah.

## Model Klasifikasi

| Aspek | Keterangan |
|---|---|
| Algoritma | Mixed Naive Bayes (Categorical, Gaussian, Bernoulli) |
| Optimasi | Whale Optimization Algorithm untuk seleksi fitur |
| Fitur terpilih | 5 fitur dari 7 fitur kandidat, fitur dapat berbeda mengingat WOA bersifat stokastik  |
| Dataset | 150 data, disusun mengikuti pola BKU dan RKAS dengan aturan bisnis ketat |
| Validasi | K-Fold Cross Validation (K = 5) |
| Akurasi | Naive Bayes 86,67% menjadi 91,76% setelah optimasi WOA |

Catatan: WOA bersifat stokastik, sehingga fitur terpilih dan hasil akurasi dapat sedikit berbeda pada setiap proses pelatihan. WOA dijalankan sekali saat pelatihan, bukan saat aplikasi digunakan.

## Screenshot


### Dashboard
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/6c6700d3-9d4c-408d-a110-06b15a884294" />


### Form Pengajuan Kebutuhan
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/1eeef461-eed9-4412-b8b4-edb1f13f64a3" />


### Hasil Klasifikasi
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/015414cd-749c-46c6-b346-91afd59a05b5" />


### Pelatihan Model
<img width="1366" height="768" alt="image" src="https://github.com/user-attachments/assets/d2dcfac2-d2d5-41e7-9405-77d3a1de77af" />

