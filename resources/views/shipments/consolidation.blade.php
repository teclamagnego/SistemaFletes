@extends('layouts.bootstrap')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-boxes"></i> Consolidación de Carga</h2>
        <span class="badge bg-primary fs-6">Stock en Oficina</span>
    </div>

    @if($groups->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No hay guías pendientes de consolidación en este momento (Estado: In Office).
    </div>
    @else
    <div class="row">
        @foreach($groups as $agencyId => $shipments)
        @php $agency = $shipments->first()->destinationAgency; @endphp
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-geo-alt"></i> Destino: <strong>{{ $agency->name }}</strong></span>
                    <span class="badge bg-light text-dark">{{ $shipments->count() }} guías</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($shipments as $s)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('shipments.show', $s) }}" class="text-decoration-none">
                                    <strong>{{ $s->tracking_number }}</strong>
                                </a>
                                <div class="small text-muted">{{ $s->sender->nombre_fantasia }} -> {{
                                    $s->receiver->nombre_fantasia }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="small d-block text-muted">${{ number_format($s->total_flete, 2) }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer bg-white border-top-0 d-grid">
                    <form action="{{ route('shipments.dispatch') }}" method="POST">
                        @csrf
                        @foreach($shipments as $s)
                        <input type="hidden" name="shipment_ids[]" value="{{ $s->id }}">
                        @endforeach
                        <div class="mb-2">
                            <select name="carrier_id" class="form-select form-select-sm" required>
                                <option value="">Seleccione Transportista...</option>
                                @foreach($carriers as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }} {{ $c->apellido }} ({{ $c->patente }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-truck"></i> Despachar Grupo
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection