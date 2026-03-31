<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use Illuminate\Http\Request;

class CarrierController extends Controller
{
    public function index()
    {
        $carriers = Carrier::paginate(15);
        return view('carriers.index', compact('carriers'));
    }

    public function create()
    {
        return view('carriers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        Carrier::create($request->all());

        return redirect()->route('carriers.index')->with('success', 'Transportista creado correctamente.');
    }

    public function edit(Carrier $carrier)
    {
        return view('carriers.edit', compact('carrier'));
    }

    public function update(Request $request, Carrier $carrier)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        $carrier->update($request->all());

        return redirect()->route('carriers.index')->with('success', 'Transportista actualizado correctamente.');
    }

    public function destroy(Carrier $carrier)
    {
        $carrier->delete();
        return redirect()->route('carriers.index')->with('success', 'Transportista eliminado correctamente.');
    }
}