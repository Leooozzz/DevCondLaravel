<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WarningController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
  return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {

  Route::post('/auth/validate', [AuthController::class, 'valideteToken']);
  Route::post('/auth/logout', [AuthController::class, 'logout']);
  //Notice walls
  Route::get('/walls', [WallController::class, 'getAll']);
  Route::post('/wall/{id}/like', [WallController::class, 'like']);
  //Docs
  Route::get('/docs', [DocController::class, 'getAll']);
  //Occurrence book
  Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
  Route::post('/warnings', [WarningController::class, 'setWarning']);
  Route::post('/warning/file', [WarningController::class, 'addWarningFile']);
  //Billets
  Route::get('/billets', [BilletController::class, 'getAll']);
  //Found and lost
  Route::get('/foundandlost', [FoundAndLostController::class, 'getAll']);
  Route::post('/foundandlost', [FoundAndLostController::class, 'insert']);
  Route::put('/foundandlost/{id}', [FoundAndLostController::class, 'update']);
  //Unit
  Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
  Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson']);
  Route::post('/unit/{id}/addvehicle', [UnitController::class, 'addVehicle']);
  Route::post('/unit/{id}/addpet', [UnitController::class, 'addPet']);

  Route::delete('/unit/{id}/removeperson', [UnitController::class, 'removePerson']);
  Route::delete('/unit/{id}/removevehicle', [UnitController::class, 'removeVehicle']);
  Route::delete('/unit/{id}/removepet', [UnitController::class, 'removePet']);
  //Reservations
  Route::get('/reservations', [ReservationController::class, 'getReservation']);
  Route::get('/myreservation', [ReservationController::class, 'myReservation']);

  Route::get('/reservation/{id}/disabledates', [ReservationController::class, 'getDisableDates']);
  Route::get('/reservation/{id}/time', [ReservationController::class, 'getTimes']);

  Route::delete('/myreservation/{id}', [ReservationController::class, 'delMyReservation']);
  Route::post('/reservation/{id}', [ReservationController::class, 'delMyReservation']);
});
