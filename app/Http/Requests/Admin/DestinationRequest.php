<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Fasilitas dan aktivitas boleh ditulis satu per baris atau dipisah tanda |.
     * Disimpan dengan pemisah | seperti pada dataset.
     */
    protected function prepareForValidation(): void
    {
        foreach (['facilities', 'activities'] as $field) {
            $value = $this->input($field);

            if (is_string($value)) {
                $parts = preg_split('/[|\r\n]+/', $value) ?: [];
                $parts = array_values(array_filter(array_map('trim', $parts), fn ($p) => $p !== ''));

                $this->merge([$field => $parts ? implode('|', $parts) : null]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'district' => ['required', 'string', 'max:100'],
            'ticket_price' => ['nullable', 'integer', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'opening_hours' => ['nullable', 'string', 'max:150'],
            'facilities' => ['nullable', 'string', 'max:2000'],
            'tourism_type' => ['nullable', 'string', 'max:100'],
            'atmosphere' => ['nullable', 'string', 'max:100'],
            'activities' => ['nullable', 'string', 'max:2000'],
            'accessibility' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'kategori',
            'name' => 'nama destinasi',
            'description' => 'deskripsi',
            'district' => 'kecamatan',
            'ticket_price' => 'harga tiket',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'opening_hours' => 'jam buka',
            'facilities' => 'fasilitas',
            'tourism_type' => 'jenis wisata',
            'atmosphere' => 'suasana',
            'activities' => 'aktivitas',
            'accessibility' => 'aksesibilitas',
        ];
    }
}