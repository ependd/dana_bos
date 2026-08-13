<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi pesan kesalahan standar yang digunakan oleh
    | kelas validator. Beberapa aturan memiliki beberapa versi seperti
    | aturan ukuran. Jangan ragu untuk menyesuaikan setiap pesan di sini.
    |
    */

    'accepted' => 'Isian :attribute harus diterima.',
    'accepted_if' => 'Isian :attribute harus diterima ketika :other bernilai :value.',
    'active_url' => 'Isian :attribute bukan URL yang sah.',
    'after' => 'Isian :attribute harus tanggal setelah :date.',
    'after_or_equal' => 'Isian :attribute harus tanggal setelah atau sama dengan :date.',
    'alpha' => 'Isian :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Isian :attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => 'Isian :attribute hanya boleh berisi huruf dan angka.',
    'array' => 'Isian :attribute harus berupa sebuah array.',
    'ascii' => 'Isian :attribute hanya boleh berisi karakter alfanumerik dan simbol single-byte.',
    'before' => 'Isian :attribute harus tanggal sebelum :date.',
    'before_or_equal' => 'Isian :attribute harus tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Isian :attribute harus memiliki anggota antara :min dan :max.',
        'file' => 'Isian :attribute harus berukuran antara :min dan :max kilobita.',
        'numeric' => 'Isian :attribute harus bernilai antara :min dan :max.',
        'string' => 'Isian :attribute harus berukuran antara :min dan :max karakter.',
    ],
    'boolean' => 'Isian :attribute harus bernilai true atau false.',
    'can' => 'Isian :attribute mengandung nilai yang tidak diizinkan.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'contains' => 'Isian :attribute kekurangan nilai yang diwajibkan.',
    'current_password' => 'Kata sandi salah.',
    'date' => 'Isian :attribute bukan tanggal yang sah.',
    'date_equals' => 'Isian :attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => 'Isian :attribute tidak cocok dengan format :format.',
    'decimal' => 'Isian :attribute harus memiliki :decimal tempat desimal.',
    'declined' => 'Isian :attribute harus ditolak.',
    'declined_if' => 'Isian :attribute harus ditolak ketika :other bernilai :value.',
    'different' => 'Isian :attribute dan :other harus berbeda.',
    'digits' => 'Isian :attribute harus berukuran :digits angka.',
    'digits_between' => 'Isian :attribute harus berukuran antara :min dan :max angka.',
    'dimensions' => 'Isian :attribute memiliki dimensi gambar yang tidak sah.',
    'distinct' => 'Isian :attribute memiliki nilai yang duplikat.',
    'doesnt_contain' => 'Isian :attribute tidak boleh berisi nilai berikut: :values.',
    'doesnt_end_with' => 'Isian :attribute tidak boleh diakhiri dengan nilai berikut: :values.',
    'doesnt_start_with' => 'Isian :attribute tidak boleh diawali dengan nilai berikut: :values.',
    'email' => 'Isian :attribute harus berupa alamat surel yang sah.',
    'ends_with' => 'Isian :attribute harus diakhiri dengan salah satu dari berikut: :values.',
    'enum' => 'Isian :attribute yang dipilih tidak sah.',
    'exists' => 'Isian :attribute yang dipilih tidak sah.',
    'extensions' => 'Isian :attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => 'Isian :attribute harus berupa sebuah berkas.',
    'filled' => 'Isian :attribute harus memiliki nilai.',
    'gt' => [
        'array' => 'Isian :attribute harus memiliki lebih dari :value anggota.',
        'file' => 'Isian :attribute harus berukuran lebih besar dari :value kilobita.',
        'numeric' => 'Isian :attribute harus bernilai lebih besar dari :value.',
        'string' => 'Isian :attribute harus berukuran lebih besar dari :value karakter.',
    ],
    'gte' => [
        'array' => 'Isian :attribute harus memiliki :value anggota atau lebih.',
        'file' => 'Isian :attribute harus berukuran lebih besar dari atau sama dengan :value kilobita.',
        'numeric' => 'Isian :attribute harus bernilai lebih besar dari atau sama dengan :value.',
        'string' => 'Isian :attribute harus berukuran lebih besar dari atau sama dengan :value karakter.',
    ],
    'hex_color' => 'Isian :attribute harus berupa warna heksadesimal yang sah.',
    'image' => 'Isian :attribute harus berupa gambar.',
    'in' => 'Isian :attribute yang dipilih tidak sah.',
    'in_array' => 'Isian :attribute tidak ada di dalam :other.',
    'integer' => 'Isian :attribute harus berupa bilangan bulat.',
    'ip' => 'Isian :attribute harus berupa alamat IP yang sah.',
    'ipv4' => 'Isian :attribute harus berupa alamat IPv4 yang sah.',
    'ipv6' => 'Isian :attribute harus berupa alamat IPv6 yang sah.',
    'json' => 'Isian :attribute harus berupa string JSON yang sah.',
    'lowercase' => 'Isian :attribute harus berupa huruf kecil.',
    'lt' => [
        'array' => 'Isian :attribute harus memiliki kurang dari :value anggota.',
        'file' => 'Isian :attribute harus berukuran kurang dari :value kilobita.',
        'numeric' => 'Isian :attribute harus bernilai kurang dari :value.',
        'string' => 'Isian :attribute harus berukuran kurang dari :value karakter.',
    ],
    'lte' => [
        'array' => 'Isian :attribute tidak boleh memiliki lebih dari :value anggota.',
        'file' => 'Isian :attribute harus berukuran kurang dari atau sama dengan :value kilobita.',
        'numeric' => 'Isian :attribute harus bernilai kurang dari atau sama dengan :value.',
        'string' => 'Isian :attribute harus berukuran kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address' => 'Isian :attribute harus berupa alamat MAC yang sah.',
    'max' => [
        'array' => 'Isian :attribute tidak boleh memiliki lebih dari :max anggota.',
        'file' => 'Isian :attribute tidak boleh berukuran lebih besar dari :max kilobita.',
        'numeric' => 'Isian :attribute tidak boleh bernilai lebih besar dari :max.',
        'string' => 'Isian :attribute tidak boleh berukuran lebih besar dari :max karakter.',
    ],
    'max_digits' => 'Isian :attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => 'Isian :attribute harus berupa berkas berjenis: :values.',
    'mimetypes' => 'Isian :attribute harus berupa berkas berjenis: :values.',
    'min' => [
        'array' => 'Isian :attribute minimal harus memiliki :min anggota.',
        'file' => 'Isian :attribute minimal harus berukuran :min kilobita.',
        'numeric' => 'Isian :attribute minimal harus bernilai :min.',
        'string' => 'Isian :attribute minimal harus berukuran :min karakter.',
    ],
    'min_digits' => 'Isian :attribute minimal harus memiliki :min digit.',
    'missing' => 'Isian :attribute harus tidak ada.',
    'missing_if' => 'Isian :attribute harus tidak ada ketika :other bernilai :value.',
    'missing_unless' => 'Isian :attribute harus tidak ada kecuali :other bernilai :value.',
    'missing_with' => 'Isian :attribute harus tidak ada ketika :values ada.',
    'missing_with_all' => 'Isian :attribute harus tidak ada ketika :values ada semua.',
    'multiple_of' => 'Isian :attribute harus merupakan kelipatan dari :value.',
    'not_in' => 'Isian :attribute yang dipilih tidak sah.',
    'not_regex' => 'Format isian :attribute tidak sah.',
    'numeric' => 'Isian :attribute harus berupa angka.',
    'password' => [
        'letters' => 'Isian :attribute harus mengandung setidaknya satu huruf.',
        'mixed' => 'Isian :attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers' => 'Isian :attribute harus mengandung setidaknya satu angka.',
        'symbols' => 'Isian :attribute harus mengandung setidaknya satu simbol.',
        'uncompromised' => 'Isian :attribute yang dimasukkan telah muncul dalam kebocoran data. Silakan pilih :attribute yang lain.',
    ],
    'present' => 'Isian :attribute harus ada.',
    'present_if' => 'Isian :attribute harus ada ketika :other bernilai :value.',
    'present_unless' => 'Isian :attribute harus ada kecuali :other bernilai :value.',
    'present_with' => 'Isian :attribute harus ada ketika :values ada.',
    'present_with_all' => 'Isian :attribute harus ada ketika :values ada semua.',
    'prohibited' => 'Isian :attribute dilarang.',
    'prohibited_if' => 'Isian :attribute dilarang ketika :other bernilai :value.',
    'prohibited_unless' => 'Isian :attribute dilarang kecuali :other ada di dalam :values.',
    'prohibits' => 'Isian :attribute melarang :other untuk ada.',
    'regex' => 'Format isian :attribute tidak sah.',
    'required' => 'Bidang :attribute wajib diisi.',
    'required_array_keys' => 'Isian :attribute harus berisi entri untuk: :values.',
    'required_if' => 'Bidang :attribute wajib diisi ketika :other bernilai :value.',
    'required_if_accepted' => 'Bidang :attribute wajib diisi ketika :other diterima.',
    'required_if_declined' => 'Bidang :attribute wajib diisi ketika :other ditolak.',
    'required_unless' => 'Bidang :attribute wajib diisi kecuali :other ada di dalam :values.',
    'required_with' => 'Bidang :attribute wajib diisi ketika :values ada.',
    'required_with_all' => 'Bidang :attribute wajib diisi ketika :values ada semua.',
    'required_without' => 'Bidang :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Bidang :attribute wajib diisi ketika tidak ada satupun :values yang ada.',
    'same' => 'Isian :attribute dan :other harus sama.',
    'size' => [
        'array' => 'Isian :attribute harus mengandung :size anggota.',
        'file' => 'Isian :attribute harus berukuran :size kilobita.',
        'numeric' => 'Isian :attribute harus berukuran :size.',
        'string' => 'Isian :attribute harus berukuran :size karakter.',
    ],
    'starts_with' => 'Isian :attribute harus diawali dengan salah satu dari berikut: :values.',
    'string' => 'Isian :attribute harus berupa string.',
    'timezone' => 'Isian :attribute harus berupa zona waktu yang sah.',
    'unique' => 'Isian :attribute sudah ada sebelumnya.',
    'uploaded' => 'Isian :attribute gagal diunggah.',
    'uppercase' => 'Isian :attribute harus berupa huruf besar.',
    'url' => 'Isian :attribute harus berupa URL yang sah.',
    'ulid' => 'Isian :attribute harus berupa ULID yang sah.',
    'uuid' => 'Isian :attribute harus berupa UUID yang sah.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'periode_bos_id' => 'Periode BOS',
        'katalog_barang_id' => 'Barang',
        'kategori_belanja' => 'Kategori Belanja',
        'volume' => 'Volume / Jumlah',
        'satuan' => 'Satuan',
        'keterangan' => 'Keterangan / Justifikasi',
    ],

];
