<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AgencyShipmentsBillingExport implements FromCollection, WithHeadings, WithMapping
{
    protected $shipments;
    protected $agency;

    public function __construct($shipments, $agency)
    {
        $this->shipments = $shipments;
        $this->agency = $agency;
    }

    public function collection()
    {
        return $this->shipments;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'F. Entrega',
            'Guía #',
            'Ref Remito',
            'Rol Agencia',
            'Estado',
            'Remitente',
            'Destinatario',
            'Pagador',
            'Forma Pago',
            'F. Pago',
            'Total Flete',
            'Comisión Agencia',
        ];
    }

    public function map($shipment): array
    {
        $deliveryLog = $shipment->logs->where('status_to_id', \App\Models\ShipmentStatus::DELIVERED)->first();
        $fEntrega = $deliveryLog ? \Carbon\Carbon::parse($deliveryLog->created_at)->format('d/m/Y') : '-';
        
        return [
            \Carbon\Carbon::parse($shipment->fecha)->format('d/m/Y'),
            $fEntrega,
            $shipment->tracking_number,
            $shipment->ref_remito,
            implode(' / ', $shipment->role_in_billing),
            $shipment->status?->name,
            $shipment->sender?->nombre_fantasia,
            $shipment->receiver?->nombre_fantasia,
            $shipment->cliente?->nombre_fantasia,
            $shipment->formaPago?->nombre,
            $shipment->faltarendir <= 0 ? 'Pagado' : 'Pendiente',
            $shipment->total_flete,
            $shipment->comision_total_agencia,
        ];
    }
}
