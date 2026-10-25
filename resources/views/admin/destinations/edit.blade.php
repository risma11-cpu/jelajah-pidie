@extends('layouts.admin')

@section('title', 'Edit Destinasi')

@section('content')
    <div>
        <h1 class="chrome-text">Edit Destinasi</h1>
        <p class="page-sub">&gt; ID {{ $destination->id }}_</p>
    </div>

    <x-win title="FORM_DESTINASI.exe">
        @include('admin.destinations._form')
    </x-win>

    <x-win title="ZONA_BAHAYA.exe">
        <p style="margin-bottom:0.8rem">Menghapus destinasi juga menghapus foto, favorit, dan ulasannya.</p>
        <form method="POST" action="{{ route('admin.destinations.destroy', $destination) }}"
              onsubmit="return confirm('Yakin hapus destinasi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus destinasi</button>
        </form>
    </x-win>
@endsection