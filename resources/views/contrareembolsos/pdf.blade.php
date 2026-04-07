<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Contrareembolso - Guía {{ $contrareembolso->shipment->tracking_number }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .document {
            border: 2px solid #000;
            padding: 15px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header-logo {
            width: 40%;
            text-align: left;
            vertical-align: middle;
        }

        .header-title {
            width: 60%;
            text-align: right;
            vertical-align: middle;
        }

        .header-title h1 {
            margin: 0;
            font-size: 20px;
            color: #2c3e50;
        }

        .header-title p {
            margin: 3px 0;
            font-size: 11px;
            color: #555;
        }

        .logo-img {
            max-height: 50px;
            max-width: 180px;
        }

        .info-section {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-section td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            width: 160px;
            color: #2c3e50;
        }

        .section-boxes {
            width: 100%;
            border-spacing: 8px 0;
            margin-left: -8px;
            margin-bottom: 15px;
        }

        .section-box {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
            width: 50%;
        }

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            font-size: 12px;
            color: #2c3e50;
        }

        .section-box p {
            margin: 2px 0;
        }

        .amount-box {
            border: 2px solid #000;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
            background-color: #f5f5f5;
        }

        .amount-label {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
        }

        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #000;
            margin-top: 5px;
        }

        .signature-area {
            margin-top: 40px;
            width: 100%;
        }

        .signature-area td {
            width: 45%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 60px;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 10px;
            color: #555;
        }

        .dates-section {
            margin-top: 15px;
            width: 100%;
        }

        .dates-section td {
            width: 50%;
            padding: 5px;
        }

        .date-box {
            border: 1px solid #999;
            padding: 8px;
            min-height: 20px;
        }

        .date-label {
            font-weight: bold;
            font-size: 10px;
            color: #2c3e50;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .legal-legend {
            margin-top: 20px;
            font-size: 8px;
            text-align: justify;
            line-height: 1.2;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }

        .guia-info {
            background-color: #eee;
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 15px;
        }

        .guia-info p {
            margin: 2px 0;
        }
    </style>
</head>

<body>
    <div class="document">
        {{-- Encabezado --}}
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img">
                    @elseif($empresa && $empresa->logo)
                    <img src="{{ public_path('storage/' . $empresa->logo) }}" class="logo-img">
                    @else
                    <span style="font-size: 22px; font-weight: bold;">{{ $empresa->nombre_fantasia ?? 'DOBLE G' }}</span>
                    @endif
                    <br>
                    <span style="font-size: 9px; color: #555;">{{ $empresa->razon_social ?? '' }} — CUIT: {{ $empresa->cuit ?? '' }}</span>
                </td>
                <td class="header-title">
                    <h1>COMPROBANTE DE CONTRAREEMBOLSO</h1>
                    <p>Nº {{ str_pad($contrareembolso->id, 6, '0', STR_PAD_LEFT) }}</p>
                </td>
            </tr>
        </table>

        {{-- Info de la guía --}}
        <div class="guia-info">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <p><strong>Guía Nº:</strong> {{ $shipment->tracking_number }}</p>
                        <p><strong>Fecha Guía:</strong> {{ \Carbon\Carbon::parse($shipment->fecha)->format('d/m/Y') }}</p>
                    </td>
                    <td style="width: 50%;">
                        <p><strong>Forma de Pago:</strong> {{ $shipment->formaPago->nombre ?? '-' }}</p>
                        <p><strong>Total Guía:</strong> ${{ number_format($shipment->total_flete, 2, ',', '.') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Destinatario y Remitente --}}
        <table class="section-boxes">
            <tr>
                <td class="section-box">
                    <div class="section-title">DESTINATARIO (DESTINO)</div>
                    <p><strong>{{ $shipment->receiver?->nombre_fantasia ?? $shipment->receiver?->razon_social ?? '-' }}</strong></p>
                    <p>Dir: {{ $shipment->direccion_entrega ?? $shipment->receiver->direccion ?? '-' }}
                        {{ $shipment->receiver->localidad ? '(' . $shipment->receiver->localidad->nombre . ')' : '' }}</p>
                    <p>Destino: {{ $shipment->destinationAgency->nombre ?? '-' }}</p>
                </td>
                <td class="section-box">
                    <div class="section-title">REMITENTE (ORIGEN)</div>
                    <p><strong>{{ $shipment->sender?->nombre_fantasia ?? $shipment->sender?->razon_social ?? '-' }}</strong></p>
                    <p>Dir: {{ $shipment->sender->direccion ?? '-' }}
                        {{ $shipment->sender->localidad ? '(' . $shipment->sender->localidad->nombre . ')' : '' }}</p>
                    <p>Agencia: {{ $shipment->originAgency->nombre ?? '-' }}</p>
                </td>
            </tr>
        </table>

        {{-- Cliente que paga --}}
        <table class="info-section">
            <tr>
                <td class="info-label">Cliente (Paga):</td>
                <td><strong>{{ $contrareembolso->cliente?->nombre_fantasia ?? $contrareembolso->cliente?->razon_social ?? '-' }}</strong></td>
            </tr>
        </table>

        {{-- Monto del contrareembolso --}}
        <div class="amount-box">
            <div class="amount-label">Monto del Contrareembolso</div>
            <div class="amount-value">$ {{ number_format($contrareembolso->monto, 2, ',', '.') }}</div>
        </div>

        {{-- Fechas --}}
        <table class="dates-section">
            <tr>
                <td>
                    <div class="date-label">Fecha de Cobro:</div>
                    <div class="date-box">
                        {{ $contrareembolso->fecha_cobrado ? $contrareembolso->fecha_cobrado->format('d/m/Y') : '____/____/________' }}
                    </div>
                </td>
                <td>
                    <div class="date-label">Fecha de Rendición:</div>
                    <div class="date-box">
                        {{ $contrareembolso->fecha_rendido ? $contrareembolso->fecha_rendido->format('d/m/Y') : '____/____/________' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- Firma --}}
        <table class="signature-area">
            <tr>
                <td></td>
                <td style="width: 10%;"></td>
                <td>
                    <div class="signature-line">
                        Firma, Aclaración y DNI del que Recibe
                    </div>
                </td>
            </tr>
        </table>

        {{-- Leyenda --}}
        <div class="legal-legend">
            <strong>NOTA:</strong> Este comprobante certifica la entrega del monto de contrareembolso correspondiente a la guía indicada.
            El receptor del dinero firma conforme la recepción del importe total detallado. Cualquier reclamo deberá realizarse indicando
            el número de comprobante y guía asociada dentro de los 30 días de emitido.
        </div>
    </div>
</body>

</html>
