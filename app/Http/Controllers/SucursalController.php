<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\Empresa;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::with('empresa')->paginate(15);
        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        $empresas = Empresa::all();
        return view('sucursales.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:191',
            'empresa_id' => 'required|exists:empresas,id',
            'domicilio' => 'required|string|max:191',
            'telefono' => 'required|string|max:191',
            'localidad' => 'required|string|max:191',
            'puntoventa' => 'required|string|max:191',
            'fecha_inicio_actividades' => 'required|date',
            'produccion' => 'integer|min:0',
            'alias_cbu' => 'nullable|string|max:200',
            'tope_lineas_factura' => 'integer|min:1',
            'redondeo' => 'integer',
            'decimales' => 'integer|min:0',
            'depositos' => 'nullable|string|max:191',
            'edita_remitos' => 'boolean',
            'max_botones_articulos' => 'integer|min:0',
            'listasprecios' => 'nullable|string'
        ]);

        $data['edita_remitos'] = $request->has('edita_remitos') ? 1 : 0;

        Sucursal::create($data);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal creada exitosamente.');
    }

    public function edit(Sucursal $sucursale) // Laravel routing default might put variable as $sucursale or $sucursal. Let's use $sucursal and explicitly bind it in route if needed or adapt variable name. In web.php standard resource it might use $sucursale. Let's name it $sucursal just in case.

    {
        $empresas = Empresa::all();
        return view('sucursales.edit', ['sucursal' => $sucursale, 'empresas' => $empresas]);
    }

    public function update(Request $request, Sucursal $sucursale)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:191',
            'empresa_id' => 'required|exists:empresas,id',
            'domicilio' => 'required|string|max:191',
            'telefono' => 'required|string|max:191',
            'localidad' => 'required|string|max:191',
            'puntoventa' => 'required|string|max:191',
            'fecha_inicio_actividades' => 'required|date',
            'produccion' => 'integer|min:0',
            'alias_cbu' => 'nullable|string|max:200',
            'tope_lineas_factura' => 'integer|min:1',
            'redondeo' => 'integer',
            'decimales' => 'integer|min:0',
            'depositos' => 'nullable|string|max:191',
            'edita_remitos' => 'boolean',
            'max_botones_articulos' => 'integer|min:0',
            'listasprecios' => 'nullable|string'
        ]);

        $data['edita_remitos'] = $request->has('edita_remitos') ? 1 : 0;

        $sucursale->update($data);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursale)
    {
        $sucursale->delete();
        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada exitosamente.');
    }
}