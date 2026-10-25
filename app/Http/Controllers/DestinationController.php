<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Destination;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function __construct(private RecommendationService $recommendations)
    {
    }

    public function index(Request $request): View
    {
        $q = $this->text($request, 'q');
        $district = $this->text($request, 'kecamatan');
        $type = $this->text($request, 'jenis');
        $access = $this->text($request, 'akses');
        $sort = $this->text($request, 'urut');

        $categoryId = $request->query('kategori');
        $categoryId = is_numeric($categoryId) ? (int) $categoryId : 0;

        $maxPrice = $request->query('harga_maks');
        $maxPrice = is_numeric($maxPrice) ? max(0, (int) $maxPrice) : null;

        $query = Destination::query()
            ->with(['category', 'coverPhoto'])
            ->withApprovedRatings()
            ->when($q !== '', fn ($builder) => $builder->where('name', 'like', '%'.$q.'%'))
            ->when($categoryId > 0, fn ($builder) => $builder->where('category_id', $categoryId))
            ->when($district !== '', fn ($builder) => $builder->where('district', $district))
            ->when($type !== '', fn ($builder) => $builder->where('tourism_type', $type))
            ->when($access !== '', fn ($builder) => $builder->where('accessibility', $access))
            ->when($maxPrice !== null, fn ($builder) => $builder
                ->whereNotNull('ticket_price')
                ->where('ticket_price', '<=', $maxPrice));

        match ($sort) {
            'rating' => $query->orderByRaw('avg_rating IS NULL')->orderByDesc('avg_rating')->orderBy('name'),
            'terbaru' => $query->orderByDesc('id'),
            default => $query->orderBy('name'),
        };

        $destinations = $query->paginate(9)->withQueryString();

        return view('destinations.index', [
            'destinations' => $destinations,
            'categories' => Category::orderBy('name')->get(),
            'districts' => Destination::query()->whereNotNull('district')->distinct()->orderBy('district')->pluck('district'),
            'types' => Destination::query()->whereNotNull('tourism_type')->distinct()->orderBy('tourism_type')->pluck('tourism_type'),
            'accessibilities' => Destination::query()->whereNotNull('accessibility')->distinct()->orderBy('accessibility')->pluck('accessibility'),
            'filters' => [
                'q' => $q,
                'kategori' => $categoryId > 0 ? $categoryId : '',
                'kecamatan' => $district,
                'jenis' => $type,
                'akses' => $access,
                'harga_maks' => $maxPrice ?? '',
                'urut' => in_array($sort, ['rating', 'terbaru'], true) ? $sort : 'nama',
            ],
        ]);
    }

    public function show(Request $request, Destination $destination): View
    {
        $destination->load(['category', 'photos']);
        $destination->loadAvg(['reviews as avg_rating' => fn ($q) => $q->where('status', 'approved')], 'rating');
        $destination->loadCount(['reviews as reviews_count' => fn ($q) => $q->where('status', 'approved')]);

        $reviews = $destination->reviews()
            ->where('status', 'approved')
            ->with('user:id,name')
            ->latest()
            ->paginate(5, ['*'], 'ulasan')
            ->withQueryString();

        $myReview = null;
        $isFavorite = false;

        if ($user = $request->user()) {
            $myReview = $destination->reviews()->where('user_id', $user->id)->first();
            $isFavorite = $user->favorites()->where('destinations.id', $destination->id)->exists();
        }

        return view('destinations.show', [
            'destination' => $destination,
            'reviews' => $reviews,
            'myReview' => $myReview,
            'isFavorite' => $isFavorite,
            'recommendation' => $this->recommendations->forDestination($destination, 4),
        ]);
    }

    private function text(Request $request, string $key): string
    {
        $value = $request->query($key);

        return is_string($value) ? trim($value) : '';
    }
}