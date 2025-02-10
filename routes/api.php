<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/userInfo', [App\Http\Controllers\Api\AuthController::class, 'userInfo'])->middleware('auth:sanctum');
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
