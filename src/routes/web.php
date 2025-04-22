<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\DetailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance/store', [AttendanceController::class, 'store']);
    Route::post('/attendance/update_work', [AttendanceController::class, 'updateWork']);
    Route::post('/attendance/update_rest', [AttendanceController::class, 'updateRest']);
    Route::get('/attendance/list', [ListController::class, 'index']);
    Route::get('/attendance/list/month', [ListController::class, 'indexMonth']);
    Route::get('/attendance/{id}', [DetailController::class, 'index']);
});
