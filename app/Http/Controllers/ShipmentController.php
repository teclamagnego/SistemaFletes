<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentLog;
use App\Models\Cliente;
use App\Models\Agency;
use App\Models\Carrier;
use App\Models\FormaPago;
use App\Models\Articulo;
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
        $agencies = Agency::where('activa', true)->orderBy('nombre')->get();
        $carriers = Carrier::where('activo', true)->get();
        $formasPago = FormaPago::orderBy('nombre')->get();
        return view('shipments.create', compact('agencies', 'carriers', 'formasPago'));
    }

    public function store(Request $request)
    {
        $carriers = Carrier::where('activo', true)->get();
        $singleCarrierId = $carriers->count() === 1 ? $carriers->first()->id : null;

        $request->validate([
            'sender_id' => 'required|exists:clientes,id',
            'receiver_id' => 'required|exists:clientes,id',
            'origin_agency_id' => 'required|exists:agencies,id',
            'destination_agency_id' => 'required|exists:agencies,id',
            'carrier_id' => $singleCarrierId ? 'nullable' : 'required|exists:carriers,id',
            'fecha' => 'nullable|date',
            'direccion_entrega' => 'nullable|string|max:255',
            'forma_pago_id' => 'required|exists:forma_pagos,id',
            'payer' => 'required|in:sender,receiver',
            'items' => 'required|array|min:1',
            'items.*.descripcion' => 'required|string|max:255',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.bonificacion' => 'nullable|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
            'items.*.articulo_id' => 'nullable|exists:articulos,id',
        ]);

        return DB::transaction(function () use ($request, $singleCarrierId) {
            $totalFlete = 0;
            foreach ($request->items as $item) {
                $totalFlete += $item['total'];
            }

            $originAgency = Agency::findOrFail($request->origin_agency_id);
            $commissionMonto = $totalFlete * ($originAgency->com_origen / 100);

            $carrierId = $singleCarrierId ?? $request->carrier_id;
            $status = $singleCarrierId ? 'In Transit' : 'Admitted';

            $shipment = Shipment::create([
                'tracking_number' => 'G-' . strtoupper(Str::random(8)),
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'cliente_id' => $request->payer === 'sender' ? $request->sender_id : $request->receiver_id,
                'origin_agency_id' => $request->origin_agency_id,
                'destination_agency_id' => $request->destination_agency_id,
                'carrier_id' => $carrierId,
                'commission_agency_id' => $request->origin_agency_id,
                'forma_pago_id' => $request->forma_pago_id,
                'fecha' => $request->fecha ?? now(),
                'direccion_entrega' => $request->direccion_entrega,
                'status' => $status,
                'total_flete' => $totalFlete,
                'comision_monto' => $commissionMonto,
                'notas' => $request->notas,
            ]);

            foreach ($request->items as $itemData) {
                $shipment->items()->create([
                    'articulo_id' => $itemData['articulo_id'] ?? null,
                    'descripcion' => $itemData['descripcion'],
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $itemData['precio_unitario'],
                    'bonificacion' => $itemData['bonificacion'] ?? 0,
                    'total' => $itemData['total'],
                    'iva' => $itemData['iva'] ?? 0,
                ]);
            }

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from' => 'None',
                'status_to' => $status,
                'notas' => $singleCarrierId ? 'Guía admitida y puesta en tránsito automáticamente.' : 'Guía admitida en sistema.',
            ]);

            return redirect()->route('shipments.index')->with('success', "Guía {$shipment->tracking_number} creada correctamente.");
        });
    }

    public function show(Shipment $shipment)
    {
        $shipment->load(['sender', 'receiver', 'cliente', 'originAgency', 'destinationAgency', 'carrier', 'items.articulo', 'logs.user']);
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