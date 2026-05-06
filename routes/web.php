<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

// CRUD đầy đủ
// Route::resource('patients', PatientController::class);
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('patients')->name('patients.')->group(function () {

    // Danh sách
    Route::get('/', [PatientController::class, 'index'])->name('index');

    // Form thêm
    Route::get('/create', [PatientController::class, 'create'])->name('create');

    // Lưu dữ liệu
    Route::post('/', [PatientController::class, 'store'])->name('store');

    // Chi tiết
    Route::get('/{id}', [PatientController::class, 'show'])->name('show');

    // Form sửa
    Route::get('/{id}/edit', [PatientController::class, 'edit'])->name('edit');

    // Cập nhật
    Route::put('/{id}', [PatientController::class, 'update'])->name('update');

    // Xóa
    Route::delete('/{id}', [PatientController::class, 'destroy'])->name('destroy');
});


Route::get('/', function () {
    return view('welcome');
});


Route::get('/test', function () {
    return view('hello');
});
