@extends('layouts.admin')

@section('title', 'Moderasi Ulasan')

@section('content')
    <div>
        <h1 class="chrome-text">Moderasi Ulasan</h1>
        <p class="page-sub">&gt; SETUJUI ATAU TOLAK_</p>
    </div>

    <x-win title="ULASAN.exe">
        <div class="flex flex-wrap gap-2" style="margin-bottom:1rem">
            <a class="btn btn-sm {{ $status === 'pending' ? 'btn-pink' : '' }}" href="{{ route('admin.reviews.index', ['status' => 'pending']) }}">Menunggu</a>
            <a class="btn btn-sm {{ $status === 'approved' ? 'btn-pink' : '' }}" href="{{ route('admin.reviews.index', ['status' => 'approved']) }}">Disetujui</a>
            <a class="btn btn-sm {{ $status === 'rejected' ? 'btn-pink' : '' }}" href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}">Ditolak</a>
            <a class="btn btn-sm {{ $status === 'all' ? 'btn-pink' : '' }}" href="{{ route('admin.reviews.index', ['status' => 'all']) }}">Semua</a>
        </div>

        <div class="table-wrap">
            @if ($reviews->isEmpty())
                <p>Tidak ada ulasan pada status ini.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>DESTINASI</th>
                            <th>PENGGUNA</th>
                            <th>RATING</th>
                            <th>KOMENTAR</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reviews as $review)
                            <tr>
                                <td>{{ $review->destination->name ?? '-' }}</td>
                                <td>{{ $review->user->name ?? '-' }}<br><span class="hint">{{ $review->user->email ?? '' }}</span></td>
                                <td>{{ $review->rating }}/5</td>
                                <td>{{ $review->comment ?? '-' }}</td>
                                <td>
                                    @if ($review->status === 'approved')
                                        <span class="badge badge-ok">Disetujui</span>
                                    @elseif ($review->status === 'rejected')
                                        <span class="badge badge-bad">Ditolak</span>
                                    @else
                                        <span class="badge badge-warn">Menunggu</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button class="btn btn-lime btn-sm" type="submit">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button class="btn btn-sm" type="submit">Tolak</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Hapus ulasan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{ $reviews->links() }}
    </x-win>
@endsection