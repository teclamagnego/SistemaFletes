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
        $recibo->load(['cliente', 'formaPago', 'cheques']);
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
            $totalFacturas = \App\Models\ClienteFactura::where('cliente_id', $selected_cliente_id)
                ->where('forma_pago_id', '!=', 1) // No contado
                ->sum('total');
            $totalRecibos = \App\Models\ClienteRecibo::where('cliente_id', $selected_cliente_id)->sum('monto');
            $saldo = $totalFacturas - $totalRecibos;
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
            'cheques' => 'nullable|array',
            'cheques.*.numero' => 'required_with:cheques|string',
            'cheques.*.fecha' => 'required_with:cheques|date',
            'cheques.*.monto' => 'required_with:cheques|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Crear el recibo
            $recibo = ClienteRecibo::create($request->all());

            // 1b. Crear cheques si existen
            if ($request->filled('cheques')) {
                foreach ($request->cheques as $chequeData) {
                    \App\Models\Cheque::create([
                        'recibo_id' => $recibo->id,
                        'origen_id' => $request->cliente_id,
                        'monto' => $chequeData['monto'],
                        'fecha' => $chequeData['fecha'],
                        'numero' => $chequeData['numero'],
                        'observacion_origen' => $chequeData['observacion_origen'] ?? null,
                    ]);
                }
            }

            // 2. Imputar facturas (FIFO: de más vieja a más nueva)
            $montoARepartirFacturas = $request->monto;

            $facturasPendientes = ClienteFactura::where('cliente_id', $request->cliente_id)
                ->where('falta_imputar', '>', 0)
                ->orderBy('fecha', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($facturasPendientes as $factura) {
                if ($montoARepartirFacturas <= 0) break;

                if ($montoARepartirFacturas >= $factura->falta_imputar) {
                    $montoARepartirFacturas -= $factura->falta_imputar;
                    $factura->update(['falta_imputar' => 0]);
                } else {
                    $factura->update(['falta_imputar' => $factura->falta_imputar - $montoARepartirFacturas]);
                    $montoARepartirFacturas = 0;
                }
            }

            // 3. Imputar a envíos facturados (faltarendir > 0)
            $montoARepartirGuias = $request->monto;
            
            $guiasPendientes = \App\Models\Shipment::where('cliente_id', $request->cliente_id)
                ->where('factura_id', '>', 0)
                ->where('faltarendir', '>', 0)
                ->orderBy('fecha', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($guiasPendientes as $guia) {
                if ($montoARepartirGuias <= 0) break;

                if ($montoARepartirGuias >= $guia->faltarendir) {
                    $montoARepartirGuias -= $guia->faltarendir;
                    $guia->update(['faltarendir' => 0]);
                } else {
                    $guia->update(['faltarendir' => $guia->faltarendir - $montoARepartirGuias]);
                    $montoARepartirGuias = 0;
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