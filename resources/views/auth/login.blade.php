<x-guest-layout>
    <!-- Logo / Título -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-wider">ITAL FIAT</h1>
        <p class="text-gray-500 text-sm mt-2">Sistema de Gestión de Repuestos</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Campo Email -->
        <div>
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="admin@italfiat.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Campo Contraseña -->
        <div class="mt-4">
            <div class="flex justify-between items-center">
                <x-input-label for="password" value="Contraseña" />

                @if (Route::has('password.request'))
                    <a class="text-xs text-blue-600 hover:underline" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Recordar Sesión -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
            </label>
        </div>

        <!-- Botón de Ingreso -->
        <div class="mt-6">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-300">
                Iniciar Sesión
            </button>
        </div>
    </form>
</x-guest-layout>
