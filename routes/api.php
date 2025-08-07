<?php

use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'id'     => $request->user()->id,
        'name'   => $request->user()->name,
        'email'  => $request->user()->email,
        'role'   => $request->user()->role->slug,
        'amount' => $request->user()->amount,
    ]);
});

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::get('roles', [RoleController::class, 'index']);
    Route::get('orders/statuses', [AdminOrderController::class, 'statuses']);
    Route::apiResource('orders', AdminOrderController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
    Route::put('orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    Route::post('transactions', [AdminTransactionController::class, 'store']);
});

Route::middleware('auth:sanctum')->get('/transactions', [TransactionController::class, 'index']);
Route::middleware('auth:sanctum')->post('/transfer', [TransferController::class, 'store']);

Route::middleware('auth:sanctum')->get('/users/list', function (Request $request) {
    return \App\Models\User::query()
        ->where('id', '!=', $request->user()->id)
        ->select('id', 'email', 'name')
        ->orderBy('email')
        ->get();
});
Route::middleware('auth:sanctum')->post('/orders', [OrderController::class, 'store']);
