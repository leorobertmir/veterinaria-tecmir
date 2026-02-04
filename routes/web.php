<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use Src\Cliente\Application\Controllers\ClienteWebController;
use Src\Mascota\Application\Controllers\MascotaWebController;
use Src\Cita\Application\Controllers\CitaWebController;
use Src\HistoriaClinica\Application\Controllers\HistoriaClinicaWebController;
use Src\Factura\Application\Controllers\FacturaWebController;
use Src\Producto\Application\Controllers\ProductoWebController;
use Src\Portal\Application\Controllers\PortalWebController;
use Src\Reportes\Application\Controllers\ReporteWebController;
use Inertia\Inertia;

// Rutas protegidas (autenticadas)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clientes
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', [ClienteWebController::class, 'index'])->name('index');
        Route::get('/create', [ClienteWebController::class, 'create'])->name('create');
        Route::post('/', [ClienteWebController::class, 'store'])->name('store');
        Route::get('/{cliente}/edit', [ClienteWebController::class, 'edit'])->name('edit');
        Route::put('/{cliente}', [ClienteWebController::class, 'update'])->name('update');
        Route::delete('/{cliente}', [ClienteWebController::class, 'destroy'])->name('destroy');
    });

    // Mascotas
    Route::prefix('mascotas')->name('mascotas.')->group(function () {
        Route::get('/', [MascotaWebController::class, 'index'])->name('index');
        Route::get('/create', [MascotaWebController::class, 'create'])->name('create');
        Route::post('/', [MascotaWebController::class, 'store'])->name('store');
        Route::get('/{mascota}', [MascotaWebController::class, 'show'])->name('show');
        Route::get('/{mascota}/edit', [MascotaWebController::class, 'edit'])->name('edit');
        Route::put('/{mascota}', [MascotaWebController::class, 'update'])->name('update');
        Route::delete('/{mascota}', [MascotaWebController::class, 'destroy'])->name('destroy');
    });

    // Citas
    Route::prefix('citas')->name('citas.')->group(function () {
        Route::get('/', [CitaWebController::class, 'index'])->name('index');
        Route::get('/calendar', [CitaWebController::class, 'calendar'])->name('calendar');
        Route::get('/create', [CitaWebController::class, 'create'])->name('create');
        Route::post('/', [CitaWebController::class, 'store'])->name('store');
        Route::get('/{cita}', [CitaWebController::class, 'show'])->name('show');
        Route::get('/{cita}/edit', [CitaWebController::class, 'edit'])->name('edit');
        Route::put('/{cita}', [CitaWebController::class, 'update'])->name('update');
        Route::patch('/{cita}/estado', [CitaWebController::class, 'updateEstado'])->name('update-estado');
        Route::delete('/{cita}', [CitaWebController::class, 'destroy'])->name('destroy');
    });

    // Historias Clinicas
    Route::prefix('historias-clinicas')->name('historias-clinicas.')->group(function () {
        Route::get('/', [HistoriaClinicaWebController::class, 'index'])->name('index');
        Route::get('/create', [HistoriaClinicaWebController::class, 'create'])->name('create');
        Route::post('/', [HistoriaClinicaWebController::class, 'store'])->name('store');
        Route::get('/mascota/{mascota}', [HistoriaClinicaWebController::class, 'porMascota'])->name('por-mascota');
        Route::get('/{historia}', [HistoriaClinicaWebController::class, 'show'])->name('show');
        Route::get('/{historia}/edit', [HistoriaClinicaWebController::class, 'edit'])->name('edit');
        Route::put('/{historia}', [HistoriaClinicaWebController::class, 'update'])->name('update');
        Route::delete('/{historia}', [HistoriaClinicaWebController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('facturas')->name('facturas.')->group(function () {
        Route::get('/', [FacturaWebController::class, 'index'])->name('index');
        Route::get('/create', [FacturaWebController::class, 'create'])->name('create');
        Route::post('/', [FacturaWebController::class, 'store'])->name('store');
        Route::get('/{factura}/edit', [FacturaWebController::class, 'edit'])->name('edit');
        Route::put('/{factura}', [FacturaWebController::class, 'update'])->name('update');
        Route::delete('/{factura}', [FacturaWebController::class, 'destroy'])->name('destroy');
    });

    // Productos
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', [ProductoWebController::class, 'index'])->name('index');
        Route::get('/create', [ProductoWebController::class, 'create'])->name('create');
        Route::post('/', [ProductoWebController::class, 'store'])->name('store');
        Route::get('/{producto}/edit', [ProductoWebController::class, 'edit'])->name('edit');
        Route::put('/{producto}', [ProductoWebController::class, 'update'])->name('update');
        Route::delete('/{producto}', [ProductoWebController::class, 'destroy'])->name('destroy');
    });

    // Portal Cliente
    Route::get('/portal', [PortalWebController::class, 'index'])->name('portal.index');

    // Reportes
    Route::get('/reportes', fn() => Inertia::render('Reportes/index'))->name('reportes.index');
    Route::get('/reportes/data', [ReporteWebController::class, 'index'])->name('reportes.data');
    
});
