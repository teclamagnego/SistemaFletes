@extends('layouts.bootstrap')
@section('content')
<h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Clientes</h5>
                        <h2>{{ \App\Models\Cliente::count() }}</h2>
                    </div>
                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <a href="{{ route('clientes.index') }}" class="text-white"><small>Ver todos →</small></a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Artículos</h5>
                        <h2>{{ \App\Models\Articulo::count() }}</h2>
                    </div>
                    <i class="bi bi-box-seam" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <a href="{{ route('articulos.index') }}" class="text-white"><small>Ver todos →</small></a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Rubros</h5>
                        <h2>{{ \App\Models\Rubro::count() }}</h2>
                    </div>
                    <i class="bi bi-tags" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <a href="{{ route('rubros.index') }}" class="text-white"><small>Ver todos →</small></a>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Proveedores</h5>
                        <h2>{{ \App\Models\Proveedor::count() }}</h2>
                    </div>
                    <i class="bi bi-building" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <a href="{{ route('proveedores.index') }}" class="text-white"><small>Ver todos →</small></a>
            </div>
        </div>
    </div>
</div>
<div class="card mt-3">
    <div class="card-body">
        <h5>Bienvenido, {{ Auth::user()->name }}</h5>
        <p class="text-muted mb-0">Rol: <span class="badge bg-primary">{{ Auth::user()->getRoleNames()->implode(', ')
                }}</span></p>
    </div>
</div>
@endsection