<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use Illuminate\Http\Request;

class ChequeController extends Controller
{
    public function index(Request $request)
    {
        $query = Cheque::query()->with('cliente');

        if ($request->filled('numero')) {
            $query->where('numero', 'LIKE', "%{$request->numero}%");
        }

        if ($request->filled('cliente')) {
            $clienteSearch = $request->cliente;
            $query->whereHas('cliente', function ($q) use ($clienteSearch) {
                $q->where('nombre_fantasia', 'LIKE', "%{$clienteSearch}%")
                  ->orWhere('razon_social', 'LIKE', "%{$clienteSearch}%");
            });
        }

        $cheques = $query->latest()->paginate(15)->withQueryString();

        return view('cheques.index', compact('cheques'));
    }

    public function edit(Cheque $cheque)
    {
        return view('cheques.edit', compact('cheque'));
    }

    public function update(Request $request, Cheque $cheque)
    {
        $request->validate([
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
            'numero' => 'required|string',
            'observacion_origen' => 'nullable|string',
            'observacion_destino' => 'nullable|string',
        ]);

        $cheque->update($request->all());

        return redirect()->route('cheques.index')->with('success', 'Cheque actualizado correctamente.');
    }

    public function destroy(Cheque $cheque)
    {
        $cheque->delete();
        return redirect()->route('cheques.index')->with('success', 'Cheque eliminado correctamente.');
    }
}
