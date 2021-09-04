<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\AuthController;

Route::get('/ping', function() {
    return ['pong'=>true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

//Route::get('/random', [ProfessionalController::class, 'createRandom']);

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/user', [AuthController::class, 'create']);

Route::get('/user', [UserController::class, 'read']);
Route::put('/user', [UserController::class, 'update']);
Route::get('/user/favorites', [UserController::class, 'getFavorites']);
Route::post('/user/favorite', [UserController::class, 'toggleFavorite']);
Route::get('/user/appointments', [UserController::class, 'getAppointments']);


Route::get('/professionals', [ProfessionalController::class, 'list']);
Route::get('/professional/{id}', [ProfessionalController::class, 'one']);
Route::post('/professional/{id}/appointments', [ProfessionalController::class, 'setAppointment']);

Route::get('/search', [ProfessionalController::class, 'search']);


