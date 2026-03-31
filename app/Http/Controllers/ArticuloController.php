<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function index()
    {
        $articulos = Articulo::paginate(15);
        return view('articulos.index', compact('articulos'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $articulos = Articulo::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('codigo', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'nombre', 'nombre_mostrar', 'codigo', 'precio', 'descripcion']);

        return response()->json($articulos);
    }

    public function create()
    {
        return view('articulos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:articulos',
            'nombre' => 'required|string|max:255',
            'nombre_mostrar' => 'nullable|string|max:255',
            'precio' => 'required|numeric|min:0',
            'com_origen' => 'required|numeric|min:0|max:100',
            'com_destino' => 'required|numeric|min:0|max:100',
        ]);

        $articulo = Articulo::create($request->only(['codigo', 'nombre', 'nombre_mostrar', 'descripcion', 'precio', 'com_origen', 'com_destino']));

        return redirect()->route('articulos.index')->with('success', 'Artículo creado correctamente.');
    }

    public function edit(Articulo $articulo)
    {
        return view('articulos.edit', compact('articulo'));
    }

    public function update(Request $request, Articulo $articulo)
    {
        $request->validate([
            'codigo' => 'required|string|unique:articulos,codigo,' . $articulo->id,
            'nombre' => 'required|string|max:255',
            'nombre_mostrar' => 'nullable|string|max:255',
            'precio' => 'required|numeric|min:0',
            'com_origen' => 'required|numeric|min:0|max:100',
            'com_destino' => 'required|numeric|min:0|max:100',
        ]);

        $articulo->update($request->only(['codigo', 'nombre', 'nombre_mostrar', 'descripcion', 'precio', 'com_origen', 'com_destino']));

        return redirect()->route('articulos.index')->with('success', 'Artículo actualizado correctamente.');
    }

    public function destroy(Articulo $articulo)
    {
        $articulo->delete();
        return redirect()->route('articulos.index')->with('success', 'Artículo eliminado correctamente.');
    }
}