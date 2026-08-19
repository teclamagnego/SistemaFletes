<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Planilla de Guías para Facturación - {{ $cliente->nombre_fantasia }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-end {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
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
                    <h3 style="margin: 0;">PLANILLA DE GUÍAS</h3>
                    <p style="margin: 2px 0;">Fecha Emisión: {{ date('d/m/Y') }}</p>
                    <p style="margin: 2px 0;">Periodo: {{ date('d/m/Y', strtotime($from)) }} al {{ date('d/m/Y',
                        strtotime($to)) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 15px;">
        <strong>Información del Cliente:</strong><br>
        {{ $cliente->nombre_fantasia }} ({{ $cliente->razon_social }})<br>
        Dirección: {{ $cliente->direccion }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Número de Guía</th>
                <th>Remito Referencia</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Forma de Pago</th>
                <th class="text-end">Monto</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($shipments as $s)
            @php $total += $s->total_flete; @endphp
            <tr>
                <td>{{ date('d/m/Y', strtotime($s->fecha)) }}</td>
                <td>{{ $s->tracking_number }}</td>
                <td>{{ $s->ref_remito }}</td>
                <td>{{ $s->originAgency?->nombre ?? '-' }}</td>
                <td>{{ $s->destinationAgency?->nombre ?? '-' }}</td>
                <td>{{ $s->formaPago?->nombre }}</td>
                <td class="text-end">$ {{ number_format($s->total_flete, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-end">TOTAL:</td>
                <td class="text-end">$ {{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-style: italic;">
        Esta planilla detalla las guías incluidas en la facturación del periodo indicado.
    </div>
</body>

</html>