<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Guías de Carga</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 1cm;
        }
        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 15px;
        }
        .filters-display {
            margin-bottom: 20px;
            text-align: center;
            font-size: 11px;
        }
        .filters-display span {
            margin-right: 10px;
            padding: 3px 6px;
            background-color: #e9e9e9;
            border-radius: 3px;
            white-space: nowrap;
        }
        .header-info {
            text-align: right;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header-info">
        Fecha de Impresión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

    <h1>Listado de Guías de Carga</h1>

    <div class="filters-display">
        @if(isset($filters['tracking_number']) && $filters['tracking_number'])
            <span>Guía #: {{ $filters['tracking_number'] }}</span>
        @endif
        @if(isset($filters['cliente']) && $filters['cliente'])
            <span>Cliente: {{ $filters['cliente'] }}</span>
        @endif
        @if(isset($filters['origin_agency_filter_id']) && $filters['origin_agency_filter_id'])
            <span>Agencia Origen: {{ \App\Models\Agency::find($filters['origin_agency_filter_id'])->nombre ?? 'N/A' }}</span>
        @endif
        @if(isset($filters['destination_agency_filter_id']) && $filters['destination_agency_filter_id'])
            <span>Agencia Destino: {{ \App\Models\Agency::find($filters['destination_agency_filter_id'])->nombre ?? 'N/A' }}</span>
        @endif
        @if(isset($statusName) && $statusName)
            <span>Estado: {{ $statusName }}</span>
        @endif
        @if(isset($filters['from_date']) && $filters['from_date'])
            <span>Desde: {{ \Carbon\Carbon::parse($filters['from_date'])->format('d/m/Y') }}</span>
        @endif
        @if(isset($filters['to_date']) && $filters['to_date'])
            <span>Hasta: {{ \Carbon\Carbon::parse($filters['to_date'])->format('d/m/Y') }}</span>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Guía #</th>
                <th>Fecha</th>
                <th>Fecha Entregado</th>
                <th>Remitente</th>
                <th>Destinatario</th>
                <th>Agencia Origen</th>
                <th>Agencia Destino</th>
                <th>Estado</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
            @endphp
            @foreach($shipments as $s)
                <tr>
                    <td>{{ $s->tracking_number }}</td>
                    <td>{{ $s->created_at->format('d/m/Y') }}</td>
                    <td>{{ $s->delivery_date ? \Carbon\Carbon::parse($s->delivery_date)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $s->sender?->nombre_fantasia ?? 'N/A' }}</td>
                    <td>{{ $s->receiver?->nombre_fantasia ?? 'N/A' }}</td>
                    <td>{{ $s->originAgency?->nombre ?? 'N/A' }}</td>
                    <td>{{ $s->destinationAgency?->nombre ?? 'N/A' }}</td>
                    <td>{{ $s->status?->name ?? 'N/A' }}</td>
                    <td class="text-right">${{ number_format($s->total_flete, 2) }}</td>
                </tr>
                @php
                    $grandTotal += $s->total_flete;
                @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="8" class="text-right">TOTAL GENERAL:</td>
                <td class="text-right">${{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>