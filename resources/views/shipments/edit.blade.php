@extends('layouts.bootstrap')

@push('styles')
<style>
    .list-group.position-absolute { z-index: 2000 !important; }
    .table-responsive { overflow: visible !important; }
    .list-group-item-action:hover, .list-group-item-action.active { background-color: #e9ecef !important; color: inherit !important; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-truck me-2 text-primary"></i>Editar Guía: {{ $shipment->tracking_number }}</h5>
                    <span class="badge bg-{{ $shipment->status->color ?? 'secondary' }}">{{ $shipment->status->name }}</span>
                </div>
                <div class="card-body p-4">
                    @include('shipments._form', ['action' => route('shipments.update', $shipment), 'method' => 'PUT'])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection