<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DiagnosisController;
// CRUD đầy đủ
// Route::resource('patients', PatientController::class);
// Route::resource('consultations', ConsultationController::class);

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


Route::prefix('consultations')->name('consultations.')->group(function () {

    Route::get('/', [ConsultationController::class, 'index'])->name('index');
    Route::get('/create', [ConsultationController::class, 'create'])->name('create');
    Route::post('/', [ConsultationController::class, 'store'])->name('store');

    Route::get('/{id}', [ConsultationController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ConsultationController::class, 'edit'])->name('edit');

    Route::put('/{id}', [ConsultationController::class, 'update'])->name('update');
    Route::delete('/{id}', [ConsultationController::class, 'destroy'])->name('destroy');

});
Route::get('/consultations/ajax/search', [ConsultationController::class, 'ajaxSearch'])->name('consultations.ajax.search');


Route::prefix('diagnoses')->name('diagnoses.')->group(function () {
    Route::get('/', [DiagnosisController::class, 'index'])->name('index');
    Route::get('/create', [DiagnosisController::class, 'create'])->name('create');
    Route::post('/', [DiagnosisController::class, 'store'])->name('store');
    Route::get('/ajax/search', [DiagnosisController::class, 'ajaxSearch'])->name('ajax.search');
    Route::get('/{id}', [DiagnosisController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [DiagnosisController::class, 'edit'])->name('edit');
    Route::put('/{id}', [DiagnosisController::class, 'update'])->name('update');
    Route::delete('/{id}', [DiagnosisController::class, 'destroy'])->name('destroy');
});
Route::get('diagnoses/ajax/search', [DiagnosisController::class, 'ajaxSearch'])->name('diagnoses.ajax.search');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/patients/ajax/search', [PatientController::class, 'ajaxSearch'])
    ->name('patients.ajax.search');
Route::get(
    '/consultations/ajax/search',
    [ConsultationController::class, 'ajaxSearch']
)
    ->name('consultations.ajax.search');

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');