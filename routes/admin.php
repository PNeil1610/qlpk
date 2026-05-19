<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\DiagnosisController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ConsultationRoomController;
use App\Http\Controllers\Admin\RoomDetailController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\MedicineCategoryController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\PrescriptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SystemSettingController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Các route quản trị hệ thống: CRUD bệnh nhân, phiên khám, thuốc,
| nhân sự, phòng ban, v.v. Chỉ dành cho ADMIN và RECEPTIONIST.
|
*/

Route::middleware(['auth'])->group(function () {

    // ===== ADMIN & RECEPTIONIST =====
    Route::middleware(['role:ADMIN,RECEPTIONIST'])->group(function () {
        // Bệnh nhân
        Route::prefix('patients')->name('patients.')->group(function () {
            Route::get('/search', [PatientController::class, 'search'])->name('search');
            Route::get('/', [PatientController::class, 'index'])->name('index');
            Route::get('/create', [PatientController::class, 'create'])->name('create');
            Route::post('/', [PatientController::class, 'store'])->name('store');
            Route::get('/{id}', [PatientController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [PatientController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PatientController::class, 'update'])->name('update');
            Route::delete('/{id}', [PatientController::class, 'destroy'])->name('destroy');
        });
        Route::get('/patients/ajax/search', [PatientController::class, 'ajaxSearch'])->name('patients.ajax.search');

        // Phiên khám (Admin CRUD)
        Route::prefix('consultations')->name('consultations.')->group(function () {
            Route::get('/', [ConsultationController::class, 'index'])->name('index');
            Route::get('/create', [ConsultationController::class, 'create'])->name('create');
            Route::post('/', [ConsultationController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ConsultationController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ConsultationController::class, 'update'])->name('update');
            Route::delete('/{id}', [ConsultationController::class, 'destroy'])->name('destroy');
        });
        Route::get('/consultations/ajax/search', [ConsultationController::class, 'ajaxSearch'])->name('consultations.ajax.search');

        // Xếp phòng khám
        Route::prefix('consultation-rooms')
            ->name('consultation_rooms.')
            ->controller(ConsultationRoomController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{consultation_id}/{room_id}', 'show')->name('show');
                Route::get('/{consultation_id}/{room_id}/edit', 'edit')->name('edit');
                Route::put('/{consultation_id}/{room_id}', 'update')->name('update');
                Route::delete('/{consultation_id}/{room_id}', 'destroy')->name('destroy');
            });
    });

    // ===== ADMIN ONLY =====
    Route::middleware(['role:ADMIN'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [\App\Http\Controllers\Admin\DashboardController::class, 'getStats'])->name('dashboard.stats');

        // Mã bệnh (CRUD)
        Route::prefix('diagnoses')->name('diagnoses.')->group(function () {
            Route::get('/create', [DiagnosisController::class, 'create'])->name('create');
            Route::post('/', [DiagnosisController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [DiagnosisController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DiagnosisController::class, 'update'])->name('update');
            Route::delete('/{id}', [DiagnosisController::class, 'destroy'])->name('destroy');
        });

        // Khoa
        Route::prefix('departments')->name('departments.')->controller(DepartmentController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/ajax/search', 'ajaxSearch')->name('ajax.search');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Phòng
        Route::prefix('rooms')->name('rooms.')->controller(RoomController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/ajax/search', 'ajaxSearch')->name('ajax.search');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Chi tiết phòng
        Route::get('/room-details/ajax/search', [RoomDetailController::class, 'ajaxSearch'])->name('room_details.ajax.search');
        Route::resource('room-details', RoomDetailController::class)->names('room_details');

        // Nhân viên
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::get('/', [StaffController::class, 'index'])->name('index');
            Route::get('/ajax/search', [StaffController::class, 'ajaxSearch'])->name('ajax.search');
            Route::get('/create', [StaffController::class, 'create'])->name('create');
            Route::post('/', [StaffController::class, 'store'])->name('store');
            Route::get('/{id}', [StaffController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('edit');
            Route::put('/{id}', [StaffController::class, 'update'])->name('update');
            Route::delete('/{id}', [StaffController::class, 'destroy'])->name('destroy');
        });

        // Loại thuốc (CRUD)
        Route::prefix('medicine-categories')->name('medicine_categories.')->group(function () {
            Route::get('/create', [MedicineCategoryController::class, 'create'])->name('create');
            Route::post('/', [MedicineCategoryController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MedicineCategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MedicineCategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [MedicineCategoryController::class, 'destroy'])->name('destroy');
        });

        // Thuốc (CRUD)
        Route::prefix('medicines')->name('medicines.')->group(function () {
            Route::get('/create', [MedicineController::class, 'create'])->name('create');
            Route::post('/', [MedicineController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [MedicineController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MedicineController::class, 'update'])->name('update');
            Route::delete('/{id}', [MedicineController::class, 'destroy'])->name('destroy');

            Route::get('/{id}/details/create', [MedicineController::class, 'createDetail'])->name('details.create');
            Route::post('/{id}/details', [MedicineController::class, 'storeDetail'])->name('details.store');
            Route::get('/details/{detail_id}/edit', [MedicineController::class, 'editDetail'])->name('details.edit');
            Route::put('/details/{detail_id}', [MedicineController::class, 'updateDetail'])->name('details.update');
            Route::delete('/details/{detail_id}', [MedicineController::class, 'destroyDetail'])->name('details.destroy');
        });

        // Quản lý tài khoản người dùng
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/ajax/search', [UserController::class, 'ajaxSearch'])->name('ajax.search');
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/{id}', [UserController::class, 'show'])->name('show');
            Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle_status');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('reset_password');
        });

        // Cấu hình phòng khám (chỉ ADMIN)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SystemSettingController::class, 'index'])->name('index');
            Route::get('/create', [SystemSettingController::class, 'create'])->name('create');
            Route::post('/', [SystemSettingController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [SystemSettingController::class, 'edit'])->name('edit');
            Route::put('/{id}', [SystemSettingController::class, 'update'])->name('update');
            Route::delete('/{id}', [SystemSettingController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/activate', [SystemSettingController::class, 'activate'])->name('activate');
            Route::post('/{id}/deactivate', [SystemSettingController::class, 'deactivate'])->name('deactivate');
        });
    });

    // ===== ADMIN, DOCTOR & PHARMACIST =====
    Route::middleware(['role:ADMIN,DOCTOR,PHARMACIST'])->group(function () {
        // Đơn thuốc (Chỉ xem)
        Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
            Route::get('/', [PrescriptionController::class, 'index'])->name('index');
            Route::get('/{id}/show', [PrescriptionController::class, 'show'])->name('show');
        });

        // ================= DANH MỤC DÙNG CHUNG (XEM) =================
        // Mã bệnh (Xem)
        Route::prefix('diagnoses')->name('diagnoses.')->group(function () {
            Route::get('/', [DiagnosisController::class, 'index'])->name('index');
            Route::get('/ajax/search', [DiagnosisController::class, 'ajaxSearch'])->name('ajax.search');
            Route::get('/{id}', [DiagnosisController::class, 'show'])->name('show');
        });

        // Loại thuốc (Xem)
        Route::prefix('medicine-categories')->name('medicine_categories.')->group(function () {
            Route::get('/ajax/search', [MedicineCategoryController::class, 'ajaxSearch'])->name('ajax.search');
            Route::get('/', [MedicineCategoryController::class, 'index'])->name('index');
            Route::get('/{id}', [MedicineCategoryController::class, 'show'])->name('show');
        });

        // Thuốc (Xem)
        Route::prefix('medicines')->name('medicines.')->group(function () {
            Route::get('/ajax/search', [MedicineController::class, 'ajaxSearch'])->name('ajax.search');
            Route::get('/', [MedicineController::class, 'index'])->name('index');
            Route::get('/{id}/show', [MedicineController::class, 'show'])->name('show');
            Route::get('/{id}/details', [MedicineController::class, 'details'])->name('details');
        });
    });

    // ===== ADMIN & PHARMACIST =====
    Route::middleware(['role:ADMIN,PHARMACIST'])->group(function () {
        // Xác nhận cấp phát thuốc
        Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
            Route::patch('/{id}/status', [PrescriptionController::class, 'updateStatus'])->name('update_status');
        });
    });

    // ===== ADMIN & DOCTOR =====
    Route::middleware(['role:ADMIN,DOCTOR'])->group(function () {
        // Đơn thuốc (CRUD)
        Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
            Route::get('/create', [PrescriptionController::class, 'create'])->name('create');
            Route::post('/', [PrescriptionController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PrescriptionController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PrescriptionController::class, 'update'])->name('update');
            Route::delete('/{id}', [PrescriptionController::class, 'destroy'])->name('destroy');
        });
    });

    // ===== ADMIN, RECEPTIONIST & DOCTOR =====
    Route::middleware(['role:ADMIN,RECEPTIONIST,DOCTOR'])->group(function () {
        // Cho phép bác sĩ xem chi tiết phiên khám cũ
        Route::get('/consultations/{id}', [ConsultationController::class, 'show'])->name('consultations.show');
    });

});
