@extends('layouts.bootstrap')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-pencil-square"></i> Editar Empresa</h2>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('empresas.update', $empresa) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre Fantasía <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_fantasia" class="form-control"
                        value="{{ old('nombre_fantasia', $empresa->nombre_fantasia) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Razón Social <span class="text-danger">*</span></label>
                    <input type="text" name="razon_social" class="form-control"
                        value="{{ old('razon_social', $empresa->razon_social) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">CUIT <span class="text-danger">*</span></label>
                    <input type="text" name="cuit" class="form-control" value="{{ old('cuit', $empresa->cuit) }}"
                        required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipo de IVA <span class="text-danger">*</span></label>
                    <select name="tipoiva_id" class="form-select" required>
                        <option value="">Seleccione...</option>
                        @foreach($tiposIva as $iva)
                        <option value="{{ $iva->id }}" {{ old('tipoiva_id', $empresa->tipoiva_id) == $iva->id ?
                            'selected' : '' }}>
                            {{ $iva->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Logo de la Empresa</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <div class="form-text">Dejar en blanco para conservar el actual. Máx 2MB.</div>
                </div>
                @if($empresa->logo)
                <div class="col-md-6">
                    <label class="form-label d-block">Logo Actual</label>
                    <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo" class="img-thumbnail"
                        style="max-height: 100px;">
                </div>
                @endif
            </div>

            <hr class="my-4">
            <h4 class="mb-3 text-primary"><i class="bi bi-printer"></i> Configuración QZ-Tray para Guías</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Certificado Digital (digital-certificate.txt)</label>
                    <input type="file" name="qz_certificate_file" class="form-control" accept=".txt,.crt">
                    @if($empresa->qz_certificate)
                    <div class="form-text text-success"><i class="bi bi-check-circle"></i> Certificado cargado</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Llave Privada (private-key.pem)</label>
                    <input type="file" name="qz_private_key_file" class="form-control" accept=".pem,.key">
                    @if($empresa->qz_private_key)
                    <div class="form-text text-success"><i class="bi bi-check-circle"></i> Llave privada cargada</div>
                    @endif
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <label class="form-label">Impresora para Guías</label>
                    <div class="input-group">
                        <select name="qz_printer" id="qz_printer" class="form-select">
                            <option value="">Seleccione impresora...</option>
                            @if($empresa->qz_printer)
                            <option value="{{ $empresa->qz_printer }}" selected>{{ $empresa->qz_printer }}</option>
                            @endif
                        </select>
                        <button type="button" class="btn btn-outline-primary" id="btn_refresh_printers">
                            <i class="bi bi-arrow-clockwise"></i> Escanear Impresoras
                        </button>
                    </div>
                    <div class="form-text">Asegúrese de que QZ-Tray esté ejecutándose para escanear.</div>
                </div>
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="esagenteretencioniva" id="esagenteretencioniva"
                    value="1" {{ old('esagenteretencioniva', $empresa->esagenteretencioniva) ? 'checked' : '' }}>
                <label class="form-check-label" for="esagenteretencioniva">
                    Es Agente Retención IVA
                </label>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1" {{ old('activo',
                    $empresa->activo) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">
                    Empresa Activa
                </label>
            </div>

            <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Actualizar Empresa</button>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.js"></script>
<script>
    // Configurar firma para que QZ-Tray no pida permisos manualmente
    qz.security.setCertificatePromise(function(resolve, reject) {
        fetch('{{ route("qz.certificate") }}')
            .then(r => r.ok ? r.text() : reject('No hay certificado guardado todavía'))
            .then(resolve)
            .catch(reject);
    });

    qz.security.setSignatureAlgorithm('SHA512');
    qz.security.setSignaturePromise(function(toSign) {
        return function(resolve, reject) {
            fetch('{{ route("qz.sign") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ toSign: toSign })
            })
            .then(r => r.ok ? r.text() : reject('Error firmando'))
            .then(resolve)
            .catch(reject);
        };
    });

    document.getElementById('btn_refresh_printers').addEventListener('click', async function() {
        const btn = this;
        const select = document.getElementById('qz_printer');
        const originalText = btn.innerHTML;
        const currentPrinter = select.value;
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Conectando...';

        try {
            if (!qz.websocket.isActive()) {
                await qz.websocket.connect();
            }

            const printers = await qz.printers.find();
            
            // Limpiar y cargar
            select.innerHTML = '<option value="">Seleccione impresora...</option>';
            printers.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p;
                opt.textContent = p;
                if (p === currentPrinter) opt.selected = true;
                select.appendChild(opt);
            });

            if (printers.length === 0) {
                alert('No se encontraron impresoras instaladas en el sistema.');
            }
        } catch (err) {
            console.error(err);
            alert('Error al conectar con QZ-Tray: ' + err.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>
@endpush
@endsection