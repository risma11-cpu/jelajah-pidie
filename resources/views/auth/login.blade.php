@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <div class="card" style="max-width:26rem;margin:2rem auto">
        <span class="hand">selamat datang kembali</span>
        <h1 class="section-title" style="margin-top:0">Masuk</h1>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field">
                <label class="label" for="email">Email</label>
                <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
                @error('email')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label class="label" for="password">Password</label>
                <input id="password" class="input" type="password" name="password" required autocomplete="current-password">
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <label class="text-sm" style="display:flex;gap:0.5rem;align-items:center;margin-bottom:1rem">
                <input type="checkbox" name="remember" value="1"> Ingat saya
            </label>
            <button type="submit" class="btn btn-primary" style="width:100%">Masuk</button>
        </form>

        <p class="muted text-sm" style="margin-top:1rem">Belum punya akun? <a href="{{ route('register') }}"><u>Daftar</u></a></p>
    </div>
@endsection