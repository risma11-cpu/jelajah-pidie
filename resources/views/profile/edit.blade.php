@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <span class="hand">akun kamu</span>
    <h1 class="section-title" style="margin-top:0">Profil Saya</h1>

    <div class="grid-cards" style="grid-template-columns:repeat(auto-fit,minmax(20rem,1fr))">
        <section class="card">
            <h2 style="font-size:1.2rem;margin-bottom:0.75rem">Data akun</h2>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="field">
                    <label class="label" for="name">Nama</label>
                    <input id="name" class="input" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="email">Email</label>
                    <input id="email" class="input" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </section>

        <section class="card">
            <h2 style="font-size:1.2rem;margin-bottom:0.75rem">Ganti password</h2>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')
                <div class="field">
                    <label class="label" for="current_password">Password saat ini</label>
                    <input id="current_password" class="input" type="password" name="current_password" required autocomplete="current-password">
                    @error('current_password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="new_password">Password baru</label>
                    <input id="new_password" class="input" type="password" name="password" required autocomplete="new-password">
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label class="label" for="new_password_confirmation">Konfirmasi password baru</label>
                    <input id="new_password_confirmation" class="input" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary">Ganti password</button>
            </form>
        </section>
    </div>
@endsection