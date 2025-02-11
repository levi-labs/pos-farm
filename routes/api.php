<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/userInfo', [App\Http\Controllers\Api\AuthController::class, 'userInfo'])->middleware('auth:sanctum');
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index'])->middleware('auth:sanctum');
Route::get('/categories/{id}', [App\Http\Controllers\Api\CategoryController::class, 'show'])->middleware('auth:sanctum');
Route::post('/categories', [App\Http\Controllers\Api\CategoryController::class, 'store'])->middleware('auth:sanctum');
Route::put('/categories/{id}', [App\Http\Controllers\Api\CategoryController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/categories/{id}', [App\Http\Controllers\Api\CategoryController::class, 'destroy'])->middleware('auth:sanctum');
