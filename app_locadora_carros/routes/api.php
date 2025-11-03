<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('cliente', App\Http\Controllers\ClienteController::class);
Route::apiResource('locacao', App\Http\Controllers\LocacaoController::class);
Route::apiResource('marca', App\Http\Controllers\MarcaController::class);
Route::apiResource('modelo', App\Http\Controllers\ModeloController::class);
