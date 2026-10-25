<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestinationRequest;
use App\Models\Category;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->query('q');
        $q = is_string($q) ? trim($q) : '';

        $categoryId = $request->query('kategori');
        $categoryId = is_numeric($categoryId) ? (int) $categoryId : 0;

        $destinations = Destination::query()
            ->with('category')
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%'.$q.'%'))
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.destinations.index', [
            'destinations' => $destinations,
            'categories' => Category::orderBy('name')->get(),
            'q' => $q,
            'categoryId' => $categoryId > 0 ? $categoryId : '',
        ]);
    }

    public function create(): View
    {
        return view('admin.destinations.create', $this->formData(new Destination()));
    }

    public function store(DestinationRequest $request): RedirectResponse
    {
        $destination = Destination::create($request->validated());

        return redirect()
            ->route('admin.destinations.edit', $destination)
            ->with('success', 'Destinasi ditambahkan.');
    }

    public function edit(Destination $destination): View
    {
        return view('admin.destinations.edit', $this->formData($destination));
    }

    public function update(DestinationRequest $request, Destination $destination): RedirectResponse
    {
        $destination->update($request->validated());

        return back()->with('success', 'Destinasi diperbarui.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $destination->delete();

        return redirect()
            ->route('admin.destinations.index')
            ->with('success', 'Destinasi dihapus.');
    }

    private function formData(Destination $destination): array
    {
        return [
            'destination' => $destination,
            'categories' => Category::orderBy('name')->get(),
            'types' => Destination::query()->whereNotNull('tourism_type')->distinct()->orderBy('tourism_type')->pluck('tourism_type'),
            'atmospheres' => Destination::query()->whereNotNull('atmosphere')->distinct()->orderBy('atmosphere')->pluck('atmosphere'),
            'accessibilities' => Destination::query()->whereNotNull('accessibility')->distinct()->orderBy('accessibility')->pluck('accessibility'),
        ];
    }
}