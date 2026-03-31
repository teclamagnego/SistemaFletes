<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Informe de Guías de Contado</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #333;
        }

        .header {
            margin-bottom: 20px;
        }

        .logo-container {
            width: 50%;
            float: left;
        }

        .report-info {
            width: 45%;
            float: right;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        .filters {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .filters p {
            margin: 2px 0;
        }

        .summary {
            margin-bottom: 15px;
        }

        .summary-box {
            display: inline-block;
            width: 45%;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            border-radius: 4px;
            margin-right: 4%;
        }

        .summary-box:last-child {
            margin-right: 0;
        }

        .summary-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            background-color: #f1f1f1;
            font-weight: bold;
            font-size: 11px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-container">
            <h1 style="margin: 0; color: #2c3e50;">{{ $empresa->nombre_fantasia ?? 'DOBLE G' }}</h1>
            <p style="margin: 2px 0;">{{ $empresa->razon_social ?? '' }}</p>
            <p style="margin: 2px 0;">CUIT: {{ $empresa->cuit ?? '' }}</p>
        </div>
        <div class="report-info">
            <h2 style="margin: 0; color: #7f8c8d;">GUÍAS DE CONTADO</h2>
            <p style="margin: 5px 0;"><strong>Fecha de Emisión:</strong> {{ date('d/m/Y H:i') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="filters">
        <p><strong>Filtros aplicados:</strong></p>
        <p>
            <strong>Período:</strong>
            {{ request('fecha_desde') ? date('d/m/Y', strtotime(request('fecha_desde'))) : 'Sin límite' }}
            al
            {{ request('fecha_hasta') ? date('d/m/Y', strtotime(request('fecha_hasta'))) : 'Sin límite' }}
            &nbsp; | &nbsp; <strong>Ag. Origen:</strong> {{ $agenciaOrigen }}
            &nbsp; | &nbsp; <strong>Ag. Destino:</strong> {{ $agenciaDestino }}
        </p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <div class="summary-label">Total Guías</div>
            <div class="summary-value">{{ $totalGuias }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Importe Total</div>
            <div class="summary-value">$ {{ number_format($totalImporte, 2, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Guía #</th>
                <th style="width: 10%;">Fecha</th>
                <th style="width: 20%;">Remitente</th>
                <th style="width: 20%;">Destinatario</th>
                <th style="width: 14%;">Ag. Origen</th>
                <th style="width: 14%;">Ag. Destino</th>
                <th style="width: 14%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $s)
            <tr>
                <td>{{ $s->tracking_number }}</td>
                <td>{{ date('d/m/Y', strtotime($s->fecha)) }}</td>
                <td>{{ $s->sender->nombre_fantasia ?? $s->sender->razon_social ?? '-' }}</td>
                <td>{{ $s->receiver->nombre_fantasia ?? $s->receiver->razon_social ?? '-' }}</td>
                <td>{{ $s->originAgency->nombre ?? '-' }}</td>
                <td>{{ $s->destinationAgency->nombre ?? '-' }}</td>
                <td class="text-end">$ {{ number_format($s->total_flete, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No se encontraron guías.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-end">TOTAL:</td>
                <td class="text-end">$ {{ number_format($totalImporte, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Informe generado el {{ date('d/m/Y') }} a las {{ date('H:i') }} hs. — {{ $empresa->nombre_fantasia ?? 'DOBLE G' }}</p>
    </div>
</body>

</html>
