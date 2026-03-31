<?php

namespace App\Http\Controllers;

use App\Models\Contrareembolso;
use App\Models\Cliente;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ContrareembolsoController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrareembolso::with(['shipment', 'cliente']);

        // Filtro por cliente
        if ($request->filled('cliente')) {
            $clienteSearch = $request->cliente;
            $query->whereHas('cliente', function ($q) use ($clienteSearch) {
                $q->where('nombre_fantasia', 'LIKE', "%{$clienteSearch}%")
                  ->orWhere('razon_social', 'LIKE', "%{$clienteSearch}%");
            });
        }

        // Filtro por fecha cobrado
        if ($request->filled('fecha_cobrado_desde')) {
            $query->whereDate('fecha_cobrado', '>=', $request->fecha_cobrado_desde);
        }
        if ($request->filled('fecha_cobrado_hasta')) {
            $query->whereDate('fecha_cobrado', '<=', $request->fecha_cobrado_hasta);
        }

        // Filtro por estado cobrado
        if ($request->filled('estado_cobrado')) {
            if ($request->estado_cobrado === 'cobrado') {
                $query->whereNotNull('fecha_cobrado');
            } elseif ($request->estado_cobrado === 'pendiente') {
                $query->whereNull('fecha_cobrado');
            }
        }

        // Filtro por estado rendido
        if ($request->filled('estado_rendido')) {
            if ($request->estado_rendido === 'rendido') {
                $query->whereNotNull('fecha_rendido');
            } elseif ($request->estado_rendido === 'pendiente') {
                $query->whereNull('fecha_rendido');
            }
        }

        $contrareembolsos = $query->latest()->paginate(20)->withQueryString();

        // Totales
        $totalMonto = $query->sum('monto');
        $totalPendienteCobro = (clone $query)->whereNull('fecha_cobrado')->sum('monto');
        $totalPendienteRendicion = (clone $query)->whereNull('fecha_rendido')->whereNotNull('fecha_cobrado')->sum('monto');

        return view('contrareembolsos.index', compact(
            'contrareembolsos', 'totalMonto', 'totalPendienteCobro', 'totalPendienteRendicion'
        ));
    }

    public function print(Contrareembolso $contrareembolso)
    {
        $contrareembolso->load([
            'shipment.sender.localidad',
            'shipment.receiver.localidad',
            'shipment.originAgency',
            'shipment.destinationAgency',
            'shipment.formaPago',
            'cliente',
        ]);

        $shipment = $contrareembolso->shipment;
        $empresa = Empresa::first();

        $logoBase64 = null;
        if ($empresa && $empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
            $path = storage_path('app/public/' . $empresa->logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('contrareembolsos.pdf', compact('contrareembolso', 'shipment', 'empresa', 'logoBase64'));

        return $pdf->stream("Contrareembolso_{$contrareembolso->id}_Guia_{$shipment->tracking_number}.pdf");
    }

    public function updateFechaRendido(Request $request, Contrareembolso $contrareembolso)
    {
        $request->validate([
            'fecha_rendido' => 'required|date',
        ]);

        $contrareembolso->update(['fecha_rendido' => $request->fecha_rendido]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Fecha de rendición actualizada correctamente.');
    }

    public function clearFechaRendido(Contrareembolso $contrareembolso)
    {
        $contrareembolso->update(['fecha_rendido' => null]);

        return response()->json(['success' => true]);
    }
}

