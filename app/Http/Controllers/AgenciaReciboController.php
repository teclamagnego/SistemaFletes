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
        $formasPago = FormaPago::orderBy('nombre')->get();
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

        AgenciaRecibo::create($request->all());

        return redirect()->route('agencies.history', $request->agency_id)->with('success', 'Pago registrado correctamente.');
    }

    public function destroy(AgenciaRecibo $agenciaRecibo)
    {
        $agencyId = $agenciaRecibo->agency_id;
        $agenciaRecibo->delete();
        return redirect()->route('agencies.history', $agencyId)->with('success', 'Pago eliminado correctamente.');
    }
}