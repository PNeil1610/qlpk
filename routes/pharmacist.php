<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pharmacist\PrescriptionController;

// ===== ADMIN & PHARMACIST =====
    Route::middleware(['role:ADMIN,PHARMACIST'])->group(function () {
        // Xác nhận cấp phát thuốc
        Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
            Route::patch('/{id}/status', [PrescriptionController::class, 'updateStatus'])->name('update_status');
        });
    });