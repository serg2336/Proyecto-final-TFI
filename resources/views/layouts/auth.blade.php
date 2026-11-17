<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo_documento', 'ITAL FIAT - Repuestos & Stock')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')
</head>
<body class="app-dark app-login h-full">

    <div class="app-strip" aria-hidden="true"></div>

    <main class="app-login-main">
        <section class="app-login-card">

            @include('partials.auth-wheel')

            <p class="app-eyebrow app-eyebrow-center"><span class="tick"></span>@yield('eyebrow', 'Acceso al sistema')</p>
            <h1 class="app-login-title">@yield('titulo')</h1>
            <p class="app-login-lead">@yield('lead')</p>

            @yield('content')

            <div class="app-login-foot">
                &copy; {{ date('Y') }} ITAL FIAT · Formosa, Argentina
            </div>
        </section>
    </main>

</body>
</html>
