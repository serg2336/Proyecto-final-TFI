<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - ITAL FIAT</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen font-sans antialiased">

    <!-- Tarjeta del Login -->
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md border-t-4 border-blue-600">
        
        <!-- Logo / Título -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-wider">ITAL FIAT</h1>
            <p class="text-gray-500 text-sm mt-2">Sistema de Gestión de Repuestos</p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('login') }}" method="POST">
            @csrf <!-- Directiva de seguridad contra ataques CSRF -->

            <!-- Error de autenticación -->
            @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <!-- Campo Email / Usuario -->
            <div class="mb-5">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@italfiat.com" autocomplete="username" autofocus class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
            </div>

            <!-- Campo Contraseña -->
            <div class="mb-5">
                <div class="flex justify-between items-center mb-1">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                    <a href="#" class="text-xs text-blue-600 hover:underline">¿Olvidaste tu contraseña?</a>
                </div>
                <input type="password" id="password" name="password" autocomplete="current-password" placeholder="••••••••" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
            </div>

            <!-- Botón de Ingreso -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-300 flex justify-center items-center gap-2">
                Iniciar Sesión
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-400">
            &copy; 2024 ITAL FIAT - Formosa, Argentina.
        </div>
    </div>

</body>
</html> 