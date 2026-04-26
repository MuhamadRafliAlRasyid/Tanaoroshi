<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BagianController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PengambilanSparepartController;
use App\Http\Controllers\Api\PengembalianController;
use App\Http\Controllers\Api\PurchaseRequestController;
use App\Http\Controllers\Api\SparepartController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Khusus untuk QR Scan Mobile (support numeric ID)
Route::get('/spareparts/id/{id}', [SparepartController::class, 'showByNumericId'])
    ->name('spareparts.showByNumericId');
Route::post('/login/{spareparts_hashid}', [AuthController::class, 'login']);
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

    // Pengembalian
    Route::prefix('pengembalian')->group(function () {
    Route::get('/', [PengembalianController::class, 'index']);
    Route::post('/', [PengembalianController::class, 'store']);
    Route::get('/{hashid}', [PengembalianController::class, 'show']);
    Route::put('/{hashid}', [PengembalianController::class, 'update']);
    Route::delete('/{hashid}', [PengembalianController::class, 'destroy']);
});

    // Pengambilan
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
});
