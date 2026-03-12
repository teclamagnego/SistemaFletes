<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Localidad;
use Illuminate\Http\Request;

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
        }
        ))
            ->orderByDesc('created_at');

        $shipments = $query->paginate(20)->appends($request->query());

        $totalOrigen = \App\Models\Shipment::where('origin_agency_id', $agency->id)->count();
        $totalDestino = \App\Models\Shipment::where('destination_agency_id', $agency->id)->count();

        return view('agencies.shipments', compact('agency', 'shipments', 'tab', 'totalOrigen', 'totalDestino'));
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();
        return redirect()->route('agencies.index')->with('success', 'Agencia eliminada correctamente.');
    }
}