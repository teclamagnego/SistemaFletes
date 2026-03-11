<?php

namespace App\Http\Controllers;

use App\Models\FormaPago;
use Illuminate\Http\Request;

class FormaPagoController extends Controller
{
    public function index()
    {
        $formasPago = FormaPago::paginate(15);
        return view('formas_pago.index', compact('formasPago'));
    }

    public function create()
    {
        return view('formas_pago.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        FormaPago::create($request->all());

        return redirect()->route('formas_pago.index')->with('success', 'Forma de pago creada correctamente.');
    }

    public function edit(FormaPago $formaPago)
    {
        return view('formas_pago.edit', compact('formaPago'));
    }

    public function update(Request $request, FormaPago $formaPago)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $formaPago->update($request->all());

        return redirect()->route('formas_pago.index')->with('success', 'Forma de pago actualizada correctamente.');
    }

    public function destroy(FormaPago $formaPago)
    {
        $formaPago->delete();
        return redirect()->route('formas_pago.index')->with('success', 'Forma de pago eliminada correctamente.');
    }
}