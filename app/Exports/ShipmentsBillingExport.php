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
            'Remitente',
            'Destinatario',
            'Forma de Pago',
            'Factura ID',
            'Total Flete',
        ];
    }

    public function map($shipment): array
    {
        return [
            $shipment->fecha,
            $shipment->tracking_number,
            $shipment->sender?->nombre_fantasia,
            $shipment->receiver?->nombre_fantasia,
            $shipment->formaPago?->nombre,
            $shipment->factura_id > 0 ? $shipment->factura_id : 'Pendiente',
            $shipment->total_flete,
        ];
    }
}