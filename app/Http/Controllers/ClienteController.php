<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\TipoDoc;
use App\Models\Localidad;
use App\Models\TipoCuenta;
use App\Models\TipoIva;
use App\Models\Agency;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with(['tipoDoc', 'localidad', 'tipoCuenta', 'tipoIva', 'agenciaOrigen', 'agenciaDestino'])->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $clientes = Cliente::where('nombre_fantasia', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'nombre_fantasia', 'documento_nro', 'direccion']);

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
            'direccion' => $request->direccion,
            'tipodoc_id' => TipoDoc::first()->id ?? 1,
            'documento_nro' => 'A CONFIRMAR',
            'localidad_id' => Localidad::first()->id ?? 1,
            'tipocuenta_id' => TipoCuenta::first()->id ?? 1,
            'tipoiva_id' => TipoIva::first()->id ?? 1,
            'agenciaorigen_id' => Agency::where('activa', true)->first()->id ?? 1,
            'agenciadestino_id' => Agency::where('activa', true)->first()->id ?? 1,
        ]);

        return response()->json($cliente);
    }

    public function history(Cliente $cliente)
    {
        $shipments = $cliente->shipmentsPaid()->orderBy('fecha', 'desc')->get();
        $recibos = $cliente->recibos()->orderBy('fecha', 'desc')->get();

        // Identificar ID de Cuenta Corriente (usualmente 2 en base a los datos actuales)
        $idCuentaCorriente = FormaPago::where('nombre', 'LIKE', '%Cuenta Corriente%')->first()?->id ?? 2;

        // Combinar y ordenar por fecha para el estado de cuenta
        $movimientos = collect();
        foreach ($shipments as $s) {
            $esCuentaCorriente = ($s->forma_pago_id == $idCuentaCorriente);
            $movimientos->push([
                'fecha' => $s->fecha,
                'tipo' => 'Guía',
                'referencia' => $s->tracking_number,
                'detalle' => $s->formaPago?->nombre,
                'debe' => $s->total_flete,
                'haber' => $esCuentaCorriente ? 0 : $s->total_flete,
                'link' => route('shipments.show', $s)
            ]);
        }
        foreach ($recibos as $r) {
            $movimientos->push([
                'fecha' => $r->fecha,
                'tipo' => 'Recibo',
                'referencia' => $r->nro_recibo ?? 'Recibo #' . $r->id,
                'detalle' => $r->formaPago?->nombre,
                'debe' => 0,
                'haber' => $r->monto,
                'link' => null
            ]);
        }

        $movimientos = $movimientos->sortBy('fecha');

        return view('clientes.history', compact('cliente', 'movimientos'));
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