<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', [ProductController::class, 'welcome']);

// product routes
Route::group(['middleware' => 'api', 'prefix' => 'product',], function ($router) {
    Route::get('', [ProductController::class, 'index']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::post('/store', [ProductController::class, 'store']);
    Route::patch('/{id}/update', [ProductController::class, 'update']);
    Route::delete('/{id}/delete', [ProductController::class, 'destroy']);
    Route::get('/getList', [ProductController::class, 'getListProduct']);
});

// stock routes
Route::group(['middleware' => 'api', 'prefix' => 'product',], function ($router) {
    Route::get('/stock', [StockController::class, 'indexStock']);
    Route::post('/addStock', [StockController::class, 'addStock']);
    Route::patch('/update/{id}', [StockController::class, 'update']);
    Route::delete('/delete/{id}', [StockController::class, 'destroy']);
});
Route::group(['middleware' => 'api', 'prefix' => 'transaction',], function ($router) {
    Route::get('/', [TransactionController::class, 'indexTransaction']);
    Route::post('/create', [TransactionController::class, 'createTransaction']);
    Route::patch('/{id}/update', [TransactionController::class, 'updateTransaction']);
    Route::delete('/{id}/delete', [TransactionController::class, 'deleteTransaction']);
});
