<?php

use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;




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
Route::apiResource('sales', SalesController::class);
Route::post('/sales', [SalesController::class, 'store']);
Route::get('/sales', [SalesController::class, 'index']);
Route::get('/sales/{id}', [SalesController::class, 'show']);
Route::put('/sales/{id}', [SalesController::class, 'update']);
Route::delete('/sales/{id}', [SalesController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::options('/sale', function (Request $request) {
    return response()->json(['message' => 'Preflight OK'], 200);
});
