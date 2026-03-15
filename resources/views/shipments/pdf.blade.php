<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Guía de Carga - {{ $shipment->tracking_number }}</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .copy-container {
            height: 47%;
            position: relative;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
            overflow: hidden;
        }

        .copy-container:last-child {
            border-bottom: none;
            padding-top: 20px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .header-logo {
            width: 40%;
            text-align: left;
        }

        .header-title {
            width: 60%;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        .sub-header {
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .sub-header td {
            vertical-align: bottom;
        }

        .section-table {
            width: 100%;
            margin-bottom: 10px;
            border-spacing: 10px 0;
            margin-left: -10px;
        }

        .section-box {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
            width: 50%;
        }

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            font-size: 12px;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.items-table th,
        table.items-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        table.items-table th {
            background: #eee;
        }

        .totals-table {
            width: 100%;
            margin-top: 5px;
        }

        .totals-table td {
            text-align: right;
        }

        .total-amount {
            font-size: 14px;
            font-weight: bold;
        }

        .notes-section {
            margin-top: 10px;
            min-height: 20px;
        }

        .signature-section {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 5px;
            width: 60%;
        }

        .legal-legend {
            margin-top: 15px;
            font-size: 8px;
            text-align: justify;
            line-height: 1;
            color: #333;
        }

        .logo-img {
            max-height: 50px;
            max-width: 180px;
        }
    </style>
</head>

<body>

    @for($i = 0; $i < 2; $i++) <div class="copy-container">
        {{-- Linea 1: Logo y Orden de Recoleccion --}}
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img">
                    @elseif($empresa && $empresa->logo)
                    <img src="{{ public_path('storage/' . $empresa->logo) }}" class="logo-img">
                    @else
                    <span style="font-size: 18px; font-weight: bold;">DOBLE G</span>
                    @endif
                </td>
                <td class="header-title">
                    ORDEN DE RECOLECCION Nº {{ $shipment->tracking_number }}
                </td>
            </tr>
        </table>

        {{-- Linea 2: Celular y Fecha --}}
        <table class="sub-header">
            <tr>
                <td style="text-align: left;">
                    @if($sucursal)
                    Cel: {{ $sucursal->telefono }}
                    @elseif($empresa)
                    CUIT: {{ $empresa->cuit }}
                    @endif
                </td>
                <td style="text-align: right;">
                    FECHA: {{ \Carbon\Carbon::parse($shipment->fecha)->format('d/m/Y') }}
                </td>
            </tr>
        </table>

        {{-- Remitente y Destino --}}
        <table class="section-table">
            <tr>
                <td class="section-box">
                    <div class="section-title">REMITENTE {{ $shipment->payer === 'sender' ? '(Paga: ' .
                        $shipment->formaPago->nombre . ')' : '' }}</div>
                    <div><strong>{{ $shipment->sender?->nombre_fantasia }}</strong></div>
                    <div>Dir: {{ $shipment->sender?->direccion }} ({{ $shipment->sender?->localidad?->nombre ?? 'N/A'
                        }})</div>
                    <div>Agencia: {{ $shipment->originAgency?->nombre }}</div>
                </td>
                <td class="section-box">
                    <div class="section-title">DESTINATARIO {{ $shipment->payer === 'receiver' ? '(Paga: ' .
                        $shipment->formaPago->nombre . ')' : '' }}</div>
                    <div><strong>{{ $shipment->receiver?->nombre_fantasia }}</strong></div>
                    <div>Dir: {{ $shipment->direccion_entrega ?? $shipment->receiver?->direccion }} ({{
                        $shipment->receiver?->localidad?->nombre ?? 'N/A' }})</div>
                    <div>Destino: {{ $shipment->destinationAgency?->nombre }}</div>
                </td>
            </tr>
        </table>

        {{-- Detalle --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Cant.</th>
                    <th>Descripción</th>
                    <th style="width: 12%;">P. Unit.</th>
                    <th style="width: 10%;">Bonif.</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipment->items as $item)
                <tr>
                    <td style="text-align: center;">{{ $item->cantidad }}</td>
                    <td>{{ $item->descripcion }}</td>
                    <td style="text-align: right;">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td style="text-align: center;">{{ $item->bonificacion }}%</td>
                    <td style="text-align: right;">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td class="total-amount">TOTAL: ${{ number_format($shipment->total_flete, 2) }}</td>
            </tr>
        </table>

        {{-- Observaciones (sin titulo) --}}
        @if($shipment->notas)
        <div class="notes-section">
            {{ $shipment->notas }}
        </div>
        @endif

        {{-- Firma --}}
        <div class="signature-section">
            Firma Destino, Aclaracion y nro documento
        </div>

        {{-- Leyenda Legal --}}
        <div class="legal-legend">
            <strong>LIMITACIÓN DE RESPONSABILIDAD:</strong> No se aceptan reclamos pasados los 30 dias. Todo reclamo
            debe ser efectuado indicando el número de guía, fecha de remisión y destino. La empresa limita su
            responsabilidad a lo establecido en el articulo Art. 117 del Reglamento General de la ley 12.346 y Art 172 y
            173 del Codigo de Comercio, y por lo tanto, el seguro corre por cuenta del remitente y consignatario. De no
            hacerlo, la empresa se desliga de toda responsabilidad.
        </div>
        </div>
        @endfor
</body>

</html>