<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Recibo de Pago - {{ $recibo->nro_recibo ?? $recibo->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .receipt-container {
            width: 100%;
            border: 2px solid #333;
            padding: 20px;
            box-sizing: border-box;
        }

        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo-section {
            width: 50%;
            float: left;
        }

        .info-section {
            width: 45%;
            float: right;
            text-align: right;
        }

        .clear {
            clear: both;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            border: 1px solid #333;
            padding: 5px;
        }

        .content-section {
            margin-bottom: 20px;
        }

        .row {
            margin-bottom: 10px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 5px;
        }

        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }

        .amount-box {
            border: 2px solid #333;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            width: fit-content;
            margin: 20px 0;
        }

        .footer {
            margin-top: 50px;
        }

        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="header">
            <div class="logo-section">
                <h2 style="margin: 0;">{{ $empresa->nombre_fantasia ?? 'DOBLE G' }}</h2>
                <p style="margin: 2px 0;">{{ $empresa->razon_social ?? '' }}</p>
                <p style="margin: 2px 0;">{{ $empresa->direccion ?? '' }}</p>
                <p style="margin: 2px 0;">Tel: {{ $empresa->telefono ?? '' }}</p>
            </div>
            <div class="info-section">
                <h3 style="margin: 0;">RECIBO</h3>
                <p style="margin: 5px 0;"><strong>Nro:</strong> {{ $recibo->nro_recibo ?? str_pad($recibo->id, 8, '0',
                    STR_PAD_LEFT) }}</p>
                <p style="margin: 5px 0;"><strong>Fecha:</strong> {{ date('d/m/Y', strtotime($recibo->fecha)) }}</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="title">RECIBO DE PAGO</div>

        <div class="content-section">
            <div class="row">
                <span class="label">Recibimos de:</span>
                <span>{{ $recibo->cliente->nombre_fantasia }} ({{ $recibo->cliente->razon_social }})</span>
            </div>
            <div class="row">
                <span class="label">La suma de:</span>
                <span>$ {{ number_format($recibo->monto, 2) }}</span>
            </div>
            <div class="row">
                <span class="label">Concepto:</span>
                <span>{{ $recibo->observaciones ?? 'Cancelación de saldo / Pago a cuenta' }}</span>
            </div>
            <div class="row">
                <span class="label">Forma de Pago:</span>
                <span>{{ $recibo->formaPago->nombre }}</span>
            </div>
        </div>

        <div class="amount-box">
            TOTAL: $ {{ number_format($recibo->monto, 2) }}
        </div>

        <div class="footer">
            <p><strong>Nota:</strong> Este documento sirve como comprobante de pago.</p>
            <div class="signature-box">
                Firma y Sello
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>

</html>