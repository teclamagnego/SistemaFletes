<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liquidación de Comisiones - {{ $agency->nombre }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .badge { padding: 2px 4px; border-radius: 3px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="header">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 60%;">
                    <h2 style="margin: 0;">{{ $empresa->nombre_fantasia ?? 'DOBLE G' }}</h2>
                    <p style="margin: 2px 0;">{{ $empresa->razon_social ?? '' }}</p>
                </td>
                <td style="border: none; text-align: right;">
                    <h3 style="margin: 0;">LIQUIDACIÓN DE COMISIONES</h3>
                    <p style="margin: 2px 0;">Fecha Emisión: {{ date('d/m/Y') }}</p>
                    <p style="margin: 2px 0;">Periodo: {{ date('d/m/Y', strtotime($from)) }} al {{ date('d/m/Y', strtotime($to)) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 15px;">
        <strong>Información de la Agencia:</strong> {{ $agency->nombre }}<br>
        <strong>Filtros aplicados:</strong> <br>
        Rol: {{ $roles_list[$role] }} | Estado Facturación: {{ $facturation_list[$status_factura] }} | Estado Guía: {{ $status_name }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>F. Entrega</th>
                <th>Guía</th>
                <th>Estado</th>
                <th>Participación</th>
                <th>Pagador</th>
                <th>F. Pago</th>
                <th class="text-end">Comisión</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($shipments as $s)
            @php $total += $s->comision_total_agencia; @endphp
            <tr>
                <td>{{ date('d/m/Y', strtotime($s->fecha)) }}</td>
                <td>
                    @php
                        $deliveryLog = $s->logs->where('status_to_id', \App\Models\ShipmentStatus::DELIVERED)->first();
                    @endphp
                    @if($deliveryLog)
                        {{ \Carbon\Carbon::parse($deliveryLog->created_at)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $s->tracking_number }}</td>
                <td>{{ $s->status->name }}</td>
                <td>{{ implode(' / ', $s->role_in_billing) }}</td>
                <td>{{ $s->cliente?->nombre_fantasia }}</td>
                <td>{{ $s->formaPago?->nombre }}</td>
                <td class="text-end">$ {{ number_format($s->comision_total_agencia, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-end">TOTAL COMISIONES:</td>
                <td class="text-end">$ {{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-style: italic;">
        Esta planilla detalla las comisiones correspondientes a las guías en el periodo indicado.
    </div>
</body>
</html>
