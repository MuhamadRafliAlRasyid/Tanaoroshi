<?php

use Livewire\Volt\Volt;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TanaoroshiPartController;
use App\Http\Controllers\PengambilanSparepartController;

Route::get('/', function () {
    return view('welcome');
})->name('home');


// // Sparepart Routes
// Route::get('/spareparts', [SparepartController::class, 'index'])->name('spareparts.index');
// Route::get('/spareparts/create', [SparepartController::class, 'create'])->name('spareparts.create');
// Route::post('/spareparts', [SparepartController::class, 'store'])->name('spareparts.store');
// Route::get('/spareparts/{id}', [SparepartController::class, 'show'])->name('spareparts.show');
// Route::get('/spareparts/{id}/edit', [SparepartController::class, 'edit'])->name('spareparts.edit');
// Route::put('/spareparts/{id}', [SparepartController::class, 'update'])->name('spareparts.update');
// Route::delete('/spareparts/{id}', [SparepartController::class, 'destroy'])->name('spareparts.destroy');
// // Pengambilan Sparepart Routes

// Route::get('/pengambilan', [PengambilanSparepartController::class, 'index'])->name('pengambilan.index');
// Route::get('/pengambilan/create', [PengambilanSparepartController::class, 'create'])->name('pengambilan.create');
// Route::post('/pengambilan', [PengambilanSparepartController::class, 'store'])->name('pengambilan.store');
// Route::get('/pengambilan/{id}', [PengambilanSparepartController::class, 'show'])->name('pengambilan.show');
// Route::get('/pengambilan/{id}/edit', [PengambilanSparepartController::class, 'edit'])->name('pengambilan.edit');
// Route::put('/pengambilan/{id}', [PengambilanSparepartController::class, 'update'])->name('pengambilan.update');
// Route::delete('/pengambilan/{id}', [PengambilanSparepartController::class, 'destroy'])->name('pengambilan.destroy');
// // Tanaoroshi Sparepart Routes

// Route::get('/tanaoroshi', [TanaoroshiPartController::class, 'index'])->name('tanaoroshi.index');
// Route::get('/tanaoroshi/create', [TanaoroshiPartController::class, 'create'])->name('tanaoroshi.create');
// Route::post('/tanaoroshi', [TanaoroshiPartController::class, 'store'])->name('tanaoroshi.store');
// Route::get('/tanaoroshi/{id}', [TanaoroshiPartController::class, 'show'])->name('tanaoroshi.show');
// Route::get('/tanaoroshi/{id}/edit', [TanaoroshiPartController::class, 'edit'])->name('tanaoroshi.edit');
// Route::put('/tanaoroshi/{id}', [TanaoroshiPartController::class, 'update'])->name('tanaoroshi.update');
// Route::delete('/tanaoroshi/{id}', [TanaoroshiPartController::class, 'destroy'])->name('tanaoroshi.destroy');

// Route::middleware(['auth', 'role:admin'])->get('/admin-dashboard', function () {
//     return view('admin.dashboard');
// });


// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/super/dashboard', function () {
        return view('super.kepala');
    });
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get('/karyawan/dashboard', function () {
        return view('karyawan.gudang');
    });
});
