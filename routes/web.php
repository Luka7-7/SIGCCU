<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PerfilController;

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::get('/profile', fn() => view('user.profile'))->name('profile');

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
    Route::get('/', fn() => view('auth.login'))->name('login');
    Route::get('/fogot-password', fn() => view('auth.fogot-password'))->name('fogot');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});