<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TechnicianController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');

Route::get('/cek', [PublicController::class, 'check'])->name('check');
Route::get('/cek/{nomor}', [PublicController::class, 'check'])->name('check.nomor');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [AuthController::class, 'updatePassword'])->name('password.update');

    Route::middleware('admin')->group(function () {
        Route::get('/servis/baru', [ServiceController::class, 'create'])->name('services.create');
        Route::post('/servis', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/servis/backup', [ServiceController::class, 'exportBackup'])->name('services.backup');
        Route::put('/servis/{nomor}', [ServiceController::class, 'update'])->name('services.update');
        Route::post('/servis/{nomor}/serahkan', [ServiceController::class, 'handOver'])->name('services.handover');

        // Pelanggan
        Route::get('/pelanggan/petunjuk', [CustomerController::class, 'hint'])->name('customers.hint');
        Route::get('/pelanggan', [CustomerController::class, 'index'])->name('customers.index');
        Route::put('/pelanggan/{id}', [CustomerController::class, 'update'])->name('customers.update');

        // Teknisi
        Route::get('/teknisi', [TechnicianController::class, 'index'])->name('technicians.index');
        Route::post('/teknisi', [TechnicianController::class, 'store'])->name('technicians.store');
        Route::put('/teknisi/{id}', [TechnicianController::class, 'update'])->name('technicians.update');
        Route::delete('/teknisi/{id}', [TechnicianController::class, 'destroy'])->name('technicians.destroy');
    });

    Route::get('/servis', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/servis/{nomor}', [ServiceController::class, 'show'])->name('services.show');
    Route::post('/servis/{nomor}/selesai', [ServiceController::class, 'markFinished'])->name('services.finish');
    Route::post('/servis/{nomor}/batal', [ServiceController::class, 'cancel'])->name('services.cancel');

    Route::get('/foto/{path}', [ServiceController::class, 'photo'])->where('path', '.*')->name('services.photo');
});
