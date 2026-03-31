<?php

namespace App\Http\Controllers;

use App\Models\ClienteRecibo;
use App\Models\Cliente;
use App\Models\ClienteFactura;
use App\Models\FormaPago;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ClienteReciboController extends Controller
{
    public function print(ClienteRecibo $recibo)
    {
        $recibo->load(['cliente', 'formaPago']);
        $empresa = Empresa::first();
        $pdf = Pdf::loadView('cliente_recibos.print', compact('recibo', 'empresa'));
        return $pdf->stream("Recibo_{$recibo->id}.pdf");
    }

    public function index()
    {
        $recibos = ClienteRecibo::with(['cliente', 'formaPago'])->latest()->paginate(15);
        return view('cliente_recibos.index', compact('recibos'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::orderBy('nombre_fantasia')->get();
        $formasPago = FormaPago::where('id', '!=', 2)->orderBy('nombre')->get();
        $selected_cliente_id = $request->get('cliente_id');

        $saldo = 0;
        if ($selected_cliente_id) {
            $saldo = \App\Models\ClienteFactura::where('cliente_id', $selected_cliente_id)->sum('falta_imputar');
        }

        return view('cliente_recibos.create', compact('clientes', 'formasPago', 'selected_cliente_id', 'saldo'));
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

        DB::transaction(function () use ($request) {
            // 1. Crear el recibo
            $recibo = ClienteRecibo::create($request->all());

            // 2. Imputar facturas (FIFO: de más vieja a más nueva)
            $montoARepartir = $request->monto;

            $facturasPendientes = ClienteFactura::where('cliente_id', $request->cliente_id)
                ->where('falta_imputar', '>', 0)
                ->orderBy('fecha', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($facturasPendientes as $factura) {
                if ($montoARepartir <= 0) break;

                if ($montoARepartir >= $factura->falta_imputar) {
                    // Cubre toda la factura
                    $montoARepartir -= $factura->falta_imputar;
                    $factura->update(['falta_imputar' => 0]);
                } else {
                    // Cubre solo una parte
                    $factura->update(['falta_imputar' => $factura->falta_imputar - $montoARepartir]);
                    $montoARepartir = 0;
                }
            }
        });

        return redirect()->route('clientes.history', $request->cliente_id)->with('success', 'Recibo creado e imputado correctamente.');
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