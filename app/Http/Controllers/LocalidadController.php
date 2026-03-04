<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use Illuminate\Http\Request;

class LocalidadController extends Controller
{
    public function index()
    {
        $localidades = Localidad::orderBy('nombre')->paginate(15);
        return view('localidades.index', compact('localidades'));
    }

    public function create()
    {
        return view('localidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:localidades',
        ]);

        Localidad::create($request->all());

        return redirect()->route('localidades.index')->with('success', 'Localidad creada correctamente.');
    }

    public function edit(Localidad $localidad)
    {
        return view('localidades.edit', compact('localidad'));
    }

    public function update(Request $request, Localidad $localidad)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:localidades,nombre,' . $localidad->id,
        ]);

        $localidad->update($request->all());

        return redirect()->route('localidades.index')->with('success', 'Localidad actualizada correctamente.');
    }

    public function destroy(Localidad $localidad)
    {
        try {
            $localidad->delete();
            return redirect()->route('localidades.index')->with('success', 'Localidad eliminada correctamente.');
        }
        catch (\Exception $e) {
            return redirect()->route('localidades.index')->with('error', 'No se puede eliminar la localidad porque está siendo utilizada.');
        }
    }
}