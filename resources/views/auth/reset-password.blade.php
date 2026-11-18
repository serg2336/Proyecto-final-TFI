@extends('layouts.auth')

@section('titulo_documento', 'Nueva contraseña - ITAL FIAT')
@section('eyebrow', 'Nueva contraseña')

@section('titulo')
    Elegí una <span class="accent">contraseña nueva</span>
@endsection

@section('lead', 'Definí una contraseña segura para volver a entrar a tu cuenta.')

@section('content')
    <form method="POST" action="{{ route('password.store') }}" class="app-login-form">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <p class="app-form-legend">Datos de acceso</p>

        <div class="app-field">
            <label for="email">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $request->email) }}"
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

        <div class="app-field">
            <label for="password">Contraseña nueva</label>
            <div class="app-input-wrap">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Mínimo 8 caracteres"
                    autocomplete="new-password"
                    @class(['has-toggle', 'is-invalid' => $errors->has('password')])
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                    required
                >
                @include('partials.password-toggle')
            </div>
            @error('password')
                <p class="app-field-error" id="password-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="app-field">
            <label for="password_confirmation">Confirmá la contraseña</label>
            <div class="app-input-wrap">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Repetí la contraseña"
                    autocomplete="new-password"
                    @class(['has-toggle', 'is-invalid' => $errors->has('password_confirmation')])
                    @error('password_confirmation') aria-invalid="true" aria-describedby="password-confirmation-error" @enderror
                    required
                >
                @include('partials.password-toggle')
            </div>
            @error('password_confirmation')
                <p class="app-field-error" id="password-confirmation-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="app-login-btn">Guardar contraseña</button>

        <p class="app-auth-alt">
            <a href="{{ route('login') }}" class="app-login-link">Volver al inicio de sesión</a>
        </p>
    </form>
@endsection
