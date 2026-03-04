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
    Route::resource('clientes', ClienteController::class)->except(['show'])
        ->middleware('permission:clientes.index|clientes.create|clientes.edit|clientes.delete');

    // Rubros
    Route::resource('rubros', RubroController::class)->except(['show'])
        ->middleware('permission:rubros.index|rubros.create|rubros.edit|rubros.delete');

    // Proveedores
    Route::resource('proveedores', ProveedorController::class)->except(['show'])
        ->middleware('permission:proveedores.index|proveedores.create|proveedores.edit|proveedores.delete');

    // Artículos
    Route::resource('articulos', ArticuloController::class)->except(['show'])
        ->middleware('permission:articulos.index|articulos.create|articulos.edit|articulos.delete');

    // Agencias
    Route::resource('agencies', AgencyController::class)->except(['show'])
        ->middleware('permission:agencies.index|agencies.create|agencies.edit|agencies.delete');

    // Transportistas
    Route::resource('carriers', CarrierController::class)->except(['show'])
        ->middleware('permission:carriers.index|carriers.create|carriers.edit|carriers.delete');

    // Localidades
    Route::resource('localidades', LocalidadController::class)->except(['show'])
        ->middleware('permission:localidades.index|localidades.create|localidades.edit|localidades.delete');

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