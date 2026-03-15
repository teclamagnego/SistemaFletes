<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Estado de Cuenta - {{ $cliente->nombre_fantasia }}</title>
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

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
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

        .saldo-anterior {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .debe {
            color: #d9534f;
        }

        .haber {
            color: #5cb85c;
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
                    <p style="margin: 2px 0;">CUIT: {{ $empresa->cuit ?? '' }}</p>
                </td>
                <td style="border: none; text-align: right;">
                    <h3 style="margin: 0;">ESTADO DE CUENTA</h3>
                    <p style="margin: 2px 0;">Fecha: {{ date('d/m/Y') }}</p>
                    <p style="margin: 2px 0;">Periodo: {{ date('d/m/Y', strtotime($from)) }} al {{ date('d/m/Y',
                        strtotime($to)) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 15px;">
        <strong>Cliente:</strong> {{ $cliente->nombre_fantasia }}<br>
        <strong>Razón Social:</strong> {{ $cliente->razon_social }}<br>
        <strong>Dirección:</strong> {{ $cliente->direccion }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Referencia</th>
                <th>Detalle</th>
                <th class="text-end">Debe</th>
                <th class="text-end">Haber</th>
                <th class="text-end">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php $saldo = $saldoAnterior; @endphp
            <tr class="saldo-anterior">
                <td>{{ date('d/m/Y', strtotime($from)) }}</td>
                <td colspan="3">SALDO ANTERIOR</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">$ {{ number_format($saldo, 2) }}</td>
            </tr>
            @foreach($movimientos as $mov)
            @php $saldo += ($mov['debe'] - $mov['haber']); @endphp
            <tr>
                <td>{{ date('d/m/Y', strtotime($mov['fecha'])) }}</td>
                <td>{{ $mov['tipo'] }}</td>
                <td>{{ $mov['referencia'] }}</td>
                <td>{{ $mov['detalle'] }}</td>
                <td class="text-end debe">{{ $mov['debe'] > 0 ? number_format($mov['debe'], 2) : '-' }}</td>
                <td class="text-end haber">{{ $mov['haber'] > 0 ? number_format($mov['haber'], 2) : '-' }}</td>
                <td class="text-end"><strong>$ {{ number_format($saldo, 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot style="background-color: #eee;">
            <tr>
                <td colspan="6" class="text-end"><strong>SALDO ACTUAL:</strong></td>
                <td class="text-end"><strong>$ {{ number_format($saldo, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generado automáticamente por el Sistema de Fletes - {{ date('d/m/Y H:i') }}
    </div>
</body>

</html>