<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Destination extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'district',
        'ticket_price',
        'latitude',
        'longitude',
        'opening_hours',
        'facilities',
        'tourism_type',
        'atmosphere',
        'activities',
        'accessibility',
    ];

    protected function casts(): array
    {
        return [
            'ticket_price' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }
    public function coverPhoto(): HasOne
    {
        return $this->hasOne(Photo::class)->oldestOfMany();
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * Tambahkan rata-rata rating dan jumlah ulasan yang sudah disetujui admin
     * sebagai kolom avg_rating dan reviews_count.
     */
    public function scopeWithApprovedRatings(Builder $query): Builder
    {
        return $query
            ->withAvg(['reviews as avg_rating' => fn ($q) => $q->where('status', 'approved')], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('status', 'approved')]);
    }

    /**
     * NULL = belum diketahui, 0 = gratis.
     */
    protected function priceLabel(): Attribute
    {
        return Attribute::get(function () {
            if ($this->ticket_price === null) {
                return 'Harga belum diketahui';
            }

            if ($this->ticket_price === 0) {
                return 'Gratis';
            }

            return 'Rp '.number_format($this->ticket_price, 0, ',', '.');
        });
    }

    protected function facilitiesList(): Attribute
    {
        return Attribute::get(fn () => self::splitList($this->facilities));
    }

    protected function activitiesList(): Attribute
    {
        return Attribute::get(fn () => self::splitList($this->activities));
    }

    /**
     * Koordinat hanya dianggap ada bila kedua nilainya terisi.
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    public static function splitList(?string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode('|', $value))));
    }
}