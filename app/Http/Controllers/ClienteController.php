<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes', compact('clientes')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
        ]);
        Cliente::create($request->all());
        return redirect()->back()->with('success', 'Cliente guardado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->back()->with('success', 'Cliente eliminado.');
    }
}