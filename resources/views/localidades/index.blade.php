@extends('layouts.bootstrap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-geo-alt"></i> Localidades</h2>
    <a href="{{ route('localidades.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Nueva
        Localidad</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 100px;">ID</th>
                    <th>Nombre</th>
                    <th style="width: 150px;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($localidades as $l)
                <tr>
                    <td>{{ $l->id }}</td>
                    <td>{{ $l->nombre }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('localidades.edit', $l) }}" class="btn btn-sm btn-outline-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('localidades.destroy', $l) }}" method="POST" class="d-inline ms-1"
                                onsubmit="return confirm('¿Está seguro de eliminar esta localidad?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $localidades->links() }}
</div>
@endsection