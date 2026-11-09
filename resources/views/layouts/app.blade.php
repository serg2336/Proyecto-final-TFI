<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITAL FIAT - Sistema de Gestión</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 flex h-screen overflow-hidden">

    <!-- 1. SIDEBAR (MENÚ LATERAL) -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col hidden md:flex">
        <!-- Logo -->
        <div class="h-16 flex items-center justify-center border-b border-slate-800">
            <h1 class="text-xl font-bold tracking-wider text-blue-400">ITAL FIAT</h1>
        </div>

        <!-- Navegación -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            @php
                $linkBase = 'flex items-center gap-3 px-4 py-3 rounded-lg transition';
                $linkActivo = 'bg-blue-600 text-white';
                $linkInactivo = 'hover:bg-slate-800 text-gray-300 hover:text-white';
            @endphp

            <a href="{{ url('/') }}" class="{{ $linkBase }} {{ request()->is('/') ? $linkActivo : $linkInactivo }}">
                <span>📊</span> Dashboard
            </a>
            <a href="{{ url('/productos') }}" class="{{ $linkBase }} {{ request()->is('productos*') ? $linkActivo : $linkInactivo }}">
                <span>📦</span> Productos (repuestos)
            </a>
            <a href="{{ url('/stock') }}" class="{{ $linkBase }} {{ request()->is('stock*') ? $linkActivo : $linkInactivo }}">
                <span>📉</span> Control de Stock
            </a>
            <a href="{{ url('/pedidos') }}" class="{{ $linkBase }} {{ request()->is('pedidos*') ? $linkActivo : $linkInactivo }}">
                <span>🚚</span> Pedidos a Proveedores
            </a>
            <a href="{{ url('/ventas') }}" class="{{ $linkBase }} {{ request()->is('ventas*') ? $linkActivo : $linkInactivo }}">
                <span>💰</span> Ventas
            </a>
            <a href="{{ url('/clientes') }}" class="{{ $linkBase }} {{ request()->is('clientes*') ? $linkActivo : $linkInactivo }}">
                <span>👥</span> Clientes
            </a>
            <a href="{{ url('/reportes') }}" class="{{ $linkBase }} {{ request()->is('reportes*') ? $linkActivo : $linkInactivo }}">
                <span>📄</span> Reportes
            </a>
            <a href="#" class="{{ $linkBase }} {{ $linkInactivo }} mt-auto border-t border-slate-800 pt-4">
                <span>⚙️</span> Usuarios y Roles
            </a>
        </nav>
    </aside>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- 2. NAVBAR (BARRA SUPERIOR) -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-gray-500">☰</button>
                <h2 class="text-lg font-semibold text-gray-700">@yield('titulo_pagina', 'Panel de Control')</h2>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-500 font-semibold">Administrador</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                    {{ collect(explode(' ', str_replace(',', '', auth()->user()->name)))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('') }}
                </div>
                <form action="{{ url('/logout') }}" method="POST" class="ml-2">
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold">Salir</button>
                </form>
            </div>
        </header>

        <!-- 3. ÁREA CENTRAL (AQUÍ SE CARGA EL CONTENIDO) -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            @yield('content')
        </main>
    </div>

</body>
</html>