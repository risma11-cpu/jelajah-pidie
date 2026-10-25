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
    'in' => ':attribute tidak valid.',
    'current_password' => 'Password saat ini salah.',
    'min' => [
        'string' => ':attribute minimal :min karakter.',
        'numeric' => ':attribute minimal :min.',
    ],
    'max' => [
        'string' => ':attribute maksimal :max karakter.',
        'numeric' => ':attribute maksimal :max.',
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