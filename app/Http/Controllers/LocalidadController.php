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

    public function search(Request $request)
    {
        $q = $request->query('q');
        $localidades = Localidad::where('nombre', 'LIKE', "%$q%")
            ->orderBy('nombre')
            ->limit(10)
            ->get();
        return response()->json($localidades);
    }

    public function destroyAjax(Localidad $localidad)
    {
        try {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            $localidad->delete();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la localidad: ' . $e->getMessage()
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
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            $localidad->delete();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return redirect()->route('localidades.index')->with('success', 'Localidad eliminada correctamente.');
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            return redirect()->route('localidades.index')->with('error', 'Error al eliminar la localidad: ' . $e->getMessage());
        }
    }
}