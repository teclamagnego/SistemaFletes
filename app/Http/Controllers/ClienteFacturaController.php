<?php

namespace App\Http\Controllers;

use App\Models\ClienteFactura;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ClienteFacturaController extends Controller
{
    public function index()
    {
        $facturas = ClienteFactura::with('cliente')->latest()->paginate(15);
        return view('cliente_facturas.index', compact('facturas'));
    }

    public function show(ClienteFactura $factura)
    {
        $factura->load(['cliente', 'shipments.formaPago']);
        $empresa = Empresa::first();
        return view('cliente_facturas.show', compact('factura', 'empresa'));
    }

    public function print(ClienteFactura $factura)
    {
        $factura->load(['cliente', 'shipments.formaPago', 'shipments.sender', 'shipments.receiver']);
        $empresa = Empresa::first();

        $pdf = Pdf::loadView('cliente_facturas.print', compact('factura', 'empresa'));
        return $pdf->stream("Factura_{$factura->nro_factura}.pdf");
    }

    public function destroy(ClienteFactura $factura)
    {
        // Al borrar una factura, desvinculamos las guías
        $factura->shipments()->update(['factura_id' => 0]);
        $factura->delete();

        return redirect()->back()->with('success', 'Factura eliminada correctamente. Las guías han sido desvinculadas.');
    }
}