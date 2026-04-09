<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentLog;
use App\Models\Cliente;
use App\Models\Agency;
use App\Models\Carrier;
use App\Models\FormaPago;
use App\Models\ShipmentStatus;
use App\Models\Articulo;
use App\Models\Empresa;
use App\Models\ClienteFactura;
use App\Models\Contrareembolso;
use App\Models\Localidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with(['sender', 'receiver', 'originAgency', 'destinationAgency', 'deliveryLog']);

        // Filtro por guía (tracking number)
        if ($request->filled('tracking_number')) {
            $query->where('tracking_number', 'LIKE', "%{$request->tracking_number}%");
        }

        // Filtro por remitente o destinatario (vía relación con cliente)
        if ($request->filled('cliente')) {
            $terms = explode(' ', $request->cliente);
            foreach ($terms as $term) {
                $term = trim($term);
                if ($term !== '') {
                    $query->where(function ($q) use ($term) {
                        $q->whereHas('sender', function ($sq) use ($term) {
                                $sq->where('nombre_fantasia', 'LIKE', "%{$term}%")
                                    ->orWhere('razon_social', 'LIKE', "%{$term}%")
                                    ->orWhere('documento_nro', 'LIKE', "%{$term}%");
                            }
                            )->orWhereHas('receiver', function ($sq) use ($term) {
                                $sq->where('nombre_fantasia', 'LIKE', "%{$term}%")
                                    ->orWhere('razon_social', 'LIKE', "%{$term}%")
                                    ->orWhere('documento_nro', 'LIKE', "%{$term}%");
                            }
                            );
                        });
                }
            }
        }

        // Filtro por agencia de origen
        if ($request->filled('origin_agency_filter_id')) {
            $query->where('origin_agency_id', $request->origin_agency_filter_id);
        }

        // Filtro por agencia de destino
        if ($request->filled('destination_agency_filter_id')) {
            $query->where('destination_agency_id', $request->destination_agency_filter_id);
        }

        // Filtro por estado
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filtro por rango de fechas
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $shipments = $query->latest()->paginate(15)->withQueryString();
        $agencies = Agency::orderBy('nombre')->get();
        $statuses = ShipmentStatus::all();

        return view('shipments.index', compact('shipments', 'agencies', 'statuses', 'request'));
    }

    public function create()
    {
        $agencies = Agency::where('activa', true)->orderBy('nombre')->get();
        $carriers = Carrier::where('activo', true)->get();
        $formasPago = FormaPago::orderBy('nombre')->get();
        $empresa = Empresa::first(); // Asumimos la primera como principal para configuración
        $localidades = Localidad::orderBy('nombre')->get();
        return view('shipments.create', compact('agencies', 'carriers', 'formasPago', 'empresa', 'localidades'));
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
            'ref_remito' => 'nullable|string|max:255',
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
            $comisionArticulosOrigen = 0;
            $comisionArticulosDestino = 0;

            foreach ($request->items as $itemData) {
                $totalFlete += $itemData['total'];
                
                if (isset($itemData['articulo_id'])) {
                    $articulo = Articulo::find($itemData['articulo_id']);
                    if ($articulo) {
                        $comisionArticulosOrigen += ($articulo->com_origen / 100) * $itemData['total'];
                        $comisionArticulosDestino += ($articulo->com_destino / 100) * $itemData['total'];
                    }
                }
            }

            $originAgency = Agency::findOrFail($request->origin_agency_id);
            $destinationAgency = Agency::findOrFail($request->destination_agency_id);
            
            $comisionOrigen = ($totalFlete * ($originAgency->com_origen / 100)) + $comisionArticulosOrigen;
            $comisionDestino = ($totalFlete * ($destinationAgency->com_destino / 100)) + $comisionArticulosDestino;

            $carrierId = $singleCarrierId ?? $request->carrier_id;
            $statusId = ShipmentStatus::ADMITTED; // Nuevo

            $notas = $request->notas;
            if ($request->filled('direccion_entrega')) {
                $direccionStr = "Lugar de Entrega: " . $request->direccion_entrega;
                $notas = $notas ? $direccionStr . " - " . $notas : $direccionStr;
            }

            $shipment = Shipment::create([
                'tracking_number' => 'PENDING',
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
                'status_id' => $statusId,
                'total_flete' => $totalFlete,
                'faltarendir' => (int)$request->forma_pago_id === 2 ? $totalFlete : 0,
                'comision_destino' => $comisionDestino,
                'notas' => $notas,
                'ref_remito' => $request->ref_remito,
            ]);

            $shipment->update(['tracking_number' => (string)$shipment->id]);

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

            // Contrareembolso: si algún item tiene artículo con código REE
            $this->handleContrareembolso($shipment, $request->items);

            // Facturación Automática (Si no es Cuenta Corriente ID 2)
            if ($request->forma_pago_id != 2) {
                $factura = ClienteFactura::create([
                    'cliente_id' => $shipment->cliente_id,
                    'forma_pago_id' => $shipment->forma_pago_id,
                    'nro_factura' => null, // Opcional: auto-generar si es necesario
                    'fecha' => $shipment->fecha,
                    'total' => $shipment->total_flete,
                    'falta_imputar' => 0, // No es CC, asumimos pagado/contado
                    'observacion' => "Facturación automática al crear guía #{$shipment->id}",
                ]);
                $shipment->update(['factura_id' => $factura->id]);
            }

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from_id' => null,
                'status_to_id' => $statusId,
                'notas' => $singleCarrierId ? 'Guía admitida y puesta en tránsito automáticamente.' : 'Guía admitida en sistema.',
            ]);

            // Actualizar agencias habituales de los clientes con la agencia de la guía actual
            $sender = Cliente::find($request->sender_id);
            if ($sender) {
                $sender->update(['agenciaorigen_id' => $request->origin_agency_id]);
            }

            $receiver = Cliente::find($request->receiver_id);
            if ($receiver) {
                $receiver->update(['agenciadestino_id' => $request->destination_agency_id]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'shipment_id' => $shipment->id,
                    'tracking_number' => $shipment->tracking_number,
                    'print_url' => route('shipments.print', $shipment->id),
                    'print_base64_url' => route('shipments.printBase64', $shipment->id),
                    'redirect_url' => route('shipments.index'),
                    'message' => "Guía {$shipment->tracking_number} creada correctamente.",
                ]);
            }

            return redirect()->route('shipments.index')->with('success', "Guía {$shipment->tracking_number} creada correctamente.");
        });
    }

    public function show(Shipment $shipment)
    {
        $shipment->load(['sender', 'receiver', 'cliente', 'originAgency', 'destinationAgency', 'carrier', 'items.articulo', 'logs.user']);
        return view('shipments.show', compact('shipment'));
    }

    public function edit(Shipment $shipment)
    {

        $shipment->load(['sender', 'receiver', 'items.articulo']);
        $agencies = Agency::where('activa', true)->orderBy('nombre')->get();
        $carriers = Carrier::where('activo', true)->get();
        $formasPago = FormaPago::orderBy('nombre')->get();
        $localidades = Localidad::orderBy('nombre')->get();

        return view('shipments.edit', compact('shipment', 'agencies', 'carriers', 'formasPago', 'localidades'));
    }

    public function update(Request $request, Shipment $shipment)
    {
        \Log::debug("Update triggered for shipment {$shipment->id}", $request->all());

        $request->validate([
            'sender_id' => 'required|exists:clientes,id',
            'receiver_id' => 'required|exists:clientes,id',
            'origin_agency_id' => 'required|exists:agencies,id',
            'destination_agency_id' => 'required|exists:agencies,id',
            'carrier_id' => 'required|exists:carriers,id',
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

        return DB::transaction(function () use ($request, $shipment) {
            $totalFlete = 0;
            $comisionArticulosOrigen = 0;
            $comisionArticulosDestino = 0;

            foreach ($request->items as $itemData) {
                $totalFlete += $itemData['total'];
                
                if (isset($itemData['articulo_id'])) {
                    $articulo = Articulo::find($itemData['articulo_id']);
                    if ($articulo) {
                        $comisionArticulosOrigen += ($articulo->com_origen / 100) * $itemData['total'];
                        $comisionArticulosDestino += ($articulo->com_destino / 100) * $itemData['total'];
                    }
                }
            }

            $originAgency = Agency::findOrFail($request->origin_agency_id);
            $destinationAgency = Agency::findOrFail($request->destination_agency_id);
            
            $comisionOrigen = ($totalFlete * ($originAgency->com_origen / 100)) + $comisionArticulosOrigen;
            $comisionDestino = ($totalFlete * ($destinationAgency->com_destino / 100)) + $comisionArticulosDestino;

            \Log::info("Updating shipment {$shipment->tracking_number}", [
                'total_flete' => $totalFlete,
                'com_art_origen' => $comisionArticulosOrigen,
                'com_art_destino' => $comisionArticulosDestino,
                'comision_origen' => $comisionOrigen,
                'comision_destino' => $comisionDestino
            ]);

            $oldMonto = (int)$shipment->forma_pago_id === 2 ? $shipment->total_flete : 0;
            $newMonto = (int)$request->forma_pago_id === 2 ? $totalFlete : 0;
            $newFaltarendir = max(0, $shipment->faltarendir + ($newMonto - $oldMonto));

            $shipment->update([
                'sender_id' => $request->sender_id,
                'receiver_id' => $request->receiver_id,
                'cliente_id' => $request->payer === 'sender' ? $request->sender_id : $request->receiver_id,
                'origin_agency_id' => $request->origin_agency_id,
                'destination_agency_id' => $request->destination_agency_id,
                'carrier_id' => $request->carrier_id,
                'forma_pago_id' => $request->forma_pago_id,
                'direccion_entrega' => $request->direccion_entrega,
                'total_flete' => $totalFlete,
                'faltarendir' => $newFaltarendir,
                'comision_origen' => $comisionOrigen,
                'comision_destino' => $comisionDestino,
                'notas' => (function() use ($request) {
                    $notas = $request->notas;
                    if ($request->filled('direccion_entrega')) {
                        $direccionStr = "Lugar de Entrega: " . $request->direccion_entrega;
                        $notas = $notas ? $direccionStr . " - " . $notas : $direccionStr;
                    }
                    return $notas;
                })(),
                'ref_remito' => $request->ref_remito,
            ]);

            \Log::info("Shipment updated in DB", ['id' => $shipment->id, 'origen' => $shipment->comision_origen, 'destino' => $shipment->comision_destino]);

            // Reemplazar items: eliminar viejos y crear nuevos (más simple para formularios dinámicos)
            $shipment->items()->delete();

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

            // Actualizar contrareembolso
            $this->handleContrareembolso($shipment, $request->items);

            // Actualizar factura asociada si existe
            if ($shipment->factura_id) {
                $factura = ClienteFactura::find($shipment->factura_id);
                if ($factura) {
                    $factura->update([
                        'cliente_id' => $shipment->cliente_id,
                        'total' => $totalFlete,
                        'fecha' => $shipment->fecha,
                    ]);
                }
            }

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from_id' => $shipment->status_id,
                'status_to_id' => $shipment->status_id,
                'notas' => 'Guía actualizada por el usuario.',
            ]);

            return redirect()->route('shipments.index')->with('success', "Guía {$shipment->tracking_number} actualizada correctamente.");
        });
    }

    public function receive(Shipment $shipment)
    {
        if ($shipment->status_id !== ShipmentStatus::ADMITTED) {
            return back()->with('error', 'Solo se pueden recibir guías en estado Admitida.');
        }

        DB::transaction(function () use ($shipment) {
            $shipment->update(['status_id' => ShipmentStatus::IN_OFFICE]);

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from_id' => ShipmentStatus::ADMITTED,
                'status_to_id' => ShipmentStatus::IN_OFFICE,
                'notas' => 'Paquete recibido en oficina y almacenado en inventario.',
            ]);
        });

        return back()->with('success', "Guía {$shipment->tracking_number} recibida en oficina.");
    }

    public function consolidation()
    {
        $groups = Shipment::where('status_id', ShipmentStatus::IN_OFFICE)
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
                    'status_id' => ShipmentStatus::IN_TRANSIT,
                    'carrier_id' => $request->carrier_id,
                ]);

                ShipmentLog::create([
                    'shipment_id' => $shipment->id,
                    'user_id' => Auth::id(),
                    'status_from_id' => ShipmentStatus::IN_OFFICE,
                    'status_to_id' => ShipmentStatus::IN_TRANSIT,
                    'notas' => "Despachado en tránsito. Transportista asignado ID: {$request->carrier_id}",
                ]);
            }
        });

        return redirect()->route('shipments.consolidation')->with('success', 'Despacho realizado correctamente. Las guías están ahora en tránsito.');
    }

    public function arrive(Shipment $shipment)
    {
        if ($shipment->status_id !== ShipmentStatus::IN_TRANSIT) {
            return back()->with('error', 'Solo se pueden marcar como arribadas las guías que están en tránsito.');
        }

        DB::transaction(function () use ($shipment) {
            $shipment->update(['status_id' => ShipmentStatus::IN_DESTINATION]);

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from_id' => ShipmentStatus::IN_TRANSIT,
                'status_to_id' => ShipmentStatus::IN_DESTINATION,
                'notas' => 'Carga arribada a la agencia de destino. Lista para retiro o entrega.',
            ]);
        });

        return back()->with('success', "Guía {$shipment->tracking_number} marcada como arribada a destino.");
    }

    public function deliver(Shipment $shipment)
    {
        if (!in_array($shipment->status_id, [ShipmentStatus::IN_DESTINATION, ShipmentStatus::IN_TRANSIT])) {
            return back()->with('error', 'Solo se pueden marcar como entregadas las guías que están en destino o en tránsito.');
        }

        DB::transaction(function () use ($shipment) {
            $fromStatusId = $shipment->status_id;
            $shipment->update(['status_id' => ShipmentStatus::DELIVERED]);

            // Setear fecha_cobrado en contrareembolso si existe
            $contrareembolso = $shipment->contrareembolso;
            if ($contrareembolso && !$contrareembolso->fecha_cobrado) {
                $contrareembolso->update(['fecha_cobrado' => now()->toDateString()]);
            }

            $notas = $fromStatusId === ShipmentStatus::IN_TRANSIT 
                ? 'Guía entregada satisfactoriamente (entrega directa desde tránsito).'
                : 'Guía entregada satisfactoriamente al destinatario.';

            ShipmentLog::create([
                'shipment_id' => $shipment->id,
                'user_id' => Auth::id(),
                'status_from_id' => $fromStatusId,
                'status_to_id' => ShipmentStatus::DELIVERED,
                'notas' => $notas,
            ]);
        });

        return back()->with('success', "Guía {$shipment->tracking_number} entregada correctamente.");
    }

    public function print(Shipment $shipment)
    {
        $shipment->load([
            'sender.localidad',
            'receiver.localidad',
            'cliente',
            'originAgency.localidad',
            'destinationAgency.localidad',
            'items.articulo',
            'formaPago'
        ]);

        $empresa = \App\Models\Empresa::first();
        $sucursal = \App\Models\Sucursal::first();

        $logoBase64 = null;
        if ($empresa && $empresa->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($empresa->logo)) {
            $path = storage_path('app/public/' . $empresa->logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        return view('shipments.pdf', compact('shipment', 'empresa', 'sucursal', 'logoBase64'));
    }

    public function printFiltered(Request $request)
    {
        $query = Shipment::with(['sender', 'receiver', 'originAgency', 'destinationAgency', 'deliveryLog']);

        // Apply filters (same logic as index method)
        if ($request->filled('tracking_number')) {
            $query->where('tracking_number', 'LIKE', "%{$request->tracking_number}%");
        }

        if ($request->filled('cliente')) {
            $clienteSearch = $request->cliente;
            $query->where(function ($q) use ($clienteSearch) {
                $q->whereHas('sender', function ($sq) use ($clienteSearch) {
                        $sq->where('nombre_fantasia', 'LIKE', "%{$clienteSearch}%")
                            ->orWhere('razon_social', 'LIKE', "%{$clienteSearch}%");
                    }
                    )->orWhereHas('receiver', function ($sq) use ($clienteSearch) {
                        $sq->where('nombre_fantasia', 'LIKE', "%{$clienteSearch}%")
                            ->orWhere('razon_social', 'LIKE', "%{$clienteSearch}%");
                    }
                    );
                });
        }

        if ($request->filled('origin_agency_filter_id')) {
            $query->where('origin_agency_id', $request->origin_agency_filter_id);
        }

        if ($request->filled('destination_agency_filter_id')) {
            $query->where('destination_agency_id', $request->destination_agency_filter_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $shipments = $query->latest()->get(); // Get all filtered shipments, no pagination for print

        $empresa = \App\Models\Empresa::first();
        $sucursal = \App\Models\Sucursal::first();

        $logoBase64 = null;
        if ($empresa && $empresa->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($empresa->logo)) {
            $path = storage_path('app/public/' . $empresa->logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $filters = $request->all();


        return view('shipments.print_filtered_pdf', compact('shipments', 'empresa', 'sucursal', 'logoBase64', 'filters'));
    }

    public function printBase64(Shipment $shipment)
    {
        $shipment->load([
            'sender.localidad',
            'receiver.localidad',
            'cliente',
            'originAgency.localidad',
            'destinationAgency.localidad',
            'items.articulo',
            'formaPago'
        ]);

        $empresa = \App\Models\Empresa::first();
        $sucursal = \App\Models\Sucursal::first();

        $logoBase64 = null;
        if ($empresa && $empresa->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($empresa->logo)) {
            $path = storage_path('app/public/' . $empresa->logo);
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $pdf = Pdf::loadView('shipments.pdf', compact('shipment', 'empresa', 'sucursal', 'logoBase64'));
        $pdfContent = $pdf->output();

        return response()->json([
            'success' => true,
            'pdf_base64' => base64_encode($pdfContent),
            'filename' => "Guia_{$shipment->tracking_number}.pdf",
        ]);
    }

    public function signRequest(Request $request)
    {
        $toSign = $request->input('toSign');
        $empresa = Empresa::first();

        if (!$empresa || !$empresa->qz_private_key) {
            return response('No hay llave privada configurada', 500);
        }

        $privateKey = $empresa->qz_private_key;
        $signature = "";

        if (openssl_sign($toSign, $signature, $privateKey, "sha512")) {
            return base64_encode($signature);
        }

        return response('Error al firmar con OpenSSL', 500);
    }

    public function getCertificate()
    {
        $empresa = Empresa::first();
        if (!$empresa || !$empresa->qz_certificate) {
            return response('No hay certificado configurado', 404);
        }
        return response($empresa->qz_certificate)->header('Content-Type', 'text/plain');
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status_id' => 'required|exists:shipment_statuses,id',
            'notas' => 'nullable|string',
        ]);

        $oldStatusId = $shipment->status_id;
        $shipment->update(['status_id' => $request->status_id]);

        // Si el nuevo estado es ENTREGADO, setear fecha_cobrado en contrareembolso
        if ((int)$request->status_id === ShipmentStatus::DELIVERED) {
            $contrareembolso = $shipment->contrareembolso;
            if ($contrareembolso && !$contrareembolso->fecha_cobrado) {
                $contrareembolso->update(['fecha_cobrado' => now()->toDateString()]);
            }
        }

        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'user_id' => Auth::id(),
            'status_from_id' => $oldStatusId,
            'status_to_id' => $request->status_id,
            'notas' => $request->notas ?? 'Cambio de estado desde listado centralizado',
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Shipment $shipment)
    {
        DB::transaction(function () use ($shipment) {
            // Eliminar contrareembolso asociado
            if ($shipment->contrareembolso) {
                $shipment->contrareembolso->delete();
            }

            // Si tiene factura asociada, eliminarla
            if ($shipment->factura_id) {
                $factura = ClienteFactura::find($shipment->factura_id);
                $shipment->update(['factura_id' => null]);
                if ($factura) {
                    $factura->delete();
                }
            }

            $shipment->items()->delete();
            $shipment->logs()->delete();
            $shipment->delete();
        });

        return redirect()->route('shipments.index')->with('success', 'Guía eliminada correctamente.');
    }

    /**
     * Crea o actualiza contrareembolso si hay un artículo con código REE
     */
    private function handleContrareembolso(Shipment $shipment, array $items)
    {
        $montoREE = 0;
        foreach ($items as $itemData) {
            if (isset($itemData['articulo_id'])) {
                $articulo = Articulo::find($itemData['articulo_id']);
                if ($articulo && strtoupper($articulo->codigo) === 'REE') {
                    $montoREE += $itemData['precio_unitario'];
                }
            }
        }

        if ($montoREE > 0) {
            Contrareembolso::updateOrCreate(
                ['guia_id' => $shipment->id],
                [
                    'cliente_id' => $shipment->cliente_id,
                    'monto' => $montoREE,
                ]
            );
        } else {
            // Si ya no tiene REE, eliminar contrareembolso si existía
            Contrareembolso::where('guia_id', $shipment->id)->delete();
        }
    }

    public function togglePayment(Shipment $shipment)
    {
        if ($shipment->faltarendir <= 0) {
            $shipment->update(['faltarendir' => $shipment->total_flete]);
        } else {
            $shipment->update(['faltarendir' => 0]);
        }
        return response()->json(['success' => true, 'faltarendir' => $shipment->faltarendir]);
    }
}