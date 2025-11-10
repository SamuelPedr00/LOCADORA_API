<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('modelo', App\Http\Controllers\ModeloController::class);
Route::apiResource('cliente', App\Http\Controllers\ClienteController::class);
Route::apiResource('locacao', App\Http\Controllers\LocacaoController::class);
Route::apiResource('marca', App\Http\Controllers\MarcaController::class);


Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
Route::post('/refresh', [\App\Http\Controllers\AuthController::class, 'refresh']);
Route::post('/me', [\App\Http\Controllers\AuthController::class, 'me']);

Route::get('/login', function () {
    return response()->json(['message' => 'Não autenticado'], 401);
})->name('login');
