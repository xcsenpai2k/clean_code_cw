<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('customer', CustomerController::class)->except(['store', 'show']);

    Route::apiResource('product', ProductController::class)->except(['index', 'show']);

    Route::apiResource('order', OrderController::class)->except(['update', 'destroy']);

    Route::put('order/change-status/{id}', [OrderController::class, 'changeStatusOrder']);

    Route::get('/dashboard/count', [DashboardController::class, 'count']);
    Route::get('/dashboard/top-user-orders', [DashboardController::class, 'topUserOrders']);
    Route::get('/dashboard/top-sellers', [DashboardController::class, 'topSellers']);

    Route::get('/report', [ReportController::class, 'index']);
    Route::get('/report/orders', [ReportController::class, 'OrdersReport']);
});

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,60');
