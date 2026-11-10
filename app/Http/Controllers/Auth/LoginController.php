<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de login.
     */
    public function create(): View
    {
        return view('login');
    }

    /**
     * Intentar autenticar al usuario (valida email + contraseña hasheada).
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'La combinación de correo y contraseña no coincide.',
            ]);
        }

        $request->session()->regenerate();

        // HU1: cada rol entra directo a su módulo.
        return redirect($request->user()->homePath());
    }

    /**
     * Cerrar sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
