<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Destination;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Satu pengguna satu ulasan per destinasi. Mengirim lagi = mengubah ulasan
     * dan statusnya kembali menunggu persetujuan admin.
     */
    public function store(StoreReviewRequest $request, Destination $destination): RedirectResponse
    {
        Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'destination_id' => $destination->id,
            ],
            [
                'rating' => $request->validated('rating'),
                'comment' => $request->validated('comment'),
                'status' => 'pending',
            ]
        );

        return redirect()
            ->route('destinations.show', $destination)
            ->with('success', 'Ulasan terkirim dan menunggu persetujuan admin.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        abort_unless($review->user_id === $request->user()->id, 403);

        $destinationId = $review->destination_id;
        $review->delete();

        return redirect()
            ->route('destinations.show', $destinationId)
            ->with('success', 'Ulasan dihapus.');
    }
}