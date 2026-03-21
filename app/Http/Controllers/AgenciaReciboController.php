<?php

namespace App\Http\Controllers;

use App\Models\AgenciaRecibo;
use App\Models\Agency;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class AgenciaReciboController extends Controller
{
    public function index()
    {
        $recibos = AgenciaRecibo::with(['agency', 'formaPago'])->latest()->paginate(15);
        return view('agencia_recibos.index', compact('recibos'));
    }

    public function create(Request $request)
    {
        $agencies = Agency::orderBy('nombre')->get();
        $formasPago = FormaPago::where('id', '!=', 2)->orderBy('nombre')->get();
        $selected_agency_id = $request->get('agency_id');

        $saldo = 0;
        if ($selected_agency_id) {
            $totalFacturas = \App\Models\AgenciaFactura::where('agency_id', $selected_agency_id)->sum('total');
            $totalRecibos = \App\Models\AgenciaRecibo::where('agency_id', $selected_agency_id)->sum('monto');
            $saldo = $totalFacturas - $totalRecibos;
        }

        return view('agencia_recibos.create', compact('agencies', 'formasPago', 'selected_agency_id', 'saldo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'forma_pago_id' => 'required|exists:forma_pagos,id',
            'nro_recibo' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        \DB::transaction(function () use ($request) {
            $recibo = AgenciaRecibo::create($request->all());

            // Imputación de facturas
            $montoRestante = $request->monto;
            $facturasPendientes = \App\Models\AgenciaFactura::where('agency_id', $request->agency_id)
                ->where('falta_imputar', '>', 0)
                ->orderBy('fecha', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($facturasPendientes as $factura) {
                if ($montoRestante <= 0) break;

                $pago = min($montoRestante, $factura->falta_imputar);
                $factura->decrement('falta_imputar', $pago);
                $montoRestante -= $pago;
            }
        });

        return redirect()->route('agencies.history', $request->agency_id)->with('success', 'Pago registrado correctamente e imputado a facturas pendientes.');
    }

    public function destroy(AgenciaRecibo $agenciaRecibo)
    {
        $agencyId = $agenciaRecibo->agency_id;
        $agenciaRecibo->delete();
        return redirect()->route('agencies.history', $agencyId)->with('success', 'Pago eliminado correctamente.');
    }
}