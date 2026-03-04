<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Localidad;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::with('localidad')->paginate(15);
        return view('agencies.index', compact('agencies'));
    }

    public function create()
    {
        $localidades = Localidad::orderBy('nombre')->get();
        return view('agencies.create', compact('localidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:10|unique:agencies',
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email',
            'localidad_id' => 'required|exists:localidades,id',
            'com_origen' => 'required|numeric|min:0|max:100',
            'com_destino' => 'required|numeric|min:0|max:100',
        ]);

        Agency::create($request->all());

        return redirect()->route('agencies.index')->with('success', 'Agencia creada correctamente.');
    }

    public function edit(Agency $agency)
    {
        $localidades = Localidad::orderBy('nombre')->get();
        return view('agencies.edit', compact('agency', 'localidades'));
    }

    public function update(Request $request, Agency $agency)
    {
        $request->validate([
            'codigo' => 'required|string|max:10|unique:agencies,codigo,' . $agency->id,
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email',
            'localidad_id' => 'required|exists:localidades,id',
            'com_origen' => 'required|numeric|min:0|max:100',
            'com_destino' => 'required|numeric|min:0|max:100',
        ]);

        $agency->update($request->all());

        return redirect()->route('agencies.index')->with('success', 'Agencia actualizada correctamente.');
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();
        return redirect()->route('agencies.index')->with('success', 'Agencia eliminada correctamente.');
    }
}