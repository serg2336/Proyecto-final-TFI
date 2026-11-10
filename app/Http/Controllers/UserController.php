<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Roles disponibles para asignar en la interfaz.
     *
     * @return array<string, string>
     */
    private function roles(): array
    {
        return [
            User::ROLE_ADMIN => 'Administrador',
            User::ROLE_VENDEDOR => 'Vendedor',
            User::ROLE_DEPOSITO => 'Depósito',
        ];
    }

    /**
     * Listado de usuarios con buscador y filtro por rol.
     */
    public function index(Request $request)
    {
        $buscar = $request->query('buscar');
        $buscar = is_string($buscar) ? trim($buscar) : '';

        $rol = $request->query('rol');
        $rol = is_string($rol) ? trim($rol) : '';

        $usuarios = User::query()
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($query) use ($buscar) {
                    $query->where('name', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->when($rol !== '', fn ($query) => $query->where('role', $rol))
            ->orderBy('name')
            ->get();

        $roles = $this->roles();

        // Si la edición falló la validación, reabrimos el modal con los datos.
        $editandoId = session('usuario_editando');
        $editando = $editandoId ? User::find($editandoId) : null;

        return view('usuarios', compact('usuarios', 'roles', 'editando'));
    }

    /**
     * Alta de usuario.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresá un correo válido.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'role.required' => 'Seleccioná un rol.',
            'role.in' => 'El rol seleccionado no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        User::create($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Edición de usuario y asignación de rol.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(array_keys($this->roles()))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Ingresá un correo válido.',
            'email.unique' => 'Ya existe un usuario con ese correo.',
            'role.required' => 'Seleccioná un rol.',
            'role.in' => 'El rol seleccionado no es válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('usuario_editando', $user->id);
        }

        $data = $validator->validated();

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        // No permitir quedarse sin ningún administrador.
        if ($user->isAdmin() && $data['role'] !== User::ROLE_ADMIN && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()
                ->withErrors(['role' => 'Debe existir al menos un administrador.'])
                ->withInput()
                ->with('usuario_editando', $user->id);
        }

        $user->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Baja de usuario.
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['general' => 'No podés eliminar tu propio usuario.']);
        }

        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->withErrors(['general' => 'Debe existir al menos un administrador.']);
        }

        $user->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
