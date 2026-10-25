<?php

return [
    'required' => ':attribute wajib diisi.',
    'required_with' => ':attribute wajib diisi bila :values diisi.',
    'string' => ':attribute harus berupa teks.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'integer' => ':attribute harus berupa angka bulat.',
    'numeric' => ':attribute harus berupa angka.',
    'array' => ':attribute harus berupa daftar.',
    'image' => ':attribute harus berupa gambar.',
    'mimes' => ':attribute harus berformat: :values.',
    'uploaded' => ':attribute gagal diunggah. Pastikan ukurannya tidak terlalu besar.',
    'in' => ':attribute tidak valid.',
    'current_password' => 'Password saat ini salah.',
    'min' => [
        'string' => ':attribute minimal :min karakter.',
        'numeric' => ':attribute minimal :min.',
        'array' => ':attribute minimal :min berkas.',
    ],
    'max' => [
        'string' => ':attribute maksimal :max karakter.',
        'numeric' => ':attribute maksimal :max.',
        'file' => ':attribute maksimal :max KB.',
        'array' => ':attribute maksimal :max berkas.',
    ],
    'between' => [
        'numeric' => ':attribute harus antara :min dan :max.',
    ],
    'custom' => [],
    'attributes' => [
        'name' => 'nama',
        'email' => 'email',
        'password' => 'password',
        'password_confirmation' => 'konfirmasi password',
        'current_password' => 'password saat ini',
        'rating' => 'rating',
        'comment' => 'komentar',
        'status' => 'status',
    ],
];