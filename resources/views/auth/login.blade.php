@extends('layouts.auth')

@section('titulo_documento', 'Iniciar Sesión - ITAL FIAT')
@section('eyebrow', 'Acceso al sistema')

@section('titulo')
    Bienvenido <span class="accent">de nuevo</span>
@endsection

@section('lead', 'Iniciá sesión para administrar tu catálogo.')

@section('content')
    @if (session('status'))
        <div class="app-login-status" role="status">{{ session('status') }}</div>
    @endif

    {{-- TASK-7: action route('login') + @csrf, igual que el POST de Breeze. --}}
    <form action="{{ route('login') }}" method="POST" class="app-login-form">
        @csrf

        <p class="app-form-legend">Correo y contraseña</p>

        <div class="app-field">
            <label for="email">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="admin@italfiat.com"
                autocomplete="username"
                @class(['is-invalid' => $errors->has('email')])
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                required
                autofocus
            >
            {{-- TASK-8: error dinámico del campo email. --}}
            @error('email')
                <p class="app-field-error" id="email-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="app-field">
            <div class="app-field-row">
                <label for="password">Contraseña</label>
                <a href="{{ route('password.request') }}" class="app-login-link">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="app-input-wrap">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    @class(['has-toggle', 'is-invalid' => $errors->has('password')])
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                    required
                >
                @include('partials.password-toggle')
            </div>
            {{-- TASK-8: error dinámico del campo contraseña. --}}
            @error('password')
                <p class="app-field-error" id="password-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="app-login-btn">Iniciar sesión</button>
    </form>
@endsection
