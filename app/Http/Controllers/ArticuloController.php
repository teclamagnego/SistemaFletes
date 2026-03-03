<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Rubro;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function index()
    {
        $articulos = Articulo::with(['rubros', 'proveedores'])->paginate(15);
        return view('articulos.index', compact('articulos'));
    }

    public function create()
    {
        $rubros = Rubro::all();
        $proveedores = Proveedor::all();
        return view('articulos.create', compact('rubros', 'proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:articulos',
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'rubros' => 'nullable|array',
            'proveedores' => 'nullable|array',
        ]);

        $articulo = Articulo::create($request->only(['codigo', 'nombre', 'descripcion', 'precio', 'stock']));

        if ($request->filled('rubros')) {
            $articulo->rubros()->sync($request->rubros);
        }

        if ($request->filled('proveedores')) {
            $pivotData = [];
            foreach ($request->proveedores as $proveedorId) {
                $precioCompra = $request->input("precio_compra.{$proveedorId}", null);
                $pivotData[$proveedorId] = ['precio_compra' => $precioCompra];
            }
            $articulo->proveedores()->sync($pivotData);
        }

        return redirect()->route('articulos.index')->with('success', 'Artículo creado correctamente.');
    }

    public function edit(Articulo $articulo)
    {
        $rubros = Rubro::all();
        $proveedores = Proveedor::all();
        $articulo->load(['rubros', 'proveedores']);
        return view('articulos.edit', compact('articulo', 'rubros', 'proveedores'));
    }

    public function update(Request $request, Articulo $articulo)
    {
        $request->validate([
            'codigo' => 'required|string|unique:articulos,codigo,' . $articulo->id,
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'rubros' => 'nullable|array',
            'proveedores' => 'nullable|array',
        ]);

        $articulo->update($request->only(['codigo', 'nombre', 'descripcion', 'precio', 'stock']));

        $articulo->rubros()->sync($request->rubros ?? []);

        $pivotData = [];
        if ($request->filled('proveedores')) {
            foreach ($request->proveedores as $proveedorId) {
                $precioCompra = $request->input("precio_compra.{$proveedorId}", null);
                $pivotData[$proveedorId] = ['precio_compra' => $precioCompra];
            }
        }
        $articulo->proveedores()->sync($pivotData);

        return redirect()->route('articulos.index')->with('success', 'Artículo actualizado correctamente.');
    }

    public function destroy(Articulo $articulo)
    {
        $articulo->delete();
        return redirect()->route('articulos.index')->with('success', 'Artículo eliminado correctamente.');
    }
}