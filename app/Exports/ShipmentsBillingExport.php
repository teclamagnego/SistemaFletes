<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShipmentsBillingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $shipments;

    public function __construct($shipments)
    {
        $this->shipments = $shipments;
    }

    public function collection()
    {
        return $this->shipments;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Guía #',
            'Ref Remito',
            'Remitente',
            'Destinatario',
            'Origen',
            'Destino',
            'Valor Declarado',
            'Total Flete',
        ];
    }

    public function map($shipment): array
    {
        $bultoxItem = $shipment->items->first(function($item) {
            return $item->articulo && strtoupper($item->articulo->codigo) === 'BULTOX';
        });

        return [
            $shipment->fecha,
            $shipment->tracking_number,
            $shipment->ref_remito,
            $shipment->sender?->nombre_fantasia,
            $shipment->receiver?->nombre_fantasia,
            $shipment->originAgency?->nombre,
            $shipment->destinationAgency?->nombre,
            $bultoxItem ? $bultoxItem->precio_unitario : 0,
            $shipment->total_flete,
        ];
    }
}