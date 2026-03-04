<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentLog;
use App\Models\Cliente;
use App\Models\Agency;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = Shipment::with(['sender', 'receiver', 'originAgency', 'destinationAgency'])
            ->latest()
            ->paginate(15);
        return view('shipments.index', compact('shipments'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $agencies = Agency::where('activa', true)->get();
        $carriers = Carrier::where('activo', true)->get();
        return view('shipments.create', compact('clientes', 'agencies', 'carriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:clientes,id',
            'receiver_id' => 'required|exists:clientes,id',
            'origin_agency_id' => 'required|exists:agencies,id',
            'destination_agency_id' => 'required|exists:agencies,id',
            'total_flete' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.descripcion' => 'required|string|max:255',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $originAgency = Agency::findOrFail($request->origin_agency_id);
            $commissionMonto = $request->total_flete * ($originAgency->com_origen / 100);

            $shipment = Shipment::create([
                'tracking_number' => 'G-' . strtoupper(Str::random(8)),
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'origin_agency_id' => $request->origin_agency_id,
                'destination_agency_id' => $request->destination_agency_id,
                'carrier_id' => $request->carrier_id,
                'commission_agency_id' => $request->origin_agency_id, // Default to origin
                'payment_mode' => $request->payment_mode ?? 'PP',
                'status' => 'Admitted',
                'total_flete' => $request->total_flete,
                'comision_monto' => $commissionMonto,
                'notas' => $request->notas,
            ]);

            foreach ($request->items as $itemData) {
                $shipment->items()->create($itemData);
            }

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from' => 'None',
                'status_to' => 'Admitted',
                'notas' => 'Guía admitida en sistema.',
            ]);

            return redirect()->route('shipments.index')->with('success', "Guía {$shipment->tracking_number} creada correctamente.");
        });
    }

    public function show(Shipment $shipment)
    {
        $shipment->load(['sender', 'receiver', 'originAgency', 'destinationAgency', 'carrier', 'items', 'logs.user']);
        return view('shipments.show', compact('shipment'));
    }

    public function receive(Shipment $shipment)
    {
        if ($shipment->status !== 'Admitted') {
            return back()->with('error', 'Solo se pueden recibir guías en estado Admitida.');
        }

        DB::transaction(function () use ($shipment) {
            $shipment->update(['status' => 'In Office']);

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from' => 'Admitted',
                'status_to' => 'In Office',
                'notas' => 'Paquete recibido en oficina y almacenado en inventario.',
            ]);
        });

        return back()->with('success', "Guía {$shipment->tracking_number} recibida en oficina.");
    }

    public function consolidation()
    {
        // Agrupamos las guías que están "In Office" por agencia de destino
        $groups = Shipment::where('status', 'In Office')
            ->with(['destinationAgency', 'sender', 'receiver'])
            ->get()
            ->groupBy('destination_agency_id');

        $carriers = Carrier::where('activo', true)->get();

        return view('shipments.consolidation', compact('groups', 'carriers'));
    }

    public function dispatch(Request $request)
    {
        $request->validate([
            'shipment_ids' => 'required|array',
            'shipment_ids.*' => 'exists:shipments,id',
            'carrier_id' => 'required|exists:carriers,id',
        ]);

        DB::transaction(function () use ($request) {
            $shipments = Shipment::whereIn('id', $request->shipment_ids)->get();

            foreach ($shipments as $shipment) {
                $shipment->update([
                    'status' => 'In Transit',
                    'carrier_id' => $request->carrier_id,
                ]);

                ShipmentLog::create([
                    'shipment_id' => $shipment->id,
                    'user_id' => Auth::id(),
                    'status_from' => 'In Office',
                    'status_to' => 'In Transit',
                    'notas' => "Despachado en tránsito. Transportista asignado ID: {$request->carrier_id}",
                ]);
            }
        });

        return redirect()->route('shipments.consolidation')->with('success', 'Despacho realizado correctamente. Las guías están ahora en tránsito.');
    }

    public function arrive(Shipment $shipment)
    {
        if ($shipment->status !== 'In Transit') {
            return back()->with('error', 'Solo se pueden marcar como arribadas las guías que están en tránsito.');
        }

        DB::transaction(function () use ($shipment) {
            $shipment->update(['status' => 'In Destination']);

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from' => 'In Transit',
                'status_to' => 'In Destination',
                'notas' => 'Carga arribada a la agencia de destino. Lista para retiro o entrega.',
            ]);
        });

        return back()->with('success', "Guía {$shipment->tracking_number} marcada como arribada a destino.");
    }

    public function deliver(Shipment $shipment)
    {
        if ($shipment->status !== 'In Destination') {
            return back()->with('error', 'Solo se pueden marcar como entregadas las guías que están en destino.');
        }

        DB::transaction(function () use ($shipment) {
            $shipment->update(['status' => 'Delivered']);

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from' => 'In Destination',
                'status_to' => 'Delivered',
                'notas' => 'Guía entregada satisfactoriamente al destinatario.',
            ]);
        });

        return back()->with('success', "Guía {$shipment->tracking_number} entregada correctamente.");
    }
}