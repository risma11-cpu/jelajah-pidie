<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePhotoRequest;
use App\Models\Destination;
use App\Models\Photo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    private const MAX_PER_DESTINATION = 10;

    public function store(StorePhotoRequest $request, Destination $destination): RedirectResponse
    {
        $files = $request->file('photos', []);
        $existing = $destination->photos()->count();

        if ($existing + count($files) > self::MAX_PER_DESTINATION) {
            return back()->with(
                'error',
                'Maksimal '.self::MAX_PER_DESTINATION.' foto per destinasi. Saat ini sudah ada '.$existing.' foto.'
            );
        }

        foreach ($files as $file) {
            $path = $file->store('destinations/'.$destination->id, 'public');

            $destination->photos()->create([
                'image' => $path,
                'caption' => $request->validated('caption'),
            ]);
        }

        return back()->with('success', count($files).' foto diunggah.');
    }

    public function update(Request $request, Photo $photo): RedirectResponse
    {
        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        $photo->update(['caption' => $data['caption'] ?? null]);

        return back()->with('success', 'Keterangan foto diperbarui.');
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        Storage::disk('public')->delete($photo->image);
        $photo->delete();

        return back()->with('success', 'Foto dihapus.');
    }
}