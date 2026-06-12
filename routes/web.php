<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\KeycloakController;

/*
|--------------------------------------------------------------------------
| Web Routes (Shared)
|--------------------------------------------------------------------------
|
| Routes dùng chung cho tất cả người dùng: Auth (Keycloak OIDC), Home, Profile.
| Các route theo domain được tách sang:
|   - routes/admin.php   → Quản trị (CRUD)
|   - routes/doctor.php  → Bác sĩ (Khám bệnh)
|   - routes/cashier.php → Thu ngân (Hóa đơn)
|   - routes/patient.php → Bệnh nhân (Tự phục vụ)
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// ===== KEYCLOAK OIDC AUTH =====
// Trang đăng nhập trung gian (chứa nút "Đăng nhập qua Keycloak")
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect('/home');
    }
    return view('auth.login');
})->name('login');

// Redirect sang Keycloak login page
Route::get('/auth/redirect', [KeycloakController::class, 'redirect'])->name('keycloak.redirect');

// Keycloak callback sau khi đăng nhập
Route::get('/auth/callback', [KeycloakController::class, 'callback'])->name('keycloak.callback');

// Đăng xuất (Laravel + Keycloak Single Logout)
Route::post('/auth/logout', [KeycloakController::class, 'logout'])->name('logout');
Route::get('/auth/logout', [KeycloakController::class, 'logout']); // fallback GET

// Home (sau đăng nhập)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    // Đổi mật khẩu → redirect Keycloak Account Console
    Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change_password');
});

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