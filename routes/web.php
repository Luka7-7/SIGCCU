<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\Administracion\RevisionCongresoController;

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::get('/profile', fn() => request()->user()->rol === 'docente'
        ? redirect()->route('docente.dashboard')
        : view('user.profile'))->name('profile');

    Route::middleware('docente')->prefix('docente')->name('docente.')->group(function () {
        Route::get('/dashboard', [DocenteController::class, 'index'])->name('dashboard');
        Route::post('/congresos', [DocenteController::class, 'store'])->name('congresos.store');
        Route::patch('/propuestas/{propuesta}/aceptar', [DocenteController::class, 'acceptProposal'])
            ->name('propuestas.accept');
    });

    Route::middleware('administrador')->prefix('administracion')->name('administracion.')->group(function () {
        Route::get('/congresos', [RevisionCongresoController::class, 'index'])->name('congresos.index');
        Route::get('/congresos/{congreso}/solicitud', [RevisionCongresoController::class, 'downloadSolicitud'])
            ->name('congresos.solicitud');
        Route::get('/congresos/propuestas/{propuesta}', [RevisionCongresoController::class, 'downloadPropuesta'])
            ->name('congresos.propuesta');
        Route::patch('/congresos/{congreso}', [RevisionCongresoController::class, 'update'])
            ->name('congresos.update');
        Route::patch('/propuestas/{propuesta}/evaluador', [RevisionCongresoController::class, 'assignEvaluator'])
            ->name('propuestas.assign-evaluator');
    });

    // Rutas de vista
    Route::get('/perfil', fn() => redirect()->route('profile'))->name('usuario.perfil');
    
    Route::get('/profile/settings', [PerfilController::class, 'show'])->name('settings');
    Route::get('/profile/settings/academic', [PerfilController::class, 'showAcademic'])->name('settings.academic');
    Route::get('/profile/settings/general',  [PerfilController::class, 'showGeneral'])->name('settings.general');
    
    // Rutas de Actualización

    Route::put('/profile/settings', [PerfilController::class, 'updatePersonal'])->name('settings.update');
    Route::put('/profile/settings/academic', [PerfilController::class, 'updateAcademic'])->name('settings.update.academic');

});
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => response()
        ->view('auth.login')
        ->header('Cache-Control', 'no-store, private'))
        ->name('login');
    Route::get('/fogot-password', fn() => view('auth.fogot-password'))->name('fogot');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});