@extends('layouts.auth')

@section('titulo_documento', 'Crear cuenta - ITAL FIAT')
@section('eyebrow', 'Registro')

@section('titulo')
    Creá tu <span class="accent">cuenta</span>
@endsection

@section('lead', 'Completá tus datos para empezar a usar el sistema.')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="app-login-form">
        @csrf

        <p class="app-form-legend">Datos de la cuenta</p>

        <div class="app-field">
            <label for="name">Nombre y apellido</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Ej: Barcos, Lía"
                autocomplete="name"
                @class(['is-invalid' => $errors->has('name')])
                @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                required
                autofocus
            >
            @error('name')
                <p class="app-field-error" id="name-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

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
            >
            @error('email')
                <p class="app-field-error" id="email-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="app-field">
            <label for="password">Contraseña</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Mínimo 8 caracteres"
                autocomplete="new-password"
                @class(['is-invalid' => $errors->has('password')])
                @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                required
            >
            @error('password')
                <p class="app-field-error" id="password-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="app-field">
            <label for="password_confirmation">Confirmá la contraseña</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Repetí la contraseña"
                autocomplete="new-password"
                @class(['is-invalid' => $errors->has('password_confirmation')])
                @error('password_confirmation') aria-invalid="true" aria-describedby="password-confirmation-error" @enderror
                required
            >
            @error('password_confirmation')
                <p class="app-field-error" id="password-confirmation-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="app-login-btn">Crear cuenta</button>

        <p class="app-auth-alt">
            ¿Ya tenés cuenta?
            <a href="{{ route('login') }}" class="app-login-link">Iniciá sesión</a>
        </p>
    </form>
@endsection
