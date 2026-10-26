<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    /**
     * Nama kategori huruf kecil tanpa spasi, dipakai sebagai kelas CSS
     * (thumb-alam, thumb-sejarah, dst).
     */
    protected function slugName(): Attribute
    {
        return Attribute::get(fn () => Str::slug($this->name));
    }

    /**
     * Nama ikon SVG (lihat components/icon.blade.php) untuk kategori.
     */
    protected function iconName(): Attribute
    {
        return Attribute::get(fn () => match (Str::slug($this->name)) {
            'alam' => 'mountain',
            'sejarah' => 'landmark',
            'religi' => 'moon',
            'rekreasi' => 'sun',
            'kuliner' => 'utensils',
            'edukasi' => 'book',
            default => 'compass',
        });
    }
}