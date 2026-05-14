<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Patient\MyRecordController;

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
|
| Các route tự phục vụ cho Bệnh nhân: xem hồ sơ bệnh án cá nhân.
|
*/

Route::middleware(['auth', 'role:PATIENT'])->group(function () {

    Route::get('/my-medical-record', [MyRecordController::class, 'index'])
        ->name('patients.my_record');

});
