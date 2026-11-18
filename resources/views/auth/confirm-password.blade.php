@extends('layouts.auth')

@section('titulo_documento', 'Confirmar contraseña - ITAL FIAT')
@section('eyebrow', 'Área segura')

@section('titulo')
    Confirmá tu <span class="accent">identidad</span>
@endsection

@section('lead', 'Por seguridad, ingresá tu contraseña antes de continuar.')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}" class="app-login-form">
        @csrf

        <p class="app-form-legend">Contraseña actual</p>

        <div class="app-field">
            <label for="password">Contraseña</label>
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
                    autofocus
                >
                @include('partials.password-toggle')
            </div>
            @error('password')
                <p class="app-field-error" id="password-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="app-login-btn">Confirmar</button>

        <p class="app-auth-alt">
            <a href="{{ route('dashboard') }}" class="app-login-link">Volver al panel</a>
        </p>
    </form>
@endsection
