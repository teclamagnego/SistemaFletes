@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-person-plus"></i> Nuevo Cliente</h2>
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i>
                Volver</a>
</div>

<div class="card shadow-sm">
        <div class="card-body">
                <form action="{{ route('clientes.store') }}" method="POST">
                        @csrf

                        <h5 class="card-title mb-4 border-bottom pb-2 text-primary">Datos Principales</h5>
                        <div class="row">
                                <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre Fantasía *</label>
                                        <input type="text" name="nombre_fantasia"
                                                class="form-control @error('nombre_fantasia') is-invalid @enderror"
                                                value="{{ old('nombre_fantasia') }}" required autofocus>
                                        @error('nombre_fantasia')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                        <label class="form-label">Razón Social</label>
                                        <input type="text" name="razon_social"
                                                class="form-control @error('razon_social') is-invalid @enderror"
                                                value="{{ old('razon_social') }}">
                                        @error('razon_social')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                        </div>

                        <div class="row">
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Tipo Documento *</label>
                                        <select name="tipodoc_id"
                                                class="form-select @error('tipodoc_id') is-invalid @enderror" required>
                                                <option value="">Seleccionar</option>
                                                @foreach($tiposDoc as $td)
                                                <option value="{{ $td->id }}" {{ old('tipodoc_id', (str_contains($td->nombre, 'CUIT') ? $td->id : null))==$td->id ? 'selected'
                                                        : '' }}>{{ $td->nombre }} ({{ $td->codigo }})</option>
                                                @endforeach
                                        </select>
                                        @error('tipodoc_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Documento Nro *</label>
                                        <input type="text" name="documento_nro"
                                                class="form-control @error('documento_nro') is-invalid @enderror"
                                                value="{{ old('documento_nro', '0') }}" required>
                                        @error('documento_nro')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Tipo IVA *</label>
                                        <select name="tipoiva_id"
                                                class="form-select @error('tipoiva_id') is-invalid @enderror" required>
                                                <option value="">Seleccionar</option>
                                                @foreach($tiposIva as $iva)
                                                <option value="{{ $iva->id }}" {{ old('tipoiva_id', ($iva->nombre == 'Consumidor Final' ? $iva->id : null))==$iva->id ?
                                                        'selected' : '' }}>{{ $iva->nombre }}</option>
                                                @endforeach
                                        </select>
                                        @error('tipoiva_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                        </div>

                        <h5 class="card-title mt-4 mb-4 border-bottom pb-2 text-primary">Contacto y Ubicación</h5>
                        <div class="row">
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}">
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono"
                                                class="form-control @error('telefono') is-invalid @enderror"
                                                value="{{ old('telefono') }}">
                                        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Localidad *</label>
                                        <div class="position-relative">
                                                <input type="text" id="localidad_search" class="form-control @error('localidad_id') is-invalid @enderror"
                                                        placeholder="Buscar localidad..." autocomplete="off" value="{{ old('localidad_id') ? \App\Models\Localidad::find(old('localidad_id'))->nombre ?? '' : '' }}">
                                                <input type="hidden" name="localidad_id" id="localidad_id" value="{{ old('localidad_id') }}">
                                                <div id="localidad_results" class="list-group position-absolute w-100 shadow-sm"
                                                        style="z-index: 1000; display: none;"></div>
                                        </div>
                                        @error('localidad_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                        </div>
                        <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion"
                                        class="form-control @error('direccion') is-invalid @enderror"
                                        value="{{ old('direccion') }}">
                                @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <h5 class="card-title mt-4 mb-4 border-bottom pb-2 text-primary">Configuración de Cuenta</h5>
                        <div class="row">
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Tipo de Cuenta *</label>
                                        <select name="tipocuenta_id"
                                                class="form-select @error('tipocuenta_id') is-invalid @enderror"
                                                required>
                                                <option value="">Seleccionar</option>
                                                @foreach($tiposCuenta as $tc)
                                                <option value="{{ $tc->id }}" {{ old('tipocuenta_id', ($tc->nombre == 'Cuenta Corriente' ? $tc->id : null))==$tc->id ?
                                                        'selected' : '' }}>{{ $tc->nombre }}</option>
                                                @endforeach
                                        </select>
                                        @error('tipocuenta_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Agencia Origen Habitual *</label>
                                        <select name="agenciaorigen_id"
                                                class="form-select @error('agenciaorigen_id') is-invalid @enderror"
                                                required>
                                                <option value="">Seleccionar</option>
                                                @foreach($agencies as $ag)
                                                <option value="{{ $ag->id }}" {{ old('agenciaorigen_id')==$ag->id ?
                                                        'selected' : '' }}>{{ $ag->nombre }}</option>
                                                @endforeach
                                        </select>
                                        @error('agenciaorigen_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label class="form-label">Agencia Destino Habitual *</label>
                                        <select name="agenciadestino_id"
                                                class="form-select @error('agenciadestino_id') is-invalid @enderror"
                                                required>
                                                <option value="">Seleccionar</option>
                                                @foreach($agencies as $ag)
                                                <option value="{{ $ag->id }}" {{ old('agenciadestino_id')==$ag->id ?
                                                        'selected' : '' }}>{{ $ag->nombre }}</option>
                                                @endforeach
                                        </select>
                                        @error('agenciadestino_id')<div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                        </div>

                        <div class="mb-4">
                                <label class="form-label">Observaciones</label>
                                <textarea name="observacion"
                                        class="form-control @error('observacion') is-invalid @enderror"
                                        rows="3">{{ old('observacion') }}</textarea>
                                @error('observacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar
                                        Cliente</button>
                        </div>
                </form>
        </div>
</div>

@push('styles')
<style>
    .list-group.position-absolute { z-index: 2000 !important; max-height: 250px; overflow-y: auto; }
    .list-group-item-action { cursor: pointer; }
    .list-group-item-action:hover, .list-group-item-action.active { background-color: #e9ecef !important; color: inherit !important; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('localidad_search');
    const hidden = document.getElementById('localidad_id');
    const results = document.getElementById('localidad_results');
    let timeout = null;
    let currentFocus = -1;

    function addActive(x) {
        if (!x) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        x[currentFocus].classList.add("active");
    }

    function removeActive(x) {
        for (let i = 0; i < x.length; i++) {
            x[i].classList.remove("active");
        }
    }

    input.addEventListener('keydown', function (e) {
        let x = results.getElementsByClassName("list-group-item-action");
        if (e.key === "ArrowDown") {
            currentFocus++;
            addActive(x);
        } else if (e.key === "ArrowUp") {
            currentFocus--;
            addActive(x);
        } else if (e.key === "Enter") {
            if (currentFocus > -1) {
                if (x[currentFocus]) x[currentFocus].click();
                e.preventDefault();
            }
        }
    });

    input.addEventListener('input', function () {
        clearTimeout(timeout);
        const q = this.value.trim();
        currentFocus = -1;
        if (q.length < 2) {
            results.style.display = 'none';
            return;
        }

        timeout = setTimeout(() => {
            const url = `{{ route('localidades.search') }}?q=${encodeURIComponent(q)}`;
            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    results.innerHTML = '';
                    if (data.length === 0) {
                        results.innerHTML = `<div class="list-group-item text-muted">No se encontraron localidades</div>`;
                        results.style.display = 'block';
                        return;
                    }
                    data.forEach((loc, index) => {
                        const a = document.createElement('a');
                        a.href = '#'; 
                        a.className = 'list-group-item list-group-item-action py-2 d-flex justify-content-between align-items-center client-search-item';
                        a.innerHTML = `
                            <div class="flex-grow-1">
                                <strong>${loc.nombre}</strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger border-0 delete-localidad-ajax" data-id="${loc.id}" title="Eliminar localidad">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        `;
                        
                        a.onclick = function (e) {
                            if (e.target.closest('.delete-localidad-ajax')) return;
                            e.preventDefault();
                            input.value = loc.nombre;
                            hidden.value = loc.id;
                            results.style.display = 'none';
                            // remove invalid class if set
                            input.classList.remove('is-invalid');
                        };

                        const btnDel = a.querySelector('.delete-localidad-ajax');
                        btnDel.onclick = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            function performDelete() {
                                const originalContent = btnDel.innerHTML;
                                btnDel.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                                btnDel.disabled = true;

                                let delUrl = `/localidades/${loc.id}/ajax`;

                                fetch(delUrl, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(resData => {
                                    if (resData.success) {
                                        a.remove();
                                        if (results.querySelectorAll('.client-search-item').length === 0) {
                                            results.style.display = 'none';
                                        }
                                    } else {
                                        alert(resData.message || 'No se puede eliminar la localidad.');
                                        btnDel.innerHTML = originalContent;
                                        btnDel.disabled = false;
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    alert('Error al intentar eliminar la localidad.');
                                    btnDel.innerHTML = originalContent;
                                    btnDel.disabled = false;
                                });
                            }

                            if (confirm('¿Está seguro de eliminar esta localidad?')) {
                                performDelete();
                            }
                        };

                        results.appendChild(a);
                    });
                    results.style.display = 'block';
                });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (e.target !== input && e.target !== results && !results.contains(e.target)) {
            results.style.display = 'none';
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        if (!hidden.value) {
            e.preventDefault();
            input.classList.add('is-invalid');
            alert('Por favor, busque y seleccione una Localidad de la lista.');
        }
    });
});
</script>
@endpush
@endsection