@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-file-earmark-text"></i> Guías de Carga</h2>
    @can('shipments.create')<a href="{{ route('shipments.create') }}" class="btn btn-primary"><i
            class="bi bi-plus-lg"></i> Nueva Guía</a>@endcan
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('shipments.index') }}" method="GET" class="row g-2">
            <div class="col-md-2">
                <input type="text" name="tracking_number" class="form-control" placeholder="Guía #"
                    value="{{ request('tracking_number') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="cliente" class="form-control" placeholder="Cliente (Rem/Dest)"
                    value="{{ request('cliente') }}">
            </div>
            <div class="col-md-3">
                <select name="agency_id" class="form-select">
                    <option value="">Todas las Agencias</option>
                    @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}" {{ request('agency_id')==$agency->id ? 'selected' : '' }}>
                        {{ $agency->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status_id" class="form-select">
                    <option value="">Todos los Estados</option>
                    @foreach($statuses as $st)
                    <option value="{{ $st->id }}" {{ request('status_id')==$st->id ? 'selected' : '' }}>{{ $st->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                <a href="{{ route('shipments.index') }}" class="btn btn-outline-secondary w-100"><i
                        class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Guía #</th>
                    <th>Remitente</th>
                    <th>Destinatario</th>
                    <th>Agencia Origen</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $s)
                <tr>
                    <td><strong>{{ $s->tracking_number }}</strong></td>
                    <td>{{ $s->sender?->nombre_fantasia ?? 'N/A' }}</td>
                    <td>{{ $s->receiver?->nombre_fantasia ?? 'N/A' }}</td>
                    <td>{{ $s->originAgency?->nombre ?? 'N/A' }}</td>
                    <td>
                        <span class="badge bg-{{ $s->status->color ?? 'secondary' }}">{{ $s->status->name ?? 'N/A'
                            }}</span>
                    </td>
                    <td>${{ number_format($s->total_flete, 2) }}</td>
                    <td class="text-end">
                        <div class="btn-group">
                            <a href="{{ route('shipments.show', $s) }}" class="btn btn-sm btn-outline-primary"
                                title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('shipments.print', $s) }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary" title="Imprimir PDF"><i
                                    class="bi bi-printer"></i></a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $shipments->links() }}</div>
@endsection