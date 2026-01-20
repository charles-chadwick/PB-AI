<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('users', UserController::class);
Route::post('users/{entity_id}/avatar', [DocumentController::class, 'uploadAvatar'])->defaults('entity_type', 'users')->name('users.avatar.upload');
Route::delete('users/{entity_id}/avatar', [DocumentController::class, 'destroyAvatar'])->defaults('entity_type', 'users')->name('users.avatar.destroy');

Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
Route::get('patients/{patient}/appointments/load-more', [PatientController::class, 'loadMoreAppointments'])->name('patients.appointments.load-more');
Route::resource('patients', PatientController::class);
Route::post('patients/{entity_id}/avatar', [DocumentController::class, 'uploadAvatar'])->defaults('entity_type', 'patients')->name('patients.avatar.upload');
Route::delete('patients/{entity_id}/avatar', [DocumentController::class, 'destroyAvatar'])->defaults('entity_type', 'patients')->name('patients.avatar.destroy');

Route::get('appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
Route::resource('appointments', AppointmentController::class);
