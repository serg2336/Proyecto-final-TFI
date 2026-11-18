@extends('layouts.auth')

@section('titulo_documento', 'Verificar correo - ITAL FIAT')
@section('eyebrow', 'Verificación')

@section('titulo')
    Verificá tu <span class="accent">correo</span>
@endsection

@section('lead', 'Estamos casi listos para que empieces a usar el sistema.')

@section('content')
    <div class="app-auth-note">
        <p>
            Te enviamos un correo con un enlace de verificación.
            Hacé clic en ese enlace para activar tu cuenta.
        </p>
        <p>
            Si no lo recibiste, revisá la carpeta de spam o pedí uno nuevo.
        </p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="app-login-status" role="status">
            Te enviamos un nuevo enlace de verificación.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="app-login-form">
        @csrf

        <button type="submit" class="app-login-btn">Reenviar correo</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="app-auth-logout">
        @csrf

        <button type="submit" class="app-auth-link-button">Cerrar sesión</button>
    </form>
@endsection
