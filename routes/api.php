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
Route::delete('v1/ufrs/{id}', [UfrController::class, 'destroy']);

Route::middleware(['auth:sanctum', CheckAdmin::class])->group(function () {


    Route::get('v1/laboratoires/{id}', [LaboratoireController::class, 'show']);
});
Route::post('v1/laboratoires', [LaboratoireController::class, 'store']);
Route::get('v1/laboratoires', [LaboratoireController::class, 'index']);
Route::put('v1/laboratoires/{id}', [LaboratoireController::class, 'update']);
Route::delete('v1/laboratoires/{id}', [LaboratoireController::class, 'destroy']);

Route::middleware(['auth:sanctum', EnsureIsResponsible::class])->group(function () {
    Route::get('v1/laboratoires/{id}/equipements', [LaboratoireController::class, 'getEquipements']);
    Route::get('v1/equipements', [EquipementController::class, 'index']);
    Route::get('v1/equipements/{id}', [EquipementController::class, 'show']);
    Route::post('v1/equipements', [EquipementController::class, 'store']);
    Route::put('v1/equipements/{id}', [EquipementController::class, 'update']);
    Route::delete('v1/equipements/{id}', [EquipementController::class, 'destroy']);
    Route::get('v1/equipements/{id}/reservations', [EquipementController::class, 'reservations']);

});

Route::get('v1/equipements', [EquipementController::class, 'index']);
Route::get('v1/equipements/{id}', [EquipementController::class, 'show']);
Route::post('v1/equipements', [EquipementController::class, 'store']);
Route::put('v1/equipements/{id}', [EquipementController::class, 'update']);
Route::delete('v1/equipements/{id}', [EquipementController::class, 'destroy']);

Route::get('v1/reservations', [ReservationController::class, 'index']);
Route::get('v1/reservations/{id}', [ReservationController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('v1/reservations', [ReservationController::class, 'store']);
});
Route::post('v1/reservations/guest', [ReservationController::class, 'store']);
Route::put('v1/reservations/update/{id}', [ReservationController::class, 'update']);
Route::delete('v1/reservations/{id}', [ReservationController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('v1/reservations/valider/{id}', [ReservationController::class, 'validerReservation']);
});

Route::get('v1/users/role/{role}', [UserController::class, 'getUsersbyRole']);
Route::post('v1/reservations/rejeter/{id}', [ReservationController::class, 'rejeterReservation']);
Route::get('v1/reservations/code/{code}', [ReservationController::class, 'getReservationByCode']);
Route::put('v1/reservations/{id}', [ReservationController::class, 'update'])->middleware('auth:sanctum');
Route::post('v1/reservations/annuler/{code}', [ReservationController::class, 'annulerReservation']);
Route::get('v1/user/reservations', [ReservationController::class, 'getUserReservations']);
