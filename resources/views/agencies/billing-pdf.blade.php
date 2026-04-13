<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detalle de Liquidación - {{ $factura->nro_factura }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .total-row { font-weight: bold; background-color: #eee; }
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 10px; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Detalle de Liquidación de Comisiones</h2>
        <p><strong>Factura Nº:</strong> {{ $factura->nro_factura }} | <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}</p>
    </div>

    <div class="info">
        <p><strong>Agencia:</strong> {{ $factura->agency->nombre }}</p>

    </div>

    <table>
        <thead>
            <tr>
                <th>F. Entrega</th>
                <th>Guía Nº</th>
                <th>Remitente</th>
                <th>Destinatario</th>
                <th>Rol</th>
                <th class="text-end">Comisión</th>
            </tr>
        </thead>
        <tbody>
            @php $totalCalculado = 0; @endphp
            @foreach($shipments as $s)
                @php
                    $comision = 0;
                    $roles = [];

                    if ($s->agencia_f_origen_id == $factura->id) {
                        $comision += $s->comision_origen;
                        $roles[] = 'Origen';
                    }

                    if ($s->agencia_f_destino_id == $factura->id) {
                        $comision += $s->comision_destino;
                        $roles[] = 'Destino';
                    }

                    $totalCalculado += $comision;
                @endphp
                <tr>
                    <td>
                        @php
                            $deliveryLog = $s->logs->where('status_to_id', \App\Models\ShipmentStatus::DELIVERED)->first();
                        @endphp
                        {{ $deliveryLog ? \Carbon\Carbon::parse($deliveryLog->created_at)->format('d/m/Y') : \Carbon\Carbon::parse($s->fecha)->format('d/m/Y') }}
                    </td>
                    <td>{{ $s->tracking_number }}</td>
                    <td>{{ $s->sender?->nombre_fantasia }}</td>
                    <td>{{ $s->receiver?->nombre_fantasia }}</td>
                    <td>{{ implode(' / ', $roles) }}</td>
                    <td class="text-end">$ {{ number_format($comision, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-end">TOTAL LIQUIDADO:</td>
                <td class="text-end">$ {{ number_format($totalCalculado, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @php
        // Eliminación insensible a mayúsculas/minúsculas de la frase sobrante
        $obs = str_ireplace('Mirar Detalle adjunto.', '', $factura->observacion);
        $obs = str_ireplace('Mirar detalle adjunto.', '', $obs);
        $obs = trim($obs);
    @endphp

    @if($obs)
        <div style="margin-top: 20px;">
            <strong>Observaciones:</strong><br>
            {{ $obs }}
        </div>
    @endif

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} - Sistema de Gestión Doble G
    </div>
</body>
</html>
