<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationSimilarity extends Model
{
    protected $fillable = [
        'destination_id',
        'similar_destination_id',
        'peringkat',
        'skor',
    ];

    protected function casts(): array
    {
        return [
            'peringkat' => 'integer',
            'skor' => 'float',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function similar(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'similar_destination_id');
    }
}