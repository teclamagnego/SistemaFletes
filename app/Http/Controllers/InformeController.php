<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Agency;
use App\Models\ClienteFactura;
use App\Models\FacturaCodigo;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InformeController extends Controller
{
    public function saldosClientes(Request $request)
    {
        $clientes = Cliente::whereHas('facturas', function($query) {
            $query->where('falta_imputar', '>', 0);
        })
        ->withSum('facturas', 'falta_imputar')
        ->withSum('facturas', 'total')
        ->orderBy('facturas_sum_falta_imputar', 'desc')
        ->get();

        $totalSaldos = $clientes->sum('facturas_sum_falta_imputar');

        return view('informes.saldos_clientes', compact('clientes', 'totalSaldos'));
    }

    public function saldosAgencias(Request $request)
    {
        $agencias = Agency::whereHas('facturas', function($query) {
            $query->where('falta_imputar', '>', 0);
        })
        ->withSum('facturas', 'falta_imputar')
        ->withSum('facturas', 'total')
        ->orderBy('facturas_sum_falta_imputar', 'desc')
        ->get();

        $totalSaldos = $agencias->sum('facturas_sum_falta_imputar');

        return view('informes.saldos_agencias', compact('agencias', 'totalSaldos'));
    }

    public function facturasClientes(Request $request)
    {
        $codigos = FacturaCodigo::orderBy('nombre')->get();

        $facturas = collect();
        $filtrado = false;
        $totalFacturas = 0;
        $totalImporte = 0;
        $totalPendiente = 0;

        if ($request->filled('fecha_desde') || $request->filled('fecha_hasta') || $request->filled('codigos')) {
            $filtrado = true;
            $query = ClienteFactura::with(['cliente', 'formaPago'])
                ->orderBy('fecha', 'desc')
                ->orderBy('nro_factura', 'desc');

            if ($request->filled('fecha_desde')) {
                $query->where('fecha', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->where('fecha', '<=', $request->fecha_hasta);
            }

            if ($request->filled('codigos')) {
                $query->whereIn('codigo', $request->codigos);
            }

            $facturas = $query->get();
            $totalFacturas = $facturas->count();
            $totalImporte = $facturas->sum('total');
            $totalPendiente = $facturas->sum('falta_imputar');
        }

        return view('informes.facturas_clientes', compact(
            'codigos', 'facturas', 'filtrado', 'totalFacturas', 'totalImporte', 'totalPendiente'
        ));
    }

    public function facturasClientesPrint(Request $request)
    {
        $query = ClienteFactura::with(['cliente', 'formaPago'])
            ->orderBy('fecha', 'desc')
            ->orderBy('nro_factura', 'desc');

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('codigos')) {
            $codigosArray = explode(',', $request->codigos);
            $query->whereIn('codigo', $codigosArray);
        }

        $facturas = $query->get();
        $totalFacturas = $facturas->count();
        $totalImporte = $facturas->sum('total');
        $totalPendiente = $facturas->sum('falta_imputar');

        // Get codigo names for the filter description
        $codigosSeleccionados = [];
        if ($request->filled('codigos')) {
            $codigosArray = explode(',', $request->codigos);
            $codigosSeleccionados = FacturaCodigo::whereIn('id', $codigosArray)->pluck('nombre')->toArray();
        }

        $empresa = Empresa::first();

        $pdf = Pdf::loadView('informes.facturas_clientes_print', compact(
            'facturas', 'totalFacturas', 'totalImporte', 'totalPendiente',
            'codigosSeleccionados', 'empresa'
        ))->setPaper('a4', 'landscape');

        $fechaDesde = $request->fecha_desde ?? 'inicio';
        $fechaHasta = $request->fecha_hasta ?? 'hoy';

        return $pdf->stream("Informe_Facturas_Clientes_{$fechaDesde}_a_{$fechaHasta}.pdf");
    }
}
