<?php

use App\Http\Controllers\Api\AlatController as ApiAlatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BagianController;
use App\Http\Controllers\Api\KalibrasiAlatController as ApiKalibrasiController;
use App\Http\Controllers\Api\KategoriController as ApiKategoriController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PengambilanAlatController as ApiPengambilanAlatController;
use App\Http\Controllers\Api\PengambilanSparepartController;
use App\Http\Controllers\Api\PengembalianAlatController as ApiPengembalianAlatController;
use App\Http\Controllers\Api\PengembalianController;
use App\Http\Controllers\Api\PurchaseRequestController;
use App\Http\Controllers\Api\SparepartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/spareparts/id/{id}', [SparepartController::class, 'showByNumericId'])
    ->name('spareparts.showByNumericId');
Route::post('/login/{spareparts_hashid}', [AuthController::class, 'login']);
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/photo', [AuthController::class, 'uploadPhoto']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    Route::prefix('bagian')->group(function () {
        Route::get('/', [BagianController::class, 'index']);
        Route::post('/', [BagianController::class, 'store']);
        Route::get('/{hashid}', [BagianController::class, 'show']);
        Route::put('/{hashid}', [BagianController::class, 'update']);
        Route::delete('/{hashid}', [BagianController::class, 'destroy']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    // Spareparts
    Route::prefix('spareparts')->name('spareparts.')->group(function () {
        Route::get('/', [SparepartController::class, 'index'])->name('index');
        Route::post('/', [SparepartController::class, 'store'])->name('store');
        Route::get('/{hashid}', [SparepartController::class, 'show'])->name('show');
        Route::put('/{hashid}', [SparepartController::class, 'update'])->name('update');
        Route::delete('/{hashid}', [SparepartController::class, 'destroy'])->name('destroy');

        Route::get('/trashed', [SparepartController::class, 'trashed'])->name('trashed');
        Route::post('/{hashid}/restore', [SparepartController::class, 'restore'])->name('restore');
        Route::delete('/{hashid}/force-delete', [SparepartController::class, 'forceDelete'])->name('force-delete');

        Route::get('/check-stock', [SparepartController::class, 'checkStock']);
        Route::post('/{sparepart}/regenerate-qr', [SparepartController::class, 'regenerateQrCode']);
        Route::get('/generate-all-qr', [SparepartController::class, 'generateAllQrCodes']);
    });

    // ======================== ALAT ========================
    // Pengambilan Alat
   Route::prefix('pengambilan_alat')->group(function () {
    Route::get('/', [ApiPengambilanAlatController::class, 'index']);
    Route::post('/', [ApiPengambilanAlatController::class, 'store']);
    Route::get('/{hashid}', [ApiPengambilanAlatController::class, 'show']);
    Route::put('/{hashid}', [ApiPengambilanAlatController::class, 'update']);
    Route::delete('/{hashid}', [ApiPengambilanAlatController::class, 'destroy']);
});

// Pengembalian Alat
Route::prefix('pengembalian_alat')->group(function () {
    Route::get('/', [ApiPengembalianAlatController::class, 'index']);
    Route::post('/{pengambilanHashid}', [ApiPengembalianAlatController::class, 'store']);
    Route::get('/{hashid}', [ApiPengembalianAlatController::class, 'show']);
    Route::put('/{hashid}', [ApiPengembalianAlatController::class, 'update']);
    Route::delete('/{hashid}', [ApiPengembalianAlatController::class, 'destroy']);
});

    // ======================== SPAREPART (lama) ========================
    // Pengembalian Sparepart
    Route::prefix('pengembalian')->group(function () {
        Route::get('/', [PengembalianController::class, 'index']);
        Route::post('/', [PengembalianController::class, 'store']);
        Route::get('/{hashid}', [PengembalianController::class, 'show']);
        Route::put('/{hashid}', [PengembalianController::class, 'update']);
        Route::delete('/{hashid}', [PengembalianController::class, 'destroy']);
    });

    // Pengambilan Sparepart
    Route::prefix('pengambilan')->name('pengambilan.')->group(function () {
        Route::get('/', [PengambilanSparepartController::class, 'index']);
        Route::post('/', [PengambilanSparepartController::class, 'store']);
        Route::get('/{hashid}', [PengambilanSparepartController::class, 'show']);
        Route::put('/{hashid}', [PengambilanSparepartController::class, 'update']);
        Route::delete('/{hashid}', [PengambilanSparepartController::class, 'destroy']);
    });

    // Purchase Request
    Route::prefix('purchase-requests')->name('purchase-requests.')->group(function () {
        Route::get('/', [PurchaseRequestController::class, 'index']);
        Route::post('/', [PurchaseRequestController::class, 'store']);
        Route::get('/{hashid}', [PurchaseRequestController::class, 'show']);
        Route::put('/{hashid}', [PurchaseRequestController::class, 'update']);
        Route::delete('/{hashid}', [PurchaseRequestController::class, 'destroy']);

        Route::post('/{hashid}/approve', [PurchaseRequestController::class, 'approve']);
        Route::post('/{hashid}/reject', [PurchaseRequestController::class, 'reject']);
        Route::post('/{hashid}/complete', [PurchaseRequestController::class, 'complete']);
    });
    Route::prefix('alat')->name('alat.')->group(function () {
        Route::get('/', [ApiAlatController::class, 'index'])->name('index');
        Route::post('/', [ApiAlatController::class, 'store'])->name('store');
        Route::get('/trashed', [ApiAlatController::class, 'trashed'])->name('trashed');
        Route::get('/expired-alerts', [ApiAlatController::class, 'expiredAlerts'])->name('expired-alerts');

        // Detail alat + restore/force delete
        Route::get('/{hashid}', [ApiAlatController::class, 'show'])->name('show');
        Route::put('/{hashid}', [ApiAlatController::class, 'update'])->name('update');
        Route::delete('/{hashid}', [ApiAlatController::class, 'destroy'])->name('destroy');
        Route::post('/{hashid}/restore', [ApiAlatController::class, 'restore'])->name('restore');
        Route::delete('/{hashid}/force-delete', [ApiAlatController::class, 'forceDelete'])->name('force-delete');

        // Kalibrasi nested di alat
        Route::get('/{hashid}/kalibrasi', [ApiKalibrasiController::class, 'index'])->name('kalibrasi.index');
        Route::post('/{hashid}/kalibrasi', [ApiKalibrasiController::class, 'store'])->name('kalibrasi.store');
    });

    // =================== KALIBRASI (global) =================
    Route::prefix('kalibrasi')->name('kalibrasi.')->group(function () {
        Route::get('/', [ApiKalibrasiController::class, 'index'])->name('index'); // semua kalibrasi
        Route::get('/{hashid}', [ApiKalibrasiController::class, 'show'])->name('show');
        Route::put('/{hashid}', [ApiKalibrasiController::class, 'update'])->name('update');
        Route::delete('/{hashid}', [ApiKalibrasiController::class, 'destroy'])->name('destroy');
    });

    // ====================== KATEGORI ========================
    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/', [ApiKategoriController::class, 'index'])->name('index');
        Route::post('/', [ApiKategoriController::class, 'store'])->name('store');
        Route::get('/{hashid}', [ApiKategoriController::class, 'show'])->name('show');
        Route::put('/{hashid}', [ApiKategoriController::class, 'update'])->name('update');
        Route::delete('/{hashid}', [ApiKategoriController::class, 'destroy'])->name('destroy');
    });
});
