<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - ITAL FIAT</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite('resources/css/app.css')
</head>
<body class="app-dark app-login h-full">

    <div class="app-strip" aria-hidden="true"></div>

    <main class="app-login-main">
        <section class="app-login-card">

            <!-- Rueda animada con el sello ITAL FIAT -->
            <div class="app-wheel" aria-hidden="true">
                <svg class="app-gear" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="5">
                    <circle cx="100" cy="100" r="62"/>
                    <circle cx="100" cy="100" r="24"/>
                    <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(0 100 100)"/>
                    <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(45 100 100)"/>
                    <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(90 100 100)"/>
                    <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(135 100 100)"/>
                    <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(180 100 100)"/>
                    <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(225 100 100)"/>
                    <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(270 100 100)"/>
                    <rect x="93" y="12" width="14" height="24" rx="2" transform="rotate(315 100 100)"/>
                </svg>

                <div class="app-stamp">
                    <div>
                        <strong>ITAL<br>FIAT</strong>
                        <span>Repuestos</span>
                    </div>
                </div>
            </div>

            <!-- Título -->
            <p class="app-eyebrow app-eyebrow-center"><span class="tick"></span>Sistema de gestión</p>
            <h1 class="app-login-title">ITAL <span class="accent">FIAT</span></h1>
            <p class="app-login-lead">Ingresá con tu cuenta para administrar repuestos, stock y ventas.</p>

            <!-- Formulario -->
            <form action="{{ route('login') }}" method="POST" class="app-login-form">
                @csrf

                @if ($errors->any())
                    <div class="app-login-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="app-field">
                    <label for="email">Correo electrónico</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="admin@italfiat.com"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="app-field">
                    <div class="app-field-row">
                        <label for="password">Contraseña</label>
                        <a href="#" class="app-login-link">¿Olvidaste tu contraseña?</a>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="app-login-btn">Iniciar sesión</button>
            </form>

            <div class="app-login-foot">
                &copy; {{ date('Y') }} ITAL FIAT · Formosa, Argentina
            </div>
        </section>
    </main>

</body>
</html>
