<?php

use Livewire\Volt\Volt;
use App\Exports\PengambilanExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SuperDashboardController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\KaryawanDashboardController;
use App\Http\Controllers\PengambilanSparepartController;

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
Route::get('/login/{spareparts_hashid}', [AuthController::class, 'showLoginWithSparepart'])
    ->name('login.with-sparepart');
Route::post('/login/{spareparts_hashid}', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public Utility Routes
Route::get('/spareparts/sync-from-sheets', [SparepartController::class, 'syncFromSheets'])->name('sync-from-sheets');
Route::get('/spareparts/sync-to-sheets', [SparepartController::class, 'syncAllToSheets'])->name('sync-to-sheets');
Route::get('/google/callback', [SparepartController::class, 'handleGoogleCallback'])->name('handleGoogleCallback');
Route::get('/generate-all-qr', [SparepartController::class, 'generateAllQrCodes'])
    ->name('spareparts.generateAllQrCodes');
Route::get('/v2qr', [SparepartController::class, 'generateQrCode'])->name('spareparts.generateQrCode');
Route::get('/pengambilan/export/{id?}', function ($id = null) {
    return Excel::download(new PengambilanExport($id), 'pengambilan_' . ($id ? 'id_' . $id : 'all') . '.xlsx');
})->name('pengambilan.export');
Route::post('/purchase-requests/{hashid}/complete', [PurchaseRequestController::class, 'complete'])
    ->name('purchase_requests.complete')
    ->middleware('auth');
// Encrypted Redirect Routes
Route::prefix('redirect')->name('redirect.')->group(function () {
    Route::get('/sparepart/{encryptedId}', [SparepartController::class, 'redirectDecrypt'])->name('sparepart');
    Route::get('/generate/{id}', [SparepartController::class, 'generateEncryptedLink'])->name('generate');
});

// Protected Routes with Auth Middleware
Route::middleware(['auth'])->group(function () {
    // Notification Routes
    Route::post('/notifications/mark-all-read', function() {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    })->name('notifications.markAllRead');

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
        Route::get('/pdf/{hashid}', [SparepartController::class, 'downloadPdf'])->name('pdf');
        Route::get('/check-stock', [SparepartController::class, 'checkStock'])->name('checkstock');
        Route::get('/qr', [SparepartController::class, 'regenerateAllQrCodes'])->name('regenerateAllQrCodes');
        Route::get('/trashed', [SparepartController::class, 'trashed'])->name('trashed');

        // Hashid based routes
        Route::get('/{hashid}', [SparepartController::class, 'show'])->name('show');
        Route::get('/{hashid}/edit', [SparepartController::class, 'edit'])->name('edit');
        Route::put('/{hashid}', [SparepartController::class, 'update'])->name('update');
        Route::get('/{hashid}/regenerate-qr', [SparepartController::class, 'regenerateQrCode'])->name('regenerateQrCode');
        Route::delete('/{hashid}', [SparepartController::class, 'destroy'])->name('destroy');
        Route::post('/{hashid}/restore', [SparepartController::class, 'restore'])->name('restore');
        Route::delete('/{hashid}/force-delete', [SparepartController::class, 'forceDelete'])->name('forceDelete');
    });

    // Pengambilan Sparepart Routes
    Route::prefix('pengambilan')->name('pengambilan.')->group(function () {
        Route::get('/', [PengambilanSparepartController::class, 'index'])->name('index');
        Route::get('/create', [PengambilanSparepartController::class, 'create'])->name('create');
        Route::get('/create/{spareparts_id}', [PengambilanSparepartController::class, 'create'])->name('create.with-sparepart');
        Route::post('/', [PengambilanSparepartController::class, 'store'])->name('store');
        Route::get('/export-pdf/{id?}', [PengambilanSparepartController::class, 'exportPdf'])->name('exportpdf');

        // ID based routes
        Route::get('/{id}', [PengambilanSparepartController::class, 'show'])->name('show');
        Route::get('/{pengambilanSparepart}/edit', [PengambilanSparepartController::class, 'edit'])->name('edit');
        Route::put('/{pengambilanSparepart}', [PengambilanSparepartController::class, 'update'])->name('update');
        Route::delete('/{pengambilanSparepart}', [PengambilanSparepartController::class, 'destroy'])->name('destroy');
    });

    // Purchase Request Routes
    Route::prefix('purchase-requests')->name('purchase_requests.')->group(function () {
        Route::get('/', [PurchaseRequestController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseRequestController::class, 'create'])->name('create');
        Route::post('/', [PurchaseRequestController::class, 'store'])->name('store');
        Route::get('/unduh', [PurchaseRequestController::class, 'unduh'])->name('unduh');

        // Hashid based routes
        Route::get('/{hashid}', [PurchaseRequestController::class, 'show'])->name('show');
        Route::get('/{hashid}/edit', [PurchaseRequestController::class, 'edit'])->name('edit');
        Route::put('/{hashid}', [PurchaseRequestController::class, 'update'])->name('update');
        Route::delete('/{hashid}', [PurchaseRequestController::class, 'destroy'])->name('destroy');
        Route::post('/{hashid}/approve', [PurchaseRequestController::class, 'approve'])->name('approve');
        Route::post('/{hashid}/reject', [PurchaseRequestController::class, 'reject'])->name('reject');
    });

    // User Management Routes
    Route::prefix('anggota')->name('admin.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');

        // User based routes
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

        // Bagian based routes
        Route::get('/{bagian}/edit', [BagianController::class, 'edit'])->name('edit');
        Route::put('/{bagian}', [BagianController::class, 'update'])->name('update');
        Route::delete('/{bagian}', [BagianController::class, 'destroy'])->name('destroy');
    });
});
