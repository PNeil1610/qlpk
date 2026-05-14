<?php

use Illuminate\Support\Facades\Route;

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