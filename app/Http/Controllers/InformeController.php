<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Agency;
use App\Models\ClienteFactura;
use App\Models\FacturaCodigo;
use App\Models\Empresa;
use App\Models\Shipment;
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

        $fecha_desde = $request->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
        $fecha_hasta = $request->input('fecha_hasta', now()->endOfMonth()->format('Y-m-d'));

        $query = ClienteFactura::with(['cliente', 'formaPago'])
            ->whereDate('fecha', '>=', $fecha_desde)
            ->whereDate('fecha', '<=', $fecha_hasta)
            ->orderBy('fecha', 'desc')
            ->orderBy('nro_factura', 'desc');

        if ($request->filled('codigos')) {
            $query->whereIn('codigo', $request->codigos);
        }

        $facturas = $query->get();
        $totalFacturas = $facturas->count();
        $totalImporte = $facturas->sum('total');
        $totalPendiente = $facturas->sum('falta_imputar');
        $filtrado = true;

        return view('informes.facturas_clientes', compact(
            'codigos', 'facturas', 'filtrado', 'totalFacturas', 'totalImporte', 'totalPendiente', 'fecha_desde', 'fecha_hasta'
        ));
    }

    public function facturasClientesPrint(Request $request)
    {
        $fecha_desde = $request->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
        $fecha_hasta = $request->input('fecha_hasta', now()->endOfMonth()->format('Y-m-d'));

        $query = ClienteFactura::with(['cliente', 'formaPago'])
            ->whereDate('fecha', '>=', $fecha_desde)
            ->whereDate('fecha', '<=', $fecha_hasta)
            ->orderBy('fecha', 'desc')
            ->orderBy('nro_factura', 'desc');

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

    public function guiasContado(Request $request)
    {
        $agencies = Agency::orderBy('nombre')->get();
        $shipments = collect();
        $filtrado = false;
        $totalGuias = 0;
        $totalImporte = 0;

        if ($request->filled('fecha_desde') || $request->filled('fecha_hasta')) {
            $filtrado = true;
            $query = Shipment::with(['sender', 'receiver', 'originAgency', 'destinationAgency', 'formaPago'])
                ->where('forma_pago_id', '!=', 2) // No Cuenta Corriente = Contado
                ->orderBy('fecha', 'desc');

            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            }

            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }

            if ($request->filled('origin_agency_id')) {
                $query->where('origin_agency_id', $request->origin_agency_id);
            }

            if ($request->filled('destination_agency_id')) {
                $query->where('destination_agency_id', $request->destination_agency_id);
            }

            $shipments = $query->get();
            $totalGuias = $shipments->count();
            $totalImporte = $shipments->sum('total_flete');
        }

        return view('informes.guias_contado', compact(
            'agencies', 'shipments', 'filtrado', 'totalGuias', 'totalImporte'
        ));
    }

    public function guiasContadoPrint(Request $request)
    {
        $query = Shipment::with(['sender', 'receiver', 'originAgency', 'destinationAgency', 'formaPago'])
            ->where('forma_pago_id', '!=', 2)
            ->orderBy('fecha', 'desc');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('origin_agency_id')) {
            $query->where('origin_agency_id', $request->origin_agency_id);
        }

        if ($request->filled('destination_agency_id')) {
            $query->where('destination_agency_id', $request->destination_agency_id);
        }

        $shipments = $query->get();
        $totalGuias = $shipments->count();
        $totalImporte = $shipments->sum('total_flete');

        $empresa = Empresa::first();

        // Filter descriptions
        $agenciaOrigen = $request->filled('origin_agency_id') ? Agency::find($request->origin_agency_id)?->nombre : 'Todas';
        $agenciaDestino = $request->filled('destination_agency_id') ? Agency::find($request->destination_agency_id)?->nombre : 'Todas';

        $pdf = Pdf::loadView('informes.guias_contado_print', compact(
            'shipments', 'totalGuias', 'totalImporte', 'empresa', 'agenciaOrigen', 'agenciaDestino'
        ))->setPaper('a4', 'landscape');

        $fechaDesde = $request->fecha_desde ?? 'inicio';
        $fechaHasta = $request->fecha_hasta ?? 'hoy';

        return $pdf->stream("Informe_Guias_Contado_{$fechaDesde}_a_{$fechaHasta}.pdf");
    }
}

