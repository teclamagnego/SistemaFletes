<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\RubroController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocalidadController;
use App\Http\Controllers\FormaPagoController;
use App\Http\Controllers\ClienteReciboController;

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

    // Clientes
    Route::get('clientes/search', [ClienteController::class , 'search'])->name('clientes.search')->middleware('permission:clientes.index');
    Route::post('clientes/quick', [ClienteController::class , 'storeQuick'])->name('clientes.storeQuick')->middleware('permission:clientes.create');
    Route::get('clientes/{cliente}/history', [ClienteController::class , 'history'])->name('clientes.history')->middleware('permission:clientes.index');
    Route::resource('clientes', ClienteController::class)->except(['show'])
        ->middleware('permission:clientes.index|clientes.create|clientes.edit|clientes.delete');

    // Recibos de Clientes
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
    Route::resource('agencies', AgencyController::class)->except(['show'])
        ->middleware('permission:agencies.index|agencies.create|agencies.edit|agencies.delete');

    // Transportistas
    Route::resource('carriers', CarrierController::class)->except(['show'])
        ->middleware('permission:carriers.index|carriers.create|carriers.edit|carriers.delete');

    // Localidades
    Route::resource('localidades', LocalidadController::class)->except(['show'])
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

    Route::resource('shipments', ShipmentController::class)->except(['edit', 'update', 'destroy'])
        ->middleware('permission:shipments.index|shipments.create|shipments.view');
});

require __DIR__ . '/auth.php';