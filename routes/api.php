<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BagianController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SparepartController;
use App\Http\Controllers\Api\PengambilanSparepartController;
use App\Http\Controllers\Api\PurchaseRequestController;
use App\Http\Controllers\Api\NotificationController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/photo', [AuthController::class, 'uploadPhoto']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    // Bagian
    Route::apiResource('bagian', BagianController::class);

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    // Spareparts
    Route::prefix('spareparts')->group(function () {
        Route::get('/', [SparepartController::class, 'index']);
        Route::post('/', [SparepartController::class, 'store']);
        Route::get('/{sparepart}', [SparepartController::class, 'show']);
        Route::put('/{sparepart}', [SparepartController::class, 'update']);
        Route::delete('/{sparepart}', [SparepartController::class, 'destroy']);

        Route::get('/check-stock', [SparepartController::class, 'checkStock']);
        Route::post('/{sparepart}/regenerate-qr', [SparepartController::class, 'regenerateQrCode']);
        Route::get('/generate-all-qr', [SparepartController::class, 'generateAllQrCodes']);
    });

    // Pengambilan Sparepart
    Route::prefix('pengambilan')->group(function () {
        Route::get('/', [PengambilanSparepartController::class, 'index']);
        Route::post('/', [PengambilanSparepartController::class, 'store']);
        Route::get('/{pengambilan}', [PengambilanSparepartController::class, 'show']);
        Route::put('/{pengambilan}', [PengambilanSparepartController::class, 'update']);
        Route::delete('/{pengambilan}', [PengambilanSparepartController::class, 'destroy']);
    });

    // Purchase Request
    Route::prefix('purchase-requests')->group(function () {
        Route::get('/', [PurchaseRequestController::class, 'index']);
        Route::post('/', [PurchaseRequestController::class, 'store']);
        Route::get('/{purchaseRequest}', [PurchaseRequestController::class, 'show']);
        Route::put('/{purchaseRequest}', [PurchaseRequestController::class, 'update']);
        Route::delete('/{purchaseRequest}', [PurchaseRequestController::class, 'destroy']);

        Route::post('/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve']);
        Route::post('/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject']);
        Route::post('/{purchaseRequest}/complete', [PurchaseRequestController::class, 'complete']);
    });
});
