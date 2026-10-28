Dana BOS: Sistem Pendamping ARKAS dengan Klasifikasi Pengajuan Kebutuhan

Aplikasi web untuk mencatat pengajuan kebutuhan sekolah dan menilai kewajarannya sebelum dimasukkan ke ARKAS. Setiap pengajuan diklasifikasikan sebagai Wajar atau Menyimpang menggunakan algoritma Naive Bayes yang dioptimasi dengan Whale Optimization Algorithm (WOA).

Dikembangkan sebagai skripsi Sistem Informasi, studi kasus SMPN 40 Kota Bekasi (2026).

Latar Belakang
Laporan keuangan di ARKAS tidak bisa dilihat kembali setelah disubmit, sehingga menyulitkan saat audit jika bendahara tidak membuat catatan sendiri.
Penentuan barang yang layak diajukan masih bersifat subjektif.
Pengajuan kebutuhan oleh guru dan staf tata usaha masih dilakukan secara konvensional.
Fitur Utama
Pengajuan kebutuhan digital: guru dan staf tata usaha mengajukan kebutuhan lewat akun masing-masing dan memantau status pengajuannya.
Klasifikasi otomatis: setiap pengajuan dinilai Wajar atau Menyimpang dengan Naive Bayes + WOA.
Acuan bagi bendahara: hasil klasifikasi menjadi bahan objektif sebelum kebutuhan dimasukkan ke ARKAS.
Riwayat tercatat per akun: seluruh pengajuan tersimpan sehingga dapat ditunjukkan saat audit.
Manajemen data RKAS dan BKU serta laporan.
Tiga peran pengguna: Guru/Staf TU, Admin/Bendahara, dan Kepala Sekolah.
Model Klasifikasi
Aspek	Keterangan
Algoritma	Mixed Naive Bayes (Categorical, Gaussian, Bernoulli)
Optimasi	Whale Optimization Algorithm untuk seleksi fitur
Fitur terpilih	5 fitur dari 7 fitur kandidat
Dataset	150 data, disusun mengikuti pola BKU dan RKAS dengan aturan bisnis ketat
Validasi	K-Fold Cross Validation (K = 5)
Akurasi	Naive Bayes 86,67% menjadi 91,76% setelah optimasi WOA

Catatan: WOA bersifat stokastik, sehingga fitur terpilih dan hasil akurasi dapat sedikit berbeda pada setiap proses pelatihan. WOA dijalankan sekali saat pelatihan, bukan saat aplikasi digunakan.

Screenshot

Ganti path di bawah dengan screenshot asli (simpan di docs/screenshots/).

Dashboard

Dashboard

Form Pengajuan Kebutuhan

Pengajuan

Hasil Klasifikasi

Klasifikasi

Pelatihan Model

Pelatihan
