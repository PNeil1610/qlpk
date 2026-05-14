<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\ExaminationController;

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
|
| Các route dành cho Bác sĩ: xem danh sách ca khám, khám bệnh, kê đơn.
|
*/

Route::middleware(['auth', 'role:DOCTOR,SUPERADMIN'])->group(function () {

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

});
