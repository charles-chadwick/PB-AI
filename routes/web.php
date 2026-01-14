<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('users', UserController::class);
Route::resource('patients', PatientController::class);
