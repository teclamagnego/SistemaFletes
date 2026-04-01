<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use Illuminate\Http\Request;

class LocalidadController extends Controller
{
    public function index()
    {
        $localidades = Localidad::where('activo', 1)->orderBy('nombre')->paginate(15);
        return view('localidades.index', compact('localidades'));
    }

    public function search(Request $request)
    {
        $q = $request->query('q');
        $localidades = Localidad::where('activo', 1)
            ->where('nombre', 'LIKE', "%$q%")
            ->orderBy('nombre')
            ->limit(10)
            ->get();
        return response()->json($localidades);
    }

    public function destroyAjax(Localidad $localidad)
    {
        try {
            $localidad->update(['activo' => 0]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar la localidad: ' . $e->getMessage()
            ]);
        }
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
            $localidad->update(['activo' => 0]);
            return redirect()->route('localidades.index')->with('success', 'Localidad desactivada correctamente.');
        }
        catch (\Exception $e) {
            return redirect()->route('localidades.index')->with('error', 'Error al desactivar la localidad: ' . $e->getMessage());
        }
    }
}