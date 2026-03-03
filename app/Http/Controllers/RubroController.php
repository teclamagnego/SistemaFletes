<?php

namespace App\Http\Controllers;

use App\Models\Rubro;
use Illuminate\Http\Request;

class RubroController extends Controller
{
    public function index()
    {
        $rubros = Rubro::paginate(15);
        return view('rubros.index', compact('rubros'));
    }

    public function create()
    {
        return view('rubros.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        Rubro::create($request->all());
        return redirect()->route('rubros.index')->with('success', 'Rubro creado correctamente.');
    }

    public function edit(Rubro $rubro)
    {
        return view('rubros.edit', compact('rubro'));
    }

    public function update(Request $request, Rubro $rubro)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $rubro->update($request->all());
        return redirect()->route('rubros.index')->with('success', 'Rubro actualizado correctamente.');
    }

    public function destroy(Rubro $rubro)
    {
        $rubro->delete();
        return redirect()->route('rubros.index')->with('success', 'Rubro eliminado correctamente.');
    }
}