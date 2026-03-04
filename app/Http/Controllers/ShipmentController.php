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
            $commissionMonto = $request->total_flete * ($originAgency->comision_porcentaje / 100);

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
}