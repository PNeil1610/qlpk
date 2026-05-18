<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes (Shared)
|--------------------------------------------------------------------------
|
| Routes dùng chung cho tất cả người dùng: Auth, Home, Profile.
| Các route theo domain được tách sang:
|   - routes/admin.php   → Quản trị (CRUD)
|   - routes/doctor.php  → Bác sĩ (Khám bệnh)
|   - routes/cashier.php → Thu ngân (Hóa đơn)
|   - routes/patient.php → Bệnh nhân (Tự phục vụ)
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Home (sau đăng nhập)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Profile
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change_password');
Route::post('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update_password');

// ===== THÔNG BÁO NỘI BỘ =====
// Accessible cho tất cả nhân viên đăng nhập (trừ PATIENT)
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/',                 [NotificationController::class, 'index'])->name('index');
    Route::get('/create',           [NotificationController::class, 'create'])->name('create');
    Route::post('/',                [NotificationController::class, 'store'])->name('store');
    Route::get('/{id}',             [NotificationController::class, 'show'])->name('show');
    Route::delete('/{id}',          [NotificationController::class, 'destroy'])->name('destroy');
    Route::get('/ajax/unread-count',[NotificationController::class, 'ajaxUnreadCount'])->name('ajax.unread');
});