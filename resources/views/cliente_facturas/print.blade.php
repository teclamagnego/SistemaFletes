<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Factura de Guías - {{ $factura->nro_factura }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            margin-bottom: 30px;
        }

        .logo-container {
            width: 50%;
            float: left;
        }

        .invoice-info {
            width: 45%;
            float: right;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        .client-info {
            margin-bottom: 20px;
            border: 1px solid #eee;
            padding: 10px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .text-end {
            text-align: right;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .total-row {
            background-color: #f1f1f1;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="logo-container">
            <h1 style="margin: 0; color: #2c3e50;">{{ $empresa->nombre_fantasia ?? 'DOBLE G' }}</h1>
            <p style="margin: 2px 0;">{{ $empresa->razon_social ?? '' }}</p>
            <p style="margin: 2px 0;">CUIT: {{ $empresa->cuit ?? '' }}</p>
            <p style="margin: 2px 0;">{{ $empresa->direccion ?? '' }}</p>
        </div>
        <div class="invoice-info">
            <h2 style="margin: 0; color: #7f8c8d;">FACTURA / NOTA</h2>
            <p style="margin: 5px 0;"><strong>Número:</strong> {{ $factura->nro_factura ?? 'FAC-' .
                str_pad($factura->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p style="margin: 5px 0;"><strong>Fecha:</strong> {{ date('d/m/Y', strtotime($factura->fecha)) }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="client-info">
        <p style="margin: 0 0 5px 0;"><strong>CLIENTE:</strong> {{ $factura->cliente?->nombre_fantasia ?? '(S/D)' }}</p>
        <p style="margin: 0 0 5px 0;"><strong>RAZÓN SOCIAL:</strong> {{ $factura->cliente?->razon_social ?? '(S/D)' }}</p>
        <p style="margin: 0 0 5px 0;"><strong>DIRECCIÓN:</strong> {{ $factura->cliente?->direccion ?? '(S/D)' }}</p>
        <p style="margin: 0;"><strong>CUIT/DOC:</strong> {{ $factura->cliente?->documento_nro ?? '(S/D)' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 20%;">Guía #</th>
                <th>Detalle (Origen -> Destino)</th>
                <th style="width: 15%; text-align: right;">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->shipments as $s)
            <tr>
                <td>{{ date('d/m/Y', strtotime($s->fecha)) }}</td>
                <td>{{ $s->tracking_number }}</td>
                <td>
                    {{ $s->sender?->nombre_fantasia ?? '(sin remitente)' }} -> {{ $s->receiver?->nombre_fantasia ?? '(sin destinatario)' }}
                    <br><small style="color: #666;">{{ $s->formaPago?->nombre ?? '' }}</small>
                </td>
                <td class="text-end">$ {{ number_format($s->total_flete, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-end">TOTAL FACTURADO:</td>
                <td class="text-end text-primary">$ {{ number_format($factura->total, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Observaciones:</strong></p>
        <p style="font-style: italic;">{{ $factura->observacion }}</p>
    </div>

    <div class="footer">
        <p>Gracias por su confianza. Por consultas o pagos, comuníquese al {{ $empresa->telefono ?? '' }}.</p>
        <p>Comprobante no válido como factura fiscal según normativa vigente.</p>
    </div>
</body>

</html>