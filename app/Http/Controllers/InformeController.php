<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformeController extends Controller
{
    public function saldosClientes(Request $request)
    {
        $clientes = Cliente::whereHas('facturas', function($query) {
            $query->where('falta_imputar', '>', 0);
        })
        ->withSum('facturas', 'falta_imputar')
        ->withSum('facturas', 'total')
        ->orderBy('facturas_sum_falta_imputar', 'desc')
        ->get();

        $totalSaldos = $clientes->sum('facturas_sum_falta_imputar');

        return view('informes.saldos_clientes', compact('clientes', 'totalSaldos'));
    }

    public function saldosAgencias(Request $request)
    {
        $agencias = Agency::whereHas('facturas', function($query) {
            $query->where('falta_imputar', '>', 0);
        })
        ->withSum('facturas', 'falta_imputar')
        ->withSum('facturas', 'total')
        ->orderBy('facturas_sum_falta_imputar', 'desc')
        ->get();

        $totalSaldos = $agencias->sum('facturas_sum_falta_imputar');

        return view('informes.saldos_agencias', compact('agencias', 'totalSaldos'));
    }
}
