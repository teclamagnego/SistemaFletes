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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocalidadController;
use App\Http\Controllers\FormaPagoController;
use App\Http\Controllers\ClienteReciboController;
use App\Http\Controllers\EmpresaController;
use App\Models\ClienteFactura;
use App\Http\Controllers\ClienteFacturaController;
use App\Http\Controllers\SucursalController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard-bootstrap');
})->middleware(['auth', 'verified'])->name('dashboard');

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
        ->middleware('permission:users.index'); // We can use users.index or whatever makes sense for admin
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

    Route::resource('clientes', ClienteController::class)->except(['show'])
        ->middleware('permission:clientes.index|clientes.create|clientes.edit|clientes.delete');

    // Recibos de Clientes
    Route::get('cliente_recibos/{recibo}/print', [ClienteReciboController::class , 'print'])->name('cliente_recibos.print')->middleware('permission:clientes.index');
    Route::resource('cliente_recibos', ClienteReciboController::class)->except(['show'])
        ->middleware('permission:clientes.index');

    // Rubros
    Route::resource('rubros', RubroController::class)->except(['show'])
        ->middleware('permission:rubros.index|rubros.create|rubros.edit|rubros.delete');

    // Proveedores
    Route::resource('proveedores', ProveedorController::class)->except(['show'])
        ->middleware('permission:proveedores.index|proveedores.create|proveedores.edit|proveedores.delete');

    // Artículos
    Route::get('articulos/search', [ArticuloController::class , 'search'])->name('articulos.search')->middleware('permission:articulos.index');
    Route::resource('articulos', ArticuloController::class)->except(['show'])
        ->middleware('permission:articulos.index|articulos.create|articulos.edit|articulos.delete');

    // Agencias
    Route::get('agencies/{agency}/shipments', [AgencyController::class , 'shipments'])->name('agencies.shipments')
        ->middleware('permission:agencies.index');
    Route::get('agencies/{agency}/billing', [AgencyController::class , 'billing'])->name('agencies.billing')->middleware('permission:agencies.index');
    Route::post('agencies/{agency}/generate-invoice', [AgencyController::class , 'generateInvoice'])->name('agencies.generateInvoice')->middleware('permission:agencies.index');
    Route::get('agencies/{agency}/history', [AgencyController::class , 'history'])->name('agencies.history')->middleware('permission:agencies.index');

    Route::resource('agencia_recibos', AgenciaReciboController::class)->except(['show', 'edit', 'update'])
        ->middleware('permission:agencies.index');

    Route::resource('agencies', AgencyController::class)->except(['show'])
        ->middleware('permission:agencies.index|agencies.create|agencies.edit|agencies.delete');

    // Transportistas
    Route::resource('carriers', CarrierController::class)->except(['show'])
        ->middleware('permission:carriers.index|carriers.create|carriers.edit|carriers.delete');

    // Localidades
    Route::resource('localidades', LocalidadController::class)->except(['show'])
        ->parameters(['localidades' => 'localidad'])
        ->middleware('permission:localidades.index|localidades.create|localidades.edit|localidades.delete');

    // Forma de Pago
    Route::resource('formas_pago', FormaPagoController::class)->except(['show'])
        ->middleware('permission:formas_pago.index|formas_pago.create|formas_pago.edit|formas_pago.delete');

    // Envíos (Guías)
    Route::get('shipments/consolidation', [ShipmentController::class , 'consolidation'])->name('shipments.consolidation')
        ->middleware('permission:shipments.index');

    Route::post('shipments/{shipment}/receive', [ShipmentController::class , 'receive'])->name('shipments.receive')
        ->middleware('permission:shipments.create');

    Route::post('shipments/dispatch', [ShipmentController::class , 'dispatch'])->name('shipments.dispatch')
        ->middleware('permission:shipments.create');

    Route::post('shipments/{shipment}/arrive', [ShipmentController::class , 'arrive'])->name('shipments.arrive')
        ->middleware('permission:shipments.create');

    Route::post('shipments/{shipment}/deliver', [ShipmentController::class , 'deliver'])->name('shipments.deliver')
        ->middleware('permission:shipments.create');

    Route::get('shipments/{shipment}/print', [ShipmentController::class , 'print'])->name('shipments.print')
        ->middleware('permission:shipments.index');

    Route::resource('shipments', ShipmentController::class)->except(['destroy'])
        ->middleware('permission:shipments.index|shipments.create|shipments.view|shipments.edit');
});

Route::get('/test-pdf', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test PDF</h1>');
    return $pdf->stream();
});

require __DIR__ . '/auth.php';