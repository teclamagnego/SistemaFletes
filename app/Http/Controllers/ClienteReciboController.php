<?php

namespace App\Http\Controllers;

use App\Models\ClienteRecibo;
use App\Models\Cliente;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class ClienteReciboController extends Controller
{
    public function index()
    {
        $recibos = ClienteRecibo::with(['cliente', 'formaPago'])->latest()->paginate(15);
        return view('cliente_recibos.index', compact('recibos'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nombre_fantasia')->get();
        $formasPago = FormaPago::orderBy('nombre')->get();
        $selected_cliente_id = $request->get('cliente_id');
        return view('cliente_recibos.create', compact('clientes', 'formasPago', 'selected_cliente_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'forma_pago_id' => 'required|exists:forma_pagos,id',
            'nro_recibo' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
        ]);

        ClienteRecibo::create($request->all());

        return redirect()->route('cliente_recibos.index')->with('success', 'Recibo creado correctamente.');
    }

    public function edit(ClienteRecibo $clienteRecibo)
    {
        $clientes = Cliente::orderBy('nombre_fantasia')->get();
        $formasPago = FormaPago::orderBy('nombre')->get();
        return view('cliente_recibos.edit', compact('clienteRecibo', 'clientes', 'formasPago'));
    }

    public function update(Request $request, ClienteRecibo $clienteRecibo)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'forma_pago_id' => 'required|exists:forma_pagos,id',
            'nro_recibo' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
        ]);

        $clienteRecibo->update($request->all());

        return redirect()->route('cliente_recibos.index')->with('success', 'Recibo actualizado correctamente.');
    }

    public function destroy(ClienteRecibo $clienteRecibo)
    {
        $clienteRecibo->delete();
        return redirect()->route('cliente_recibos.index')->with('success', 'Recibo eliminado correctamente.');
    }
}