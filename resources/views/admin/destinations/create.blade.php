@extends('layouts.admin')

@section('title', 'Tambah Destinasi')

@section('content')
    <div>
        <h1 class="chrome-text">Tambah Destinasi</h1>
        <p class="page-sub">&gt; DATA BARU_</p>
    </div>

    <x-win title="FORM_DESTINASI.exe">
        @include('admin.destinations._form')
    </x-win>
@endsection