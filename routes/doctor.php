<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\ExaminationController;
use App\Http\Controllers\Doctor\PrescriptionTemplateController;
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

    // ✅ Lưu kết luận chuyên môn (route riêng cho Doctor, tránh lỗi 403 từ admin route)
    Route::put('/doctor/consultations/{id}/conclusion', [ExaminationController::class, 'saveConclusion'])
        ->name('doctor.consultation.save_conclusion');

    // Lịch sử khám bệnh của bác sĩ
    Route::get('/doctor/my-history', [ExaminationController::class, 'myHistory'])
        ->name('doctor.my_history');

    // Lịch sử khám bệnh của bệnh nhân
    Route::get('/doctor/patient-history', [ExaminationController::class, 'patientHistory'])
        ->name('doctor.patient_history');

    // Quản lý toa thuốc mẫu
    Route::prefix('templates')->name('doctor.templates.')->group(function () {
        Route::get('/ajax/get-all', [PrescriptionTemplateController::class, 'ajaxGetTemplates'])->name('ajax');
        Route::get('/', [PrescriptionTemplateController::class, 'index'])->name('index');
        Route::get('/create', [PrescriptionTemplateController::class, 'create'])->name('create');
        Route::post('/', [PrescriptionTemplateController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PrescriptionTemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PrescriptionTemplateController::class, 'update'])->name('update');
        Route::delete('/{id}', [PrescriptionTemplateController::class, 'destroy'])->name('destroy');
    });

    // Danh sách bệnh nhân phòng của tôi
    Route::get('/my-clinic', [ExaminationController::class, 'myRoomPatients'])
        ->name('doctor.my_room');


});
