<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();
        $lastYear = Carbon::now()->subYear();

        // Obtener IDs de estados relevantes (según el nombre almacenado en DB)
        $relevantStatuses = ShipmentStatus::whereIn('name', ['En Transito', 'Entregado'])->pluck('id');

        // Estadísticas Mes Actual
        $currentMonthEnvoys = Shipment::whereIn('status_id', $relevantStatuses)
            ->whereYear('fecha', $now->year)
            ->whereMonth('fecha', $now->month)
            ->count();

        // Estadísticas Mes Anterior
        $lastMonthEnvoys = Shipment::whereIn('status_id', $relevantStatuses)
            ->whereYear('fecha', $lastMonth->year)
            ->whereMonth('fecha', $lastMonth->month)
            ->count();

        // Estadísticas Año Actual
        $currentYearEnvoys = Shipment::whereYear('fecha', $now->year)->count();

        // Estadísticas Año Anterior
        $lastYearEnvoys = Shipment::whereYear('fecha', $lastYear->year)->count();

        // Obtener todos los estados para los contadores rápidos (mantener compatibilidad)
        $statuses = ShipmentStatus::all();

        return view('dashboard-bootstrap')
            ->with('currentMonthEnvoys', $currentMonthEnvoys)
            ->with('lastMonthEnvoys', $lastMonthEnvoys)
            ->with('currentYearEnvoys', $currentYearEnvoys)
            ->with('lastYearEnvoys', $lastYearEnvoys)
            ->with('statuses', $statuses);
    }
}
