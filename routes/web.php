<?php

use Livewire\Volt\Volt;
use App\Exports\PengambilanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SuperDashboardController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\PengambilanSparepartController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\KaryawanDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group that
| contains the "web" middleware group by default.
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');


// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/spareparts/sync-from-sheets', [SparepartController::class, 'syncFromSheets'])->name('sync-from-sheets');
Route::get('/spareparts/sync-to-sheets', [SparepartController::class, 'syncAllToSheets'])->name('sync-to-sheets');
Route::get('/google/callback', [SparepartController::class, 'handleGoogleCallback'])->name('handleGoogleCallback');
Route::get('/qr', [SparepartController::class, 'generateQrCode'])->name('generateQrCode');
Route::get('/unduh', [SparepartController::class, 'unduh'])->name('unduh');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/pengambilan/export/{id?}', function ($id = null) {
    return Excel::download(new PengambilanExport($id), 'pengambilan_' . ($id ? 'id_' . $id : 'all') . '.xlsx');
})->name('pengambilan.export');


// Protected Routes with Auth Middleware
Route::middleware(['auth'])->group(function () {
    // Dashboard Routes
    Route::get('/super/dashboard', [SuperDashboardController::class, 'index'])->name('super.dashboard');
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/karyawan/dashboard', [KaryawanDashboardController::class, 'index'])->name('karyawan.dashboard');


    // Sparepart Routes
    Route::prefix('spareparts')->name('spareparts.')->group(function () {
        Route::get('/', [SparepartController::class, 'index'])->name('index');
        Route::get('/create', [SparepartController::class, 'create'])->name('create');
        Route::post('/', [SparepartController::class, 'store'])->name('store');
        Route::get('/unduh', [SparepartController::class, 'unduh'])->name('unduh');

        // detail harus diletakkan SETELAH route spesifik lain supaya tidak menelan path lain
        Route::get('/{id}', [SparepartController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SparepartController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SparepartController::class, 'update'])->name('update');

        // soft delete ops
        Route::delete('/{id}', [SparepartController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [SparepartController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [SparepartController::class, 'forceDelete'])->name('forceDelete');

        // sinkronisasi manual
        Route::get('/sync-from-sheets', [SparepartController::class, 'syncFromSheets'])->name('sync-from-sheets');
        Route::get('/sync-to-sheets', [SparepartController::class, 'syncAllToSheets'])->name('sync-to-sheets');
    });

    // Pengambilan Sparepart Routes
    Route::prefix('pengambilan')->name('pengambilan.')->group(function () {
        Route::get('/', [PengambilanSparepartController::class, 'index'])->name('index');
        Route::get('/create', [PengambilanSparepartController::class, 'create'])->name('create');
        Route::post('/', [PengambilanSparepartController::class, 'store'])->name('store');
        Route::get('/{id}', [PengambilanSparepartController::class, 'show'])->name('show');
        Route::get('/pengambilan/export/{id?}', function ($id = null) {
            return Excel::download(new PengambilanExport($id), 'pengambilan_' . ($id ? 'id_' . $id : 'all') . '.xlsx');
        })->name('pengambilan.export');
        Route::get('/{pengambilanSparepart}/edit', [PengambilanSparepartController::class, 'edit'])->name('edit');
        Route::put('/{pengambilanSparepart}', [PengambilanSparepartController::class, 'update'])->name('update');
        Route::delete('/{pengambilanSparepart}', [PengambilanSparepartController::class, 'destroy'])->name('destroy');
    });

    // Purchase Request Routes
    Route::prefix('purchase-requests')->name('purchase_requests.')->group(function () {
        Route::get('/', [PurchaseRequestController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseRequestController::class, 'create'])->name('create');
        Route::post('/', [PurchaseRequestController::class, 'store'])->name('store');
        Route::get('/{purchaseRequest}', [PurchaseRequestController::class, 'show'])->name('show');
        Route::get('/{purchaseRequest}/edit', [PurchaseRequestController::class, 'edit'])->name('edit');
        Route::put('/{purchaseRequest}', [PurchaseRequestController::class, 'update'])->name('update');
        Route::delete('/{purchaseRequest}', [PurchaseRequestController::class, 'destroy'])->name('destroy');
        Route::post('/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])->name('approve');
        Route::post('/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])->name('reject');
    });

    // User (Admin) Routes
    Route::prefix('anggota')->name('admin.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Bagian Routes
    Route::prefix('bagian')->name('bagian.')->group(function () {
        Route::get('/', [BagianController::class, 'index'])->name('index');
        Route::get('/create', [BagianController::class, 'create'])->name('create');
        Route::post('/', [BagianController::class, 'store'])->name('store');
        // Route::get('/{id}', [BagianController::class, 'show'])->name('show');
        Route::get('/{bagian}/edit', [BagianController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BagianController::class, 'update'])->name('update');
        Route::delete('/{bagian}', [BagianController::class, 'destroy'])->name('destroy');
    });
});
