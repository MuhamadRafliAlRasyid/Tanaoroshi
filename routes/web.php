<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\PengambilanSparepartController;
use App\Http\Controllers\TanaoroshiPartController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
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

// Sparepart Routes
Route::prefix('spareparts')->name('spareparts.')->middleware('auth')->group(function () {
    Route::get('/', [SparepartController::class, 'index'])->name('index');
    Route::get('/create', [SparepartController::class, 'create'])->name('create');
    Route::post('/', [SparepartController::class, 'store'])->name('store');
    Route::get('/{id}', [SparepartController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [SparepartController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SparepartController::class, 'update'])->name('update');
    Route::delete('/{id}', [SparepartController::class, 'destroy'])->name('destroy');
});

// Pengambilan Sparepart Routes
Route::prefix('pengambilan')->name('pengambilan.')->middleware('auth')->group(function () {
    Route::get('/', [PengambilanSparepartController::class, 'index'])->name('index');
    Route::get('/create', [PengambilanSparepartController::class, 'create'])->name('create');
    Route::post('/', [PengambilanSparepartController::class, 'store'])->name('store');
    Route::get('/{id}', [PengambilanSparepartController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PengambilanSparepartController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PengambilanSparepartController::class, 'update'])->name('update');
    Route::delete('/{id}', [PengambilanSparepartController::class, 'destroy'])->name('destroy');
});

// Tanaoroshi Sparepart Routes
Route::prefix('tanaoroshi')->name('tanaoroshi.')->middleware('auth')->group(function () {
    Route::get('/', [TanaoroshiPartController::class, 'index'])->name('index');
    Route::get('/create', [TanaoroshiPartController::class, 'create'])->name('create');
    Route::post('/', [TanaoroshiPartController::class, 'store'])->name('store');
    Route::get('/{id}', [TanaoroshiPartController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [TanaoroshiPartController::class, 'edit'])->name('edit');
    Route::put('/{id}', [TanaoroshiPartController::class, 'update'])->name('update');
    Route::delete('/{id}', [TanaoroshiPartController::class, 'destroy'])->name('destroy');
});

// Anggota Routes (Admin)
Route::prefix('anggota')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});
