<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/', [ProductController::class, 'healthCheck']);

Route::put('/products/{code}', [ProductController::class, 'updateProduct']);

Route::delete(('/products/{code}'), [ProductController::class, 'deleteProduct']);

Route::get('/products/{code}', [ProductController::class, 'getProduct']);

Route::get('/products', [ProductController::class, 'getAllProducts']);