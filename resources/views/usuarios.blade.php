@use('App\Models\User')
@extends('layouts.app')

@section('titulo_pagina', 'Gestión de Usuarios')

@section('content')
    @php
        $editandoId = old('_editing_id', $editando?->id);
        $esEdicion = filled($editandoId);
    @endphp

    <!-- Encabezado con botón de crear -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Usuarios</h3>
            <p class="text-gray-500 text-sm">Administrá las cuentas del sistema y asigná el rol de cada persona.</p>
        </div>
        <button onclick="abrirModalCrear()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center gap-2">
            <span>➕</span> Nuevo Usuario
        </button>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('general'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">
            {{ $errors->first('general') }}
        </div>
    @endif

    <!-- Barra de Búsqueda y Filtros -->
    <form method="GET" action="{{ route('usuarios.index') }}" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4">
        <input
            type="search"
            name="buscar"
            value="{{ request('buscar') }}"
            placeholder="Buscar por nombre o correo..."
            class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        >

        <select name="rol" class="w-full md:w-1/4 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
            <option value="">Todos los roles</option>
            @foreach ($roles as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(request('rol') === $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>

        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
            Filtrar
        </button>

        @if (request()->filled('buscar') || request()->filled('rol'))
            <a href="{{ route('usuarios.index') }}" class="text-sm text-gray-500 hover:text-gray-700 self-center underline">
                Limpiar filtros
            </a>
        @endif
    </form>

    <!-- Tabla de Usuarios -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-4 px-6 border-b">Nombre</th>
                    <th class="py-4 px-6 border-b">Correo</th>
                    <th class="py-4 px-6 border-b">Rol</th>
                    <th class="py-4 px-6 border-b">Alta</th>
                    <th class="py-4 px-6 border-b text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($usuarios as $usuario)
                    @php
                        $badge = match ($usuario->role) {
                            User::ROLE_ADMIN => 'bg-red-100 text-red-700',
                            User::ROLE_VENDEDOR => 'bg-green-100 text-green-700',
                            User::ROLE_DEPOSITO => 'bg-blue-100 text-blue-700',
                            default => 'bg-gray-100 text-gray-700',
                        };
                        $esUnoMismo = auth()->id() === $usuario->id;
                    @endphp
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="py-4 px-6 font-semibold">
                            <div class="flex items-center gap-2">
                                {{ $usuario->name }}
                                @if ($esUnoMismo)
                                    <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded">Vos</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-6">{{ $usuario->email }}</td>
                        <td class="py-4 px-6">
                            <span class="{{ $badge }} px-2 py-1 rounded text-xs font-bold">{{ $usuario->roleLabel() }}</span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500">
                            {{ $usuario->created_at?->locale('es')->diffForHumans() ?? '—' }}
                        </td>
                        <td class="py-4 px-6 text-center space-x-2">
                            <button
                                type="button"
                                class="text-blue-500 hover:text-blue-700 font-semibold text-sm"
                                data-id="{{ $usuario->id }}"
                                data-name="{{ $usuario->name }}"
                                data-email="{{ $usuario->email }}"
                                data-role="{{ $usuario->role }}"
                                onclick="abrirModalEditar(this)"
                            >
                                Editar
                            </button>

                            @if ($esUnoMismo)
                                <span class="text-gray-400 text-sm" title="No podés eliminar tu propio usuario.">Eliminar</span>
                            @else
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="inline"
                                      onsubmit="return confirm('¿Eliminar el usuario {{ $usuario->email }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 px-6 text-center text-gray-500">
                            @if (request()->filled('buscar') || request()->filled('rol'))
                                <p class="font-semibold text-gray-700">No se encontraron usuarios</p>
                                <p class="text-sm">Probá con otro término o revisá los filtros aplicados.</p>
                            @else
                                <p class="font-semibold text-gray-700">Todavía no hay usuarios</p>
                                <p class="text-sm">Cuando agregues el primero, va a aparecer acá.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pie de tabla -->
        <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between">
            <span class="text-sm text-gray-600">Mostrando {{ $usuarios->count() }} usuario(s)</span>
        </div>
    </div>

    <!-- MODAL DE ALTA / EDICIÓN DE USUARIO -->
    <div id="modalUsuario" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <!-- Encabezado del Modal -->
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 id="usuario-modal-title" class="text-xl font-bold text-gray-800">
                    {{ $esEdicion ? 'Editar Usuario' : 'Registrar Nuevo Usuario' }}
                </h3>
                <button onclick="cerrarModal()" class="text-gray-500 hover:text-red-500 text-2xl font-bold">&times;</button>
            </div>

            <!-- Cuerpo del Formulario -->
            <form
                id="usuario-form"
                action="{{ $esEdicion ? route('usuarios.update', $editandoId) : route('usuarios.store') }}"
                method="POST"
                class="mt-4"
            >
                @csrf
                <input type="hidden" name="_method" id="usuario-method" value="{{ $esEdicion ? 'PUT' : 'POST' }}">
                <input type="hidden" name="_editing_id" value="{{ $editandoId }}">

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="usuario-name" class="block text-sm font-semibold text-gray-700 mb-1">Nombre y apellido</label>
                        <input
                            id="usuario-name"
                            type="text"
                            name="name"
                            value="{{ old('name', $editando?->name) }}"
                            placeholder="Ej: Gómez, Ana"
                            class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label for="usuario-email" class="block text-sm font-semibold text-gray-700 mb-1">Correo electrónico</label>
                        <input
                            id="usuario-email"
                            type="email"
                            name="email"
                            value="{{ old('email', $editando?->email) }}"
                            placeholder="usuario@italfiat.com"
                            class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none"
                            required
                        >
                    </div>
                    <div>
                        <label for="usuario-role" class="block text-sm font-semibold text-gray-700 mb-1">Rol</label>
                        <select
                            id="usuario-role"
                            name="role"
                            class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none"
                            required
                        >
                            @foreach ($roles as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('role', $editando?->role ?? User::ROLE_VENDEDOR) === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="usuario-password" class="block text-sm font-semibold text-gray-700 mb-1">Contraseña</label>
                        <input
                            id="usuario-password"
                            type="password"
                            name="password"
                            class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none"
                            autocomplete="new-password"
                            @if (! $esEdicion) required @endif
                        >
                        <p id="usuario-password-help" class="text-xs text-gray-500 mt-1">
                            {{ $esEdicion ? 'Dejá la contraseña en blanco para mantener la actual.' : 'Mínimo 8 caracteres.' }}
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label for="usuario-password-confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirmar contraseña</label>
                        <input
                            id="usuario-password-confirmation"
                            type="password"
                            name="password_confirmation"
                            class="w-full px-3 py-2 border rounded focus:ring-blue-500 focus:outline-none"
                            autocomplete="new-password"
                            @if (! $esEdicion) required @endif
                        >
                    </div>
                </div>

                <!-- Botones del Formulario -->
                <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                    <button type="button" onclick="cerrarModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded transition">
                        Cancelar
                    </button>
                    <button id="usuario-submit" type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition shadow">
                        {{ $esEdicion ? 'Guardar Cambios' : 'Guardar Usuario' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalUsuario = document.getElementById('modalUsuario');
        const usuarioForm = document.getElementById('usuario-form');
        const usuarioMethod = document.getElementById('usuario-method');
        const usuarioTitulo = document.getElementById('usuario-modal-title');
        const usuarioSubmit = document.getElementById('usuario-submit');
        const usuarioPassword = document.getElementById('usuario-password');
        const usuarioPasswordConfirm = document.getElementById('usuario-password-confirmation');
        const usuarioPasswordHelp = document.getElementById('usuario-password-help');

        const urlCrear = @json(route('usuarios.store'));
        const urlEditar = @json(route('usuarios.update', ['user' => '__ID__']));

        function abrirModalCrear() {
            usuarioForm.reset();
            usuarioForm.action = urlCrear;
            usuarioMethod.value = 'POST';
            usuarioTitulo.textContent = 'Registrar Nuevo Usuario';
            usuarioSubmit.textContent = 'Guardar Usuario';
            usuarioPassword.required = true;
            usuarioPasswordConfirm.required = true;
            usuarioPasswordHelp.textContent = 'Mínimo 8 caracteres.';
            modalUsuario.classList.remove('hidden');
        }

        function abrirModalEditar(boton) {
            usuarioForm.reset();
            usuarioForm.action = urlEditar.replace('__ID__', boton.dataset.id);
            usuarioMethod.value = 'PUT';
            usuarioTitulo.textContent = 'Editar Usuario';
            usuarioSubmit.textContent = 'Guardar Cambios';
            usuarioPassword.required = false;
            usuarioPasswordConfirm.required = false;
            usuarioPasswordHelp.textContent = 'Dejá la contraseña en blanco para mantener la actual.';

            usuarioForm.elements['name'].value = boton.dataset.name;
            usuarioForm.elements['email'].value = boton.dataset.email;
            usuarioForm.elements['role'].value = boton.dataset.role;

            modalUsuario.classList.remove('hidden');
        }

        function cerrarModal() {
            modalUsuario.classList.add('hidden');
        }

        // Si la validación del backend falla, reabrimos el modal con los errores.
        @if ($errors->any())
            modalUsuario.classList.remove('hidden');
        @endif
    </script>
@endsection
