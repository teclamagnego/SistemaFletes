<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::paginate(15);
        return view('agencies.index', compact('agencies'));
    }

    public function create()
    {
        return view('agencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:10|unique:agencies',
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email',
            'comision_porcentaje' => 'required|numeric|min:0|max:100',
        ]);

        Agency::create($request->all());

        return redirect()->route('agencies.index')->with('success', 'Agencia creada correctamente.');
    }

    public function edit(Agency $agency)
    {
        return view('agencies.edit', compact('agency'));
    }

    public function update(Request $request, Agency $agency)
    {
        $request->validate([
            'codigo' => 'required|string|max:10|unique:agencies,codigo,' . $agency->id,
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email',
            'comision_porcentaje' => 'required|numeric|min:0|max:100',
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