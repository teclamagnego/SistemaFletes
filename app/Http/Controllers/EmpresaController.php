<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\TipoIva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::paginate(15);
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        $tiposIva = TipoIva::all();
        return view('empresas.create', compact('tiposIva'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_fantasia' => 'required|string|max:191',
            'razon_social' => 'required|string|max:191',
            'cuit' => 'required|string|max:191',
            'tipoiva_id' => 'required|exists:tipos_iva,id',
            'activo' => 'boolean',
            'esagenteretencioniva' => 'boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('logo');
        $data['activo'] = $request->has('activo') ? 1 : 0;
        $data['esagenteretencioniva'] = $request->has('esagenteretencioniva') ? 1 : 0;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Empresa::create($data);

        return redirect()->route('empresas.index')->with('success', 'Empresa creada exitosamente.');
    }

    public function edit(Empresa $empresa)
    {
        $tiposIva = TipoIva::all();
        return view('empresas.edit', compact('empresa', 'tiposIva'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'nombre_fantasia' => 'required|string|max:191',
            'razon_social' => 'required|string|max:191',
            'cuit' => 'required|string|max:191',
            'tipoiva_id' => 'required|exists:tipos_iva,id',
            'activo' => 'boolean',
            'esagenteretencioniva' => 'boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('logo');
        $data['activo'] = $request->has('activo') ? 1 : 0;
        $data['esagenteretencioniva'] = $request->has('esagenteretencioniva') ? 1 : 0;

        if ($request->hasFile('logo')) {
            if ($empresa->logo) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa->update($data);

        return redirect()->route('empresas.index')->with('success', 'Empresa actualizada exitosamente.');
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->logo) {
            Storage::disk('public')->delete($empresa->logo);
        }
        $empresa->delete();
        return redirect()->route('empresas.index')->with('success', 'Empresa eliminada exitosamente.');
    }
}