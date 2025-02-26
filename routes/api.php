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

Route::get('/suppliers', [App\Http\Controllers\Api\SupplierController::class, 'index'])->middleware('auth:sanctum');
Route::get('/suppliers/{id}', [App\Http\Controllers\Api\SupplierController::class, 'show'])->middleware('auth:sanctum');
Route::post('/suppliers', [App\Http\Controllers\Api\SupplierController::class, 'store'])->middleware('auth:sanctum');
Route::put('/suppliers/{id}', [App\Http\Controllers\Api\SupplierController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/suppliers/{id}', [App\Http\Controllers\Api\SupplierController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index'])->middleware('auth:sanctum');
Route::get('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'show'])->middleware('auth:sanctum');
Route::post('/products', [App\Http\Controllers\Api\ProductController::class, 'store'])->middleware('auth:sanctum');
Route::put('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/customers', [App\Http\Controllers\Api\CustomerController::class, 'index'])->middleware('auth:sanctum');
Route::get('/customers/{id}', [App\Http\Controllers\Api\CustomerController::class, 'show'])->middleware('auth:sanctum');
Route::post('/customers', [App\Http\Controllers\Api\CustomerController::class, 'store'])->middleware('auth:sanctum');
Route::put('/customers/{id}', [App\Http\Controllers\Api\CustomerController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/customers/{id}', [App\Http\Controllers\Api\CustomerController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/sales', [App\Http\Controllers\Api\SalesController::class, 'index'])->middleware('auth:sanctum');
Route::get('/sales/{id}', [App\Http\Controllers\Api\SalesController::class, 'show'])->middleware('auth:sanctum');
Route::post('/sales', [App\Http\Controllers\Api\SalesController::class, 'store'])->middleware('auth:sanctum');
Route::put('/sales/{id}', [App\Http\Controllers\Api\SalesController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/sales/{id}', [App\Http\Controllers\Api\SalesController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/carts', [App\Http\Controllers\Api\CartController::class, 'index'])->middleware('auth:sanctum');
Route::get('/carts/{id}', [App\Http\Controllers\Api\CartController::class, 'show'])->middleware('auth:sanctum');
Route::post('/carts', [App\Http\Controllers\Api\CartController::class, 'store'])->middleware('auth:sanctum');
Route::put('/carts/{id}', [App\Http\Controllers\Api\CartController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/carts/{id}', [App\Http\Controllers\Api\CartController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/purchases', [App\Http\Controllers\Api\PurchaseController::class, 'index'])->middleware('auth:sanctum');
Route::get('/search/purchases', [App\Http\Controllers\Api\PurchaseController::class, 'search']);
Route::get('/purchases/{id}', [App\Http\Controllers\Api\PurchaseController::class, 'show'])->middleware('auth:sanctum');
Route::post('/purchases', [App\Http\Controllers\Api\PurchaseController::class, 'store'])->middleware('auth:sanctum');
Route::put('/purchases/{id}', [App\Http\Controllers\Api\PurchaseController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/purchases/{id}', [App\Http\Controllers\Api\PurchaseController::class, 'destroy'])->middleware('auth:sanctum');
