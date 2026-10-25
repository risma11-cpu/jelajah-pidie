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
     * Ikon emoji (HTML entity) untuk kategori.
     */
    protected function icon(): Attribute
    {
        return Attribute::get(fn () => match (Str::slug($this->name)) {
            'alam' => '&#127958;',
            'sejarah' => '&#127963;',
            'religi' => '&#128332;',
            'rekreasi' => '&#127906;',
            'kuliner' => '&#127836;',
            'edukasi' => '&#128218;',
            default => '&#127796;',
        });
    }
}