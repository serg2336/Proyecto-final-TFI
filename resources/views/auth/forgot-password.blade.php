@extends('layouts.auth')

@section('titulo_documento', 'Recuperar contraseña - ITAL FIAT')
@section('eyebrow', 'Recuperar acceso')

@section('titulo')
    ¿Olvidaste tu <span class="accent">contraseña</span>?
@endsection

@section('lead', 'Ingresá tu correo y te enviamos un enlace para elegir una nueva.')

@section('content')
    @if (session('status'))
        <div class="app-login-status" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="app-login-form">
        @csrf

        <p class="app-form-legend">Correo de la cuenta</p>

        <div class="app-field">
            <label for="email">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="nombre@italfiat.com"
                autocomplete="username"
                @class(['is-invalid' => $errors->has('email')])
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                required
                autofocus
            >
            @error('email')
                <p class="app-field-error" id="email-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="app-login-btn">Enviar enlace</button>

        <p class="app-auth-alt">
            <a href="{{ route('login') }}" class="app-login-link">Volver al inicio de sesión</a>
        </p>
    </form>
@endsection
