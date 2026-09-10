<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Shipment;
use Illuminate\Console\Command;

class RecalculateAgencyCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:recalculate-agency-commissions
        {agency : Nombre o ID de la agencia (interviene como origen o destino)}
        {--from=2026-08-01 : Fecha desde la cual recalcular (inclusive)}
        {--dry-run : Muestra los cambios sin guardarlos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula comision_origen/comision_destino (fórmula corregida) de las guías donde interviene una agencia, desde una fecha dada';

    public function handle(): int
    {
        $agencyParam = $this->argument('agency');
        $from = $this->option('from');
        $dryRun = (bool) $this->option('dry-run');

        $agency = Agency::where('id', $agencyParam)->orWhere('nombre', $agencyParam)->first();

        if (! $agency) {
            $this->error("No se encontró la agencia '{$agencyParam}'.");

            return self::FAILURE;
        }

        $this->info("Agencia: {$agency->nombre} (ID {$agency->id})");
        $this->info('Guías desde ' . $from . ($dryRun ? ' [DRY RUN - no se guarda nada]' : ''));

        $shipments = Shipment::with(['items.articulo', 'originAgency', 'destinationAgency'])
            ->where('fecha', '>=', $from)
            ->where(function ($q) use ($agency) {
                $q->where('origin_agency_id', $agency->id)
                    ->orWhere('destination_agency_id', $agency->id);
            })
            ->orderBy('id')
            ->get();

        $this->info("Guías encontradas: {$shipments->count()}");

        $rows = [];
        $totalDeltaOrigen = 0;
        $totalDeltaDestino = 0;

        foreach ($shipments as $shipment) {
            if (! $shipment->originAgency || ! $shipment->destinationAgency) {
                $this->warn("Guía {$shipment->id}: falta agencia origen/destino, se omite.");
                continue;
            }

            [$comisionOrigen, $comisionDestino] = $this->calcularComisiones($shipment);

            $deltaOrigen = round($comisionOrigen - $shipment->comision_origen, 2);
            $deltaDestino = round($comisionDestino - $shipment->comision_destino, 2);

            if ($deltaOrigen === 0.0 && $deltaDestino === 0.0) {
                continue;
            }

            $rows[] = [
                $shipment->id,
                $shipment->fecha,
                $shipment->originAgency->nombre,
                $shipment->destinationAgency->nombre,
                number_format($shipment->comision_origen, 2) . ' -> ' . number_format($comisionOrigen, 2),
                number_format($shipment->comision_destino, 2) . ' -> ' . number_format($comisionDestino, 2),
            ];

            $totalDeltaOrigen += $deltaOrigen;
            $totalDeltaDestino += $deltaDestino;

            if (! $dryRun) {
                $shipment->update([
                    'comision_origen' => $comisionOrigen,
                    'comision_destino' => $comisionDestino,
                ]);

                \Log::info("Comisión recalculada para guía {$shipment->id}", [
                    'comision_origen_old' => $shipment->getOriginal('comision_origen'),
                    'comision_origen_new' => $comisionOrigen,
                    'comision_destino_old' => $shipment->getOriginal('comision_destino'),
                    'comision_destino_new' => $comisionDestino,
                ]);
            }
        }

        if (empty($rows)) {
            $this->info('Ninguna guía requiere cambios.');

            return self::SUCCESS;
        }

        $this->table(['ID', 'Fecha', 'Origen', 'Destino', 'Com. Origen', 'Com. Destino'], $rows);

        $this->info('Guías modificadas: ' . count($rows));
        $this->info('Delta total comisión origen: ' . number_format($totalDeltaOrigen, 2));
        $this->info('Delta total comisión destino: ' . number_format($totalDeltaDestino, 2));
        $this->info($dryRun
            ? 'DRY RUN: no se guardó ningún cambio. Ejecutar sin --dry-run para aplicar.'
            : 'Cambios guardados.');

        return self::SUCCESS;
    }

    /**
     * Misma fórmula que ShipmentController@store / @update.
     */
    private function calcularComisiones(Shipment $shipment): array
    {
        $comisionArticulosOrigen = 0;
        $comisionArticulosDestino = 0;
        $baseComisionableOrigen = 0;
        $baseComisionableDestino = 0;

        foreach ($shipment->items as $item) {
            $articulo = $item->articulo;

            if (! $articulo) {
                continue;
            }

            if ($articulo->com_origen > 0) {
                $comisionArticulosOrigen += ($articulo->com_origen / 100) * $item->total;
                $baseComisionableOrigen += $item->total;
            }

            if ($articulo->com_destino > 0) {
                $comisionArticulosDestino += ($articulo->com_destino / 100) * $item->total;
                $baseComisionableDestino += $item->total;
            }
        }

        $originAgency = $shipment->originAgency;
        $destinationAgency = $shipment->destinationAgency;

        $comisionOrigen = ($baseComisionableOrigen * ($originAgency->com_origen / 100)) + $comisionArticulosOrigen;
        $comisionDestino = ($baseComisionableDestino * ($destinationAgency->com_destino / 100)) + $comisionArticulosDestino;

        // Si origen y destino son distintos a ID 1, dividir comisiones x 2
        if ($shipment->origin_agency_id != 1 && $shipment->destination_agency_id != 1) {
            $comisionOrigen /= 2;
            $comisionDestino /= 2;
        }

        return [$comisionOrigen, $comisionDestino];
    }
}
