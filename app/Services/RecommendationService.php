<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\DestinationSimilarity;
use Illuminate\Support\Collection;

class RecommendationService
{
    public const SOURCE_ML = 'ml';

    public const SOURCE_FALLBACK = 'fallback';

    /**
     * Rekomendasi untuk satu destinasi.
     *
     * - source "ml": hasil content-based filtering (TF-IDF + cosine similarity)
     *   dari Google Colab yang diimpor ke tabel destination_similarities.
     * - source "fallback": destinasi lain dengan kategori yang sama.
     *   BUKAN hasil ML, dan harus ditampilkan dengan label yang jujur.
     *
     * Hanya destinasi yang masih ada di database yang dikembalikan.
     *
     * @return array{source: string, items: Collection<int, Destination>}
     */
    public function forDestination(Destination $destination, int $limit = 4): array
    {
        $rows = DestinationSimilarity::query()
            ->where('destination_id', $destination->id)
            ->orderBy('peringkat')
            ->limit($limit)
            ->get(['similar_destination_id', 'skor']);

        if ($rows->isNotEmpty()) {
            $found = Destination::query()
                ->with('category')
                ->withApprovedRatings()
                ->whereIn('id', $rows->pluck('similar_destination_id')->all())
                ->get()
                ->keyBy('id');

            $items = $rows
                ->map(function ($row) use ($found) {
                    $item = $found->get($row->similar_destination_id);

                    if ($item) {
                        $item->setAttribute('similarity_score', (float) $row->skor);
                    }

                    return $item;
                })
                ->filter()
                ->values();

            if ($items->isNotEmpty()) {
                return ['source' => self::SOURCE_ML, 'items' => $items];
            }
        }

        $items = Destination::query()
            ->with('category')
            ->withApprovedRatings()
            ->where('category_id', $destination->category_id)
            ->whereKeyNot($destination->id)
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return ['source' => self::SOURCE_FALLBACK, 'items' => $items];
    }

    /**
     * Ringkasan status integrasi ML untuk dashboard admin.
     *
     * @return array{pairs: int, covered: int, total: int, last_import: ?string}
     */
    public function status(): array
    {
        return [
            'pairs' => DestinationSimilarity::query()->count(),
            'covered' => DestinationSimilarity::query()->distinct()->count('destination_id'),
            'total' => Destination::query()->count(),
            'last_import' => DestinationSimilarity::query()->max('created_at'),
        ];
    }
}