<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Localidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FormaPago;
use App\Models\AgenciaFactura;
use App\Models\AgenciaRecibo;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::with('localidad')->paginate(15);
        return view('agencies.index', compact('agencies'));
    }

    public function create()
    {
        $localidades = Localidad::orderBy('nombre')->get();
        return view('agencies.create', compact('localidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:10|unique:agencies',
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email',
            'localidad_id' => 'required|exists:localidades,id',
            'com_origen' => 'required|numeric|min:0|max:100',
            'com_destino' => 'required|numeric|min:0|max:100',
        ]);

        Agency::create($request->all());

        return redirect()->route('agencies.index')->with('success', 'Agencia creada correctamente.');
    }

    public function edit(Agency $agency)
    {
        $localidades = Localidad::orderBy('nombre')->get();
        return view('agencies.edit', compact('agency', 'localidades'));
    }

    public function update(Request $request, Agency $agency)
    {
        $request->validate([
            'codigo' => 'required|string|max:10|unique:agencies,codigo,' . $agency->id,
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email',
            'localidad_id' => 'required|exists:localidades,id',
            'com_origen' => 'required|numeric|min:0|max:100',
            'com_destino' => 'required|numeric|min:0|max:100',
        ]);

        $agency->update($request->all());

        return redirect()->route('agencies.index')->with('success', 'Agencia actualizada correctamente.');
    }

    public function shipments(Agency $agency, \Illuminate\Http\Request $request)
    {
        $tab = $request->get('tab', 'all'); // 'origin', 'destination', 'all'

        $query = \App\Models\Shipment::with(['sender', 'receiver', 'originAgency', 'destinationAgency', 'formaPago'])
            ->when($tab === 'origin', fn($q) => $q->where('origin_agency_id', $agency->id))
            ->when($tab === 'destination', fn($q) => $q->where('destination_agency_id', $agency->id))
            ->when($tab === 'all', fn($q) => $q->where(function ($q) use ($agency) {
                $q->where('origin_agency_id', $agency->id)
                    ->orWhere('destination_agency_id', $agency->id);
            }))
            ->orderByDesc('created_at');

        $shipments = $query->paginate(20)->appends($request->query());

        $totalOrigen = \App\Models\Shipment::where('origin_agency_id', $agency->id)->count();
        $totalDestino = \App\Models\Shipment::where('destination_agency_id', $agency->id)->count();

        return view('agencies.shipments', compact('agency', 'shipments', 'tab', 'totalOrigen', 'totalDestino'));
    }

    public function billing(Request $request, Agency $agency)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));
        $role = $request->input('role', 'all'); // 'origin', 'destination', 'all'
        $status_factura = $request->input('status_factura', 'all');
        $status = $request->input('status', 'Delivered');

        $query = \App\Models\Shipment::with(['items.articulo', 'originAgency', 'destinationAgency', 'formaPago'])
            ->whereBetween('fecha', [$from, $to]);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($role === 'origin') {
            $query->where('origin_agency_id', $agency->id);
            if ($status_factura === 'unbilled') $query->where('agencia_f_origen_id', 0);
            if ($status_factura === 'billed') $query->where('agencia_f_origen_id', '>', 0);
        } elseif ($role === 'destination') {
            $query->where('destination_agency_id', $agency->id);
            if ($status_factura === 'unbilled') $query->where('agencia_f_destino_id', 0);
            if ($status_factura === 'billed') $query->where('agencia_f_destino_id', '>', 0);
        } else {
            $query->where(function ($q) use ($agency) {
                $q->where('origin_agency_id', $agency->id)
                  ->orWhere('destination_agency_id', $agency->id);
            });
            // Filtering 'all' with status is tricky since a shipment could be billed for origin but not destination
            if ($status_factura === 'unbilled') {
                $query->where(function($q) use ($agency) {
                    $q->where(fn($sub) => $sub->where('origin_agency_id', $agency->id)->where('agencia_f_origen_id', 0))
                      ->orWhere(fn($sub) => $sub->where('destination_agency_id', $agency->id)->where('agencia_f_destino_id', 0));
                });
            } elseif ($status_factura === 'billed') {
                $query->where(function($q) use ($agency) {
                    $q->where(fn($sub) => $sub->where('origin_agency_id', $agency->id)->where('agencia_f_origen_id', '>', 0))
                      ->orWhere(fn($sub) => $sub->where('destination_agency_id', $agency->id)->where('agencia_f_destino_id', '>', 0));
                });
            }
        }

        $shipments = $query->orderBy('fecha')->get();

        // Calcular comisiones para la vista
        foreach ($shipments as $s) {
            $com = 0;
            $roles = []; 
            if ($s->origin_agency_id == $agency->id && ($role === 'all' || $role === 'origin')) {
                $c = $s->total_flete * ($s->originAgency->com_origen / 100);
                foreach ($s->items as $item) { $c += $item->cantidad * ($item->articulo->com_origen ?? 0); }
                $com += $c;
                $roles[] = 'Origen';
                $s->comision_origen_calc = $c;
            }
            if ($s->destination_agency_id == $agency->id && ($role === 'all' || $role === 'destination')) {
                $c = $s->total_flete * ($s->destinationAgency->com_destino / 100);
                foreach ($s->items as $item) { $c += $item->cantidad * ($item->articulo->com_destino ?? 0); }
                $com += $c;
                $roles[] = 'Destino';
                $s->comision_destino_calc = $c;
            }
            $s->role_in_billing = $roles;
            $s->comision_total_agencia = $com;
        }

        return view('agencies.billing', compact('agency', 'shipments', 'from', 'to', 'role', 'status_factura', 'status'));
    }

    public function generateInvoice(Request $request, Agency $agency)
    {
        $selectedIds = $request->input('shipments', []);
        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos una guía.');
        }

        $shipments = \App\Models\Shipment::whereIn('id', $selectedIds)->with(['items.articulo', 'originAgency', 'destinationAgency'])->get();
        
        $totalCommission = 0;
        foreach ($shipments as $s) {
            if ($s->origin_agency_id == $agency->id && $s->agencia_f_origen_id == 0) {
                $c = $s->total_flete * ($s->originAgency->com_origen / 100);
                foreach ($s->items as $item) { $c += $item->cantidad * ($item->articulo->com_origen ?? 0); }
                $totalCommission += $c;
            }
            if ($s->destination_agency_id == $agency->id && $s->agencia_f_destino_id == 0) {
                $c = $s->total_flete * ($s->destinationAgency->com_destino / 100);
                foreach ($s->items as $item) { $c += $item->cantidad * ($item->articulo->com_destino ?? 0); }
                $totalCommission += $c;
            }
        }

        if ($totalCommission <= 0) {
            return redirect()->back()->with('error', 'Las guías seleccionadas ya han sido facturadas o no generan comisión.');
        }

        \DB::transaction(function () use ($agency, $totalCommission, $shipments) {
            $factura = \App\Models\AgenciaFactura::create([
                'agency_id' => $agency->id,
                'fecha' => now(),
                'total' => $totalCommission,
                'observacion' => 'Comisiones por ' . count($shipments) . ' guías. Mirar detalle adjunto.',
                'nro_factura' => 'COM-' . strtoupper(substr($agency->nombre, 0, 3)) . '-' . date('ymdHis')
            ]);

            foreach ($shipments as $s) {
                $update = [];
                if ($s->origin_agency_id == $agency->id && $s->agencia_f_origen_id == 0) {
                    $update['agencia_f_origen_id'] = $factura->id;
                }
                if ($s->destination_agency_id == $agency->id && $s->agencia_f_destino_id == 0) {
                    $update['agencia_f_destino_id'] = $factura->id;
                }
                if (!empty($update)) {
                    $s->update($update);
                }
            }
        });

        return redirect()->route('agencies.history', $agency)->with('success', 'Factura de comisión generada correctamente.');
    }

    public function history(Request $request, Agency $agency)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));

        $data = $this->getHistoryData($request, $agency);

        return view('agencies.history', array_merge(['agency' => $agency, 'from' => $from, 'to' => $to], $data));
    }

    protected function getHistoryData(Request $request, Agency $agency)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));

        // 1. Saldo Anterior
        $prev_facturas = \App\Models\AgenciaFactura::where('agency_id', $agency->id)->where('fecha', '<', $from)->sum('total');
        $prev_recibos = \App\Models\AgenciaRecibo::where('agency_id', $agency->id)->where('fecha', '<', $from)->sum('monto');
        $saldoAnterior = $prev_facturas - $prev_recibos;

        // 2. Movimientos
        $facturas = \App\Models\AgenciaFactura::where('agency_id', $agency->id)->whereBetween('fecha', [$from, $to])->get();
        $recibos = \App\Models\AgenciaRecibo::where('agency_id', $agency->id)->whereBetween('fecha', [$from, $to])->get();

        $movimientos = collect();
        foreach ($facturas as $f) {
            $movimientos->push([
                'fecha' => $f->fecha,
                'tipo' => 'Factura Comisión',
                'referencia' => $f->nro_factura,
                'detalle' => $f->observacion,
                'debe' => $f->total,
                'haber' => 0,
                'id' => $f->id,
                'model' => 'AgenciaFactura'
            ]);
        }
        foreach ($recibos as $r) {
            $movimientos->push([
                'fecha' => $r->fecha,
                'tipo' => 'Pago de Comisión',
                'referencia' => $r->nro_recibo ?? 'Recibo #' . $r->id,
                'detalle' => $r->formaPago?->nombre,
                'debe' => 0,
                'haber' => $r->monto,
                'id' => $r->id,
                'model' => 'AgenciaRecibo'
            ]);
        }

        $movimientos = $movimientos->sortBy('fecha');

        return [
            'movimientos' => $movimientos,
            'saldoAnterior' => $saldoAnterior
        ];
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();
        return redirect()->route('agencies.index')->with('success', 'Agencia eliminada correctamente.');
    }
}