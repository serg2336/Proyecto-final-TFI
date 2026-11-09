<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo_pagina', 'ITAL FIAT')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')
</head>
<body class="app-dark h-full">

    <div class="app-strip" aria-hidden="true"></div>

    <header class="app-header">
        <div class="app-wrap app-header-inner">
            <a href="{{ url('/') }}" class="app-brand">
                <span class="app-brand-mark">IF</span>
                <span class="app-brand-text">
                    <strong>ITAL FIAT</strong>
                    <small>Repuestos &amp; Stock</small>
                </span>
            </a>

            <nav class="app-nav">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'is-active' : '' }}">Dashboard</a>
                <a href="{{ url('/productos') }}" class="{{ request()->is('productos*') ? 'is-active' : '' }}">Productos</a>
                <a href="{{ url('/stock') }}" class="{{ request()->is('stock*') ? 'is-active' : '' }}">Stock</a>
                <a href="{{ url('/pedidos') }}" class="{{ request()->is('pedidos*') ? 'is-active' : '' }}">Pedidos</a>
                <a href="{{ url('/ventas') }}" class="{{ request()->is('ventas*') ? 'is-active' : '' }}">Ventas</a>
                <a href="{{ url('/clientes') }}" class="{{ request()->is('clientes*') ? 'is-active' : '' }}">Clientes</a>
                <a href="{{ url('/reportes') }}" class="{{ request()->is('reportes*') ? 'is-active' : '' }}">Reportes</a>
            </nav>

            <div class="app-header-right">
                <div class="app-user">
                    <span class="app-avatar">{{ collect(explode(' ', str_replace(',', '', auth()->user()->name ?? 'Usuario')))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}</span>
                    <span class="app-user-meta">
                        <strong>{{ auth()->user()->name ?? 'Usuario' }}</strong>
                        <small>Administrador</small>
                    </span>
                </div>

                <form action="{{ url('/logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="app-logout">Salir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="app-wrap app-main">
        @yield('content')
    </main>

    <footer class="app-footer">
        <div class="app-wrap app-footer-inner">
            <span><strong>ITAL FIAT</strong> · Formosa, Argentina</span>
            <span>Sistema de gestión de repuestos</span>
        </div>
    </footer>

</body>
</html>
