<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\TipoDoc;
use App\Models\Localidad;
use App\Models\TipoCuenta;
use App\Models\TipoIva;
use App\Models\Agency;
use App\Models\FormaPago;
use App\Models\Shipment;
use App\Models\ClienteFactura;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShipmentsBillingExport;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with(['tipoDoc', 'localidad', 'tipoCuenta', 'tipoIva', 'agenciaOrigen', 'agenciaDestino']);

        if ($request->filled('nombre')) {
            $query->where('nombre_fantasia', 'LIKE', '%' . $request->nombre . '%');
        }

        if ($request->filled('localidad_id')) {
            $query->where('localidad_id', $request->localidad_id);
        }

        $clientes = $query->paginate(15)->appends($request->all());
        $localidades = Localidad::all();

        return view('clientes.index', compact('clientes', 'localidades'));
    }

    public function search(Request $request)
    {
        $q = $request->query('q');
        $clientes = Cliente::where('nombre_fantasia', 'LIKE', "%$q%")
            ->orWhere('razon_social', 'LIKE', "%$q%")
            ->orWhere('documento_nro', 'LIKE', "%$q%")
            ->limit(10)
            ->get();
        return response()->json($clientes);
    }

    public function storeQuick(Request $request)
    {
        $request->validate([
            'nombre_fantasia' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente = Cliente::create([
            'nombre_fantasia' => $request->nombre_fantasia,
            'razon_social' => $request->nombre_fantasia,
            'direccion' => $request->direccion,
            'tipodoc_id' => 1,
            'documento_nro' => '0',
            'localidad_id' => 1,
            'tipocuenta_id' => 1,
            'tipoiva_id' => 1,
            'agenciaorigen_id' => 1,
            'agenciadestino_id' => 1,
        ]);

        return response()->json($cliente);
    }

    public function history(Request $request, Cliente $cliente)
    {
        $data = $this->getHistoryData($request, $cliente);
        return view('clientes.history', array_merge(['cliente' => $cliente], $data));
    }

    public function printHistory(Request $request, Cliente $cliente)
    {
        $data = $this->getHistoryData($request, $cliente);
        $empresa = \App\Models\Empresa::first();
        
        $pdf = Pdf::loadView('clientes.history_pdf', array_merge(['cliente' => $cliente, 'empresa' => $empresa], $data));
        return $pdf->stream("Historial_{$cliente->nombre_fantasia}.pdf");
    }

    public function billing(Request $request, Cliente $cliente)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));
        $status_factura = $request->input('status_factura', 'all');

        $query = $cliente->shipmentsPaid()->whereBetween('fecha', [$from, $to]);

        if ($status_factura === 'billed') {
            $query->where('factura_id', '>', 0);
        } elseif ($status_factura === 'unbilled') {
            $query->where('factura_id', 0);
        }

        $shipments = $query->with('formaPago')->orderBy('fecha')->get();

        if ($request->has('export')) {
            if ($request->export === 'excel') {
                return Excel::download(new ShipmentsBillingExport($shipments), "Guias_Facturacion_{$cliente->nombre_fantasia}.xlsx");
            }
            if ($request->export === 'pdf') {
                $empresa = \App\Models\Empresa::first();
                $pdf = Pdf::loadView('clientes.billing_pdf', compact('cliente', 'shipments', 'from', 'to', 'empresa'));
                return $pdf->stream("Guias_Facturacion_{$cliente->nombre_fantasia}.pdf");
            }
        }

        return view('clientes.billing', compact('cliente', 'shipments', 'from', 'to', 'status_factura'));
    }

    public function generateInvoice(Request $request, Cliente $cliente)
    {
        $request->validate([
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
            'fecha' => 'required|date',
            'nro_factura' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $cliente) {
            $shipments = Shipment::whereIn('id', $request->shipment_ids)->get();
            $total = $shipments->sum('total_flete');
            $cantidad = $shipments->count();

            $factura = ClienteFactura::create([
                'cliente_id' => $cliente->id,
                'nro_factura' => $request->nro_factura,
                'fecha' => $request->fecha,
                'total' => $total,
                'observacion' => "Se factura(n) $cantidad guia(s). Mirar planilla adjunta de guias incluidas.",
            ]);

            Shipment::whereIn('id', $request->shipment_ids)->update(['factura_id' => $factura->id]);

            return redirect()->route('clientes.billing', $cliente)->with('success', "Factura generada correctamente por un total de $ " . number_format($total, 2));
        });
    }

    private function getHistoryData(Request $request, Cliente $cliente)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->endOfMonth()->format('Y-m-d'));
        $only_shipments = $request->boolean('only_shipments');

        $idCuentaCorriente = FormaPago::where('nombre', 'LIKE', '%Cuenta Corriente%')->first()?->id ?? 2;

        // 1. Calcular Saldo Anterior (antes de $from)
        $prev_facturas = ClienteFactura::where('cliente_id', $cliente->id)->where('fecha', '<', $from)->sum('total');
        $prev_recibos = $cliente->recibos()->where('fecha', '<', $from)->sum('monto');
        
        $saldoAnterior = $prev_facturas - $prev_recibos;

        // 2. Obtener movimientos del periodo
        $facturas = ClienteFactura::where('cliente_id', $cliente->id)->whereBetween('fecha', [$from, $to])->get();
        $recibos = $only_shipments ? collect() : $cliente->recibos()->whereBetween('fecha', [$from, $to])->get();

        $movimientos = collect();
        foreach ($facturas as $f) {
            $movimientos->push([
                'fecha' => $f->fecha,
                'tipo' => 'Factura',
                'referencia' => $f->nro_factura ?? 'Factura #' . $f->id,
                'detalle' => "Factura de guias",
                'debe' => $f->total,
                'haber' => 0,
                'factura_id' => $f->id,
                'link' => null
            ]);
        }
        
        if (!$only_shipments) {
            foreach ($recibos as $r) {
                $movimientos->push([
                    'fecha' => $r->fecha,
                    'tipo' => 'Recibo',
                    'referencia' => $r->nro_recibo ?? 'Recibo #' . $r->id,
                    'detalle' => $r->formaPago?->nombre,
                    'debe' => 0,
                    'haber' => $r->monto,
                    'recibo_id' => $r->id,
                    'link' => null
                ]);
            }
        }

        if ($only_shipments) {
            $shipments = $cliente->shipmentsPaid()
                ->where('factura_id', 0)
                ->whereBetween('fecha', [$from, $to])
                ->get();
            foreach ($shipments as $s) {
                $esCuentaCorriente = ($s->forma_pago_id == $idCuentaCorriente);
                $movimientos->push([
                    'fecha' => $s->fecha,
                    'tipo' => 'Guia (Pend. Fact)',
                    'referencia' => $s->tracking_number,
                    'detalle' => $s->formaPago?->nombre,
                    'debe' => $esCuentaCorriente ? $s->total_flete : 0,
                    'haber' => 0,
                    'factura_id' => 0,
                    'link' => route('shipments.show', $s)
                ]);
            }
        }

        $movimientos = $movimientos->sortBy('fecha');

        return [
            'movimientos' => $movimientos,
            'from' => $from,
            'to' => $to,
            'saldoAnterior' => $saldoAnterior,
            'only_shipments' => $only_shipments
        ];
    }

    public function create()
    {
        $tiposDoc = TipoDoc::all();
        $localidades = Localidad::all();
        $tiposCuenta = TipoCuenta::all();
        $tiposIva = TipoIva::all();
        $agencies = Agency::all();

        return view('clientes.create', compact('tiposDoc', 'localidades', 'tiposCuenta', 'tiposIva', 'agencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_fantasia' => 'required|string|max:255',
            'tipodoc_id' => 'required|exists:tipos_doc,id',
            'documento_nro' => 'required|string|max:20',
            'localidad_id' => 'required|exists:localidades,id',
            'tipocuenta_id' => 'required|exists:tipos_cuenta,id',
            'tipoiva_id' => 'required|exists:tipos_iva,id',
            'agenciaorigen_id' => 'required|exists:agencies,id',
            'agenciadestino_id' => 'required|exists:agencies,id',
            'email' => 'nullable|email',
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        $tiposDoc = TipoDoc::all();
        $localidades = Localidad::all();
        $tiposCuenta = TipoCuenta::all();
        $tiposIva = TipoIva::all();
        $agencies = Agency::all();

        return view('clientes.edit', compact('cliente', 'tiposDoc', 'localidades', 'tiposCuenta', 'tiposIva', 'agencies'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre_fantasia' => 'required|string|max:255',
            'tipodoc_id' => 'required|exists:tipos_doc,id',
            'documento_nro' => 'required|string|max:20',
            'localidad_id' => 'required|exists:localidades,id',
            'tipocuenta_id' => 'required|exists:tipos_cuenta,id',
            'tipoiva_id' => 'required|exists:tipos_iva,id',
            'agenciaorigen_id' => 'required|exists:agencies,id',
            'agenciadestino_id' => 'required|exists:agencies,id',
            'email' => 'nullable|email',
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }
}