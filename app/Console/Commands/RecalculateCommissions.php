<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shipment;
use App\Models\Agency;
use App\Models\Articulo;

class RecalculateCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:recalculate-commissions {year=2026}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate commissions for shipments of a specific year using updated logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->argument('year');
        $shipments = Shipment::with(['items.articulo', 'originAgency', 'destinationAgency'])
            ->whereYear('fecha', $year)
            ->where('comision_origen', 0)
            ->get();

        $count = $shipments->count();
        $this->info("Encontradas $count guías para el año $year.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($shipments as $shipment) {
            $comisionArticulosOrigen = 0;
            $comisionArticulosDestino = 0;
            $totalFlete = $shipment->total_flete;

            foreach ($shipment->items as $item) {
                if ($item->articulo) {
                    $comisionArticulosOrigen += ($item->articulo->com_origen / 100) * $item->total;
                    $comisionArticulosDestino += ($item->articulo->com_destino / 100) * $item->total;
                }
            }

            $originAgency = $shipment->originAgency;
            $destinationAgency = $shipment->destinationAgency;

            if (!$originAgency || !$destinationAgency) {
                $this->warn("\nAgencias faltantes para la guía ID: {$shipment->id}");
                $bar->advance();
                continue;
            }

            // Cálculo base igual que en ShipmentController
            $comisionOrigen = ($totalFlete * ($originAgency->com_origen / 100)) + $comisionArticulosOrigen;
            $comisionDestino = ($totalFlete * ($destinationAgency->com_destino / 100)) + $comisionArticulosDestino;

            // Lógica especial: si ninguna agencia es la ID 1, dividir por 2
            if ($shipment->origin_agency_id != 1 && $shipment->destination_agency_id != 1) {
                $comisionOrigen /= 2;
                $comisionDestino /= 2;
            }

            $shipment->update([
                'comision_origen' => $comisionOrigen,
                'comision_destino' => $comisionDestino,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n\nProceso completado. Se han recalculado $count guías.");
    }
}
