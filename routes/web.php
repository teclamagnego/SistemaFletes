<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\RubroController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AgenciaReciboController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocalidadController;
use App\Http\Controllers\FormaPagoController;
use App\Http\Controllers\ClienteReciboController;
use App\Http\Controllers\EmpresaController;
use App\Models\ClienteFactura;
use App\Http\Controllers\ClienteFacturaController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\InformeController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');

    // Users
    Route::resource('users', UserController::class)->except(['show'])
        ->middleware('permission:users.index|users.create|users.edit|users.delete');

    // Roles
    Route::resource('roles', RoleController::class)->except(['show'])
        ->middleware('permission:roles.index|roles.create|roles.edit|roles.delete');

    // Empresas y Sucursales
    Route::resource('empresas', EmpresaController::class)->except(['show'])
        ->middleware('permission:users.index');
    Route::resource('sucursales', SucursalController::class)->except(['show'])
        ->middleware('permission:users.index');

    // Clientes
    Route::get('clientes/search', [ClienteController::class , 'search'])->name('clientes.search')->middleware('permission:clientes.index');
    Route::post('clientes/quick', [ClienteController::class , 'storeQuick'])->name('clientes.storeQuick')->middleware('permission:clientes.create');
    Route::get('clientes/{cliente}/history', [ClienteController::class , 'history'])->name('clientes.history')->middleware('permission:clientes.index');
    Route::get('clientes/{cliente}/history/print', [ClienteController::class , 'printHistory'])->name('clientes.history.print')->middleware('permission:clientes.index');
    Route::get('clientes/{cliente}/billing', [ClienteController::class , 'billing'])->name('clientes.billing')->middleware('permission:clientes.index');
    Route::post('clientes/{cliente}/generate-invoice', [ClienteController::class , 'generateInvoice'])->name('clientes.generateInvoice')->middleware('permission:clientes.index');

    Route::get('facturas/{factura}/print', [ClienteFacturaController::class , 'print'])->name('facturas.print')->middleware('permission:clientes.index');
    Route::resource('facturas', ClienteFacturaController::class)->only(['index', 'show', 'destroy'])->middleware('permission:clientes.index');
    Route::delete('clientes/{cliente}/ajax', [ClienteController::class, 'destroyAjax'])->name('clientes.destroyAjax')->middleware('permission:clientes.delete');
    Route::resource('clientes', ClienteController::class)->middleware('permission:clientes.index|clientes.create|clientes.edit|clientes.delete');

    // Rubros y Proveedores
    Route::resource('rubros', RubroController::class)->except(['show'])->middleware('permission:configuracion.index');
    Route::resource('proveedores', ProveedorController::class)->except(['show'])->middleware('permission:configuracion.index');

    // Artículos
    Route::get('articulos/search', [ArticuloController::class, 'search'])->name('articulos.search')->middleware('permission:articulos.index');
    Route::resource('articulos', ArticuloController::class)->except(['show'])->middleware('permission:articulos.index');

    // Agencias
    Route::get('agencies/{agency}/shipments', [AgencyController::class, 'shipments'])->name('agencies.shipments')->middleware('permission:agencies.index');
    Route::get('agencies/{agency}/billing', [AgencyController::class , 'billing'])->name('agencies.billing')->middleware('permission:agencies.index');
    Route::post('agencies/{agency}/generate-invoice', [AgencyController::class , 'generateInvoice'])->name('agencies.generateInvoice')->middleware('permission:agencies.index');
    Route::get('agencies/facturas/{factura}/imprimir-detalle', [AgencyController::class, 'printDetail'])->name('agencies.facturas.printDetail')->middleware('permission:agencies.index');
    Route::get('agencies/{agency}/history', [AgencyController::class , 'history'])->name('agencies.history')->middleware('permission:agencies.index');
    Route::resource('agencies', AgencyController::class)->middleware('permission:agencies.index');

    Route::resource('agencia_recibos', AgenciaReciboController::class)->except(['show', 'edit', 'update'])->middleware('permission:agencies.index');

    // Guías (Shipments)
    Route::get('shipments/consolidation', [ShipmentController::class, 'consolidation'])->name('shipments.consolidation')->middleware('permission:shipments.index');
    Route::post('shipments/consolidation', [ShipmentController::class, 'doConsolidation'])->name('shipments.doConsolidation')->middleware('permission:shipments.index');
    Route::get('shipments/{shipment}/print', [ShipmentController::class, 'print'])->name('shipments.print')->middleware('permission:shipments.index');
    Route::get('shipments/{shipment}/print-base64', [ShipmentController::class, 'printBase64'])->name('shipments.printBase64')->middleware('permission:shipments.index');
    Route::match(['post', 'patch'], 'shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.updateStatus')->middleware('permission:shipments.edit');
    Route::post('shipments/{shipment}/arrive', [ShipmentController::class, 'arrive'])->name('shipments.arrive')->middleware('permission:shipments.edit');
    Route::post('shipments/{shipment}/deliver', [ShipmentController::class, 'deliver'])->name('shipments.deliver')->middleware('permission:shipments.edit');
    Route::post('shipments/{shipment}/receive', [ShipmentController::class, 'receive'])->name('shipments.receive')->middleware('permission:shipments.edit');
    Route::post('shipments/dispatch', [ShipmentController::class, 'dispatch'])->name('shipments.dispatch')->middleware('permission:shipments.edit');
    Route::post('qz/sign', [ShipmentController::class, 'signRequest'])->name('qz.sign');
    Route::get('shipments/print-filtered', [ShipmentController::class, 'printFiltered'])->name('shipments.print_filtered')->middleware('permission:shipments.index');
    Route::resource('shipments', ShipmentController::class)->middleware('permission:shipments.index');


    // Localidades y Transportistas
    Route::get('localidades/search', [LocalidadController::class, 'search'])->name('localidades.search')->middleware('permission:localidades.index');
    Route::delete('localidades/{localidad}/ajax', [LocalidadController::class, 'destroyAjax'])->name('localidades.destroyAjax')->middleware('permission:localidades.index');
    Route::resource('localidades', LocalidadController::class)->except(['show'])->middleware('permission:localidades.index');
    Route::resource('carriers', CarrierController::class)->except(['show'])->middleware('permission:carriers.index');

    // Formas de Pago
    Route::resource('formas_pago', FormaPagoController::class)->except(['show'])->middleware('permission:formas_pago.index');

    // Recibos de Clientes
    Route::get('cliente_recibos/{recibo}/print', [ClienteReciboController::class, 'print'])->name('cliente_recibos.print')->middleware('permission:clientes.index');
    Route::resource('cliente_recibos', ClienteReciboController::class)->except(['show', 'edit', 'update'])
        ->middleware('permission:clientes.index');

    // Informes
    Route::get('informes/saldos_clientes', [InformeController::class, 'saldosClientes'])->name('informes.saldos_clientes');
    Route::get('informes/saldos_agencias', [InformeController::class, 'saldosAgencias'])->name('informes.saldos_agencias');

    Route::get('/test-pdf', function () {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test PDF</h1>');
        return $pdf->stream();
    });
});

require __DIR__ . '/auth.php';