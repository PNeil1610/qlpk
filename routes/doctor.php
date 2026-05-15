<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\ExaminationController;
use App\Http\Controllers\Admin\DiagnosisController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\MedicineCategoryController;

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
|
| Các route dành cho Bác sĩ: xem danh sách ca khám, khám bệnh, kê đơn.
|
*/

Route::middleware(['auth', 'role:DOCTOR'])->group(function () {

    // Danh sách ca khám của bác sĩ
    Route::get('/consultations/doctor/my-consultations', [ExaminationController::class, 'index'])
        ->name('consultations.doctor');

    // AJAX tìm kiếm ca khám
    Route::get('/consultations/doctor/ajax/search', [ExaminationController::class, 'ajaxSearch'])
        ->name('consultations.doctor.ajax.search');

    // Khám bệnh
    Route::get('/consultations/doctor/{id}/examine', [ExaminationController::class, 'examine'])
        ->name('consultations.doctor.examine');

    // Danh sách bệnh nhân phòng của tôi
    Route::get('/my-clinic', [ExaminationController::class, 'myRoomPatients'])
        ->name('doctor.my_room');

    Route::prefix('diagnoses')->name('diagnoses.')->group(function () {
        Route::get('/', [DiagnosisController::class, 'index'])->name('index');
        Route::get('/ajax/search', [DiagnosisController::class, 'ajaxSearch'])->name('ajax.search');
        Route::get('/{id}', [DiagnosisController::class, 'show'])->name('show');
    });

    Route::prefix('medicine-categories')->name('medicine_categories.')->group(function () {
            Route::get('/ajax/search', [MedicineCategoryController::class, 'ajaxSearch'])->name('ajax.search');
            Route::get('/', [MedicineCategoryController::class, 'index'])->name('index');
            Route::get('/{id}', [MedicineCategoryController::class, 'show'])->name('show');
    });
    
    Route::prefix('medicines')->name('medicines.')->group(function () {
            Route::get('/ajax/search', [MedicineController::class, 'ajaxSearch'])->name('ajax.search');
            Route::get('/', [MedicineController::class, 'index'])->name('index');
            Route::get('/{id}', [MedicineController::class, 'show'])->name('show');
            Route::get('/{id}/details', [MedicineController::class, 'details'])->name('details');
    });
});
