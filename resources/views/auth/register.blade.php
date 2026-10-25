@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <div class="card" style="max-width:26rem;margin:2rem auto">
        <span class="hand">ayo bergabung</span>
        <h1 class="section-title" style="margin-top:0">Daftar</h1>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="field">
                <label class="label" for="name">Nama</label>
                <input id="name" class="input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label class="label" for="email">Email</label>
                <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label class="label" for="password">Password (minimal 8 karakter)</label>
                <input id="password" class="input" type="password" name="password" required autocomplete="new-password">
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label class="label" for="password_confirmation">Konfirmasi password</label>
                <input id="password_confirmation" class="input" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-sun" style="width:100%">Buat akun</button>
        </form>

        <p class="muted text-sm" style="margin-top:1rem">Sudah punya akun? <a href="{{ route('login') }}"><u>Masuk</u></a></p>
    </div>
@endsection