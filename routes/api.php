<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaboratoireController;
use App\Http\Controllers\Api\UfrController;
use App\Http\Controllers\Api\EquipementController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Middleware\EnsureIsResponsible;
use App\Http\Middleware\CheckAdmin;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('v1/register', [AuthController::class, 'register']);
Route::post('v1/login', [AuthController::class, 'login']);
Route::post('v1/logout', [AuthController::class, 'logout']);

Route::get('v1/users', [UserController::class, 'index']);
Route::get('v1/users/{id}', [UserController::class, 'show']);
Route::post('v1/users', [UserController::class, 'store']);
Route::put('v1/users/{id}', [UserController::class, 'update']);
Route::delete('v1/users/{id}', [UserController::class, 'destroy']);
Route::get('v1/users/{id}/laboratories', [UserController::class, 'laboratoires']);

Route::post('v1/ufrs', [UfrController::class, 'store']);
Route::get('v1/ufrs', [UfrController::class, 'index']);
Route::get('v1/ufrs/{id}', [UfrController::class, 'show']);
Route::put('v1/ufrs/{id}', [UfrController::class, 'update']);

Route::middleware(['auth:sanctum', CheckAdmin::class])->group(function () {
    Route::post('v1/laboratoires', [LaboratoireController::class, 'store']);
    Route::get('v1/laboratoires', [LaboratoireController::class, 'index']);
    Route::get('v1/laboratoires/{id}', [LaboratoireController::class, 'show']);
});
