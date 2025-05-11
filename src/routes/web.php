<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\AdminListController;
use App\Http\Controllers\admin\StaffController;
use App\Http\Controllers\admin\IndividualController;
use GuzzleHttp\Psr7\Request;


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
    Route::get('/stamp_correction_request/list', [RequestController::class, 'index']);
    Route::get('/stamp_correction_request/list/index_wait', [RequestController::class, 'indexWait']);
    Route::get('/stamp_correction_request/list/index_approved', [RequestController::class, 'indexApproved']);
});

Route::group(['prefix' => 'admin'], function () {
    // ログイン
    Route::get('login', [AdminLoginController::class, 'index'])->name('admin.login');
    Route::post('login', [AdminLoginController::class, 'login']);
    // 以下の中は認証必須のエンドポイントとなる
    Route::middleware(['admin'])->group(function () {
        Route::get('/attendance/list', [AdminListController::class, 'index'])->name('admin.attendance.list');
        Route::get('/attendance/list/day', [AdminListController::class, 'indexDay']);
        Route::get('/staff/list', [StaffController::class, 'index']);
        Route::get('/attendance/staff/{id}', [IndividualController::class, 'index']);
    });
});

Route::middleware(['auth', 'admin'])->group(
    function () {
        Route::get('/attendance/{id}', [DetailController::class, 'index']);
        Route::post('/attendance/{id}/update', [DetailController::class, 'update']);
    }
);
