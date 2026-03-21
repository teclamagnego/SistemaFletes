<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liquidación de Guías</title>
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
        .shipment-container {
            page-break-after: always;
        }
        .shipment-container:last-child {
            page-break-after: auto;
        }
        .copy-container {
            height: 48%;
            position: relative;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
            overflow: hidden;
        }
        .copy-container:last-child {
            border-bottom: none;
            padding-top: 20px;
        }
        .header-table { width: 100%; margin-bottom: 10px; }
        .header-logo { width: 40%; text-align: left; }
        .header-title { width: 60%; text-align: right; font-size: 16px; font-weight: bold; }
        .sub-header { width: 100%; margin-bottom: 5px; border-bottom: 1px solid #000; padding-bottom: 5px; }
        .section-table { width: 100%; margin-bottom: 10px; border-spacing: 10px 0; margin-left: -10px; }
        .section-box { border: 1px solid #000; padding: 5px; vertical-align: top; width: 50%; }
        .section-title { font-weight: bold; text-decoration: underline; margin-bottom: 5px; font-size: 12px; }
        table.items-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.items-table th, table.items-table td { border: 1px solid #000; padding: 4px; text-align: left; font-size: 10px; }
        table.items-table th { background: #eee; }
        .totals-table { width: 100%; margin-top: 5px; }
        .totals-table td { text-align: right; }
        .total-amount { font-size: 14px; font-weight: bold; }
        .notes-section { margin-top: 10px; min-height: 20px; }
        .signature-section { margin-top: 20px; border-top: 1px solid #000; padding-top: 5px; width: 60%; }
        .legal-legend { margin-top: 15px; font-size: 8px; text-align: justify; line-height: 1; color: #333; }
        .logo-img { max-height: 50px; max-width: 180px; }
        table.items-table td.special-desc { font-size: 26px !important; font-weight: bold !important; }
    </style>
</head>
<body>
    @foreach($shipments as $shipment)
    <div class="shipment-container">
        @for($i = 0; $i < 2; $i++)
        <div class="copy-container">
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

            <table class="sub-header">
                <tr>
                    <td style="text-align: left;">
                        @if($sucursal) Cel: {{ $sucursal->telefono }} @elseif($empresa) CUIT: {{ $empresa->cuit }} @endif
                    </td>
                    <td style="text-align: right;">
                        FECHA: {{ \Carbon\Carbon::parse($shipment->fecha)->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: left; padding-top: 5px; font-weight: bold; font-size: 12px;">
                        PAGA: {{ $shipment->cliente?->nombre_fantasia ?? 'N/A' }} ({{ $shipment->formaPago?->nombre ?? 'N/A' }})
                    </td>
                </tr>
            </table>

            <table class="section-table">
                <tr>
                    <td class="section-box">
                        <div class="section-title">REMITENTE {{ $shipment->payer === 'sender' ? '(PAGA EN ORIGEN)' : '' }}</div>
                        <div><strong>{{ $shipment->sender?->nombre_fantasia }}</strong></div>
                        <div>Dir: {{ $shipment->sender?->direccion }} ({{ $shipment->sender?->localidad?->nombre ?? 'N/A' }})</div>
                        <div>Agencia: {{ $shipment->originAgency?->nombre }}</div>
                    </td>
                    <td class="section-box">
                        <div class="section-title">DESTINATARIO {{ $shipment->payer === 'receiver' ? '(PAGA EN DESTINO)' : '' }}</div>
                        <div><strong>{{ $shipment->receiver?->nombre_fantasia }}</strong></div>
                        <div>Dir: {{ $shipment->direccion_entrega ?? $shipment->receiver?->direccion }} ({{ $shipment->receiver?->localidad?->nombre ?? 'N/A' }})</div>
                        <div>Destino: {{ $shipment->destinationAgency?->nombre }}</div>
                    </td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">Cant.</th>
                        <th>Descripción</th>
                        <th style="width: 12%;">P. Unit.</th>
                        <th style="width: 10%;">%</th>
                        <th style="width: 15%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shipment->items as $item)
                    @php
                        $articulo = $item->articulo;
                        $codigo = strtoupper($articulo->codigo ?? '');
                        $isSpecial = in_array($codigo, ['REE', 'BULTOX']) || str_contains(strtoupper($item->descripcion), 'REE');
                        $displayName = $item->descripcion;
                        if ($codigo === 'REE') { $displayName .= ' ' . number_format($item->precio_unitario, 2); }
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $item->cantidad }}</td>
                        <td class="{{ $isSpecial ? 'special-desc' : '' }}">{{ $displayName }}</td>
                        <td style="text-align: right;">${{ number_format($item->precio_unitario, 2) }}</td>
                        <td style="text-align: center;">{{ $item->bonificacion }}%</td>
                        <td style="text-align: right;">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="totals-table">
                <tr><td class="total-amount">TOTAL: ${{ number_format($shipment->total_flete, 2) }}</td></tr>
            </table>

            @if($shipment->notas) <div class="notes-section">{{ $shipment->notas }}</div> @endif

            <div class="signature-section">Firma Destino, Aclaracion y nro documento</div>

            <div class="legal-legend">
                <strong>LIMITACIÓN DE RESPONSABILIDAD:</strong> No se aceptan reclamos pasados los 30 dias... (Ley 12.346)
            </div>
        </div>
        @endfor
    </div>
    @endforeach
</body>
</html>
