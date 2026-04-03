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
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-truck me-2 text-primary"></i>Nueva Guía de Flete</h5>
                </div>
                <div class="card-body p-4">
                    @include('shipments._form', ['action' => route('shipments.store'), 'method' => 'POST'])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection