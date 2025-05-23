<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LampuController;
use App\Http\Controllers\JadwalController;

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

// Route API untuk update status lampu
Route::post('/lampu/{id}/status', [LampuController::class, 'updateStatus']);

// Route API untuk statistik penggunaan lampu
Route::get('/lampu/{id}/statistik', [LampuController::class, 'getStatistik']);

// Route API untuk total penggunaan semua lampu
Route::get('/lampu/total-penggunaan', [LampuController::class, 'getTotalPenggunaan']);

// Route API untuk menjalankan jadwal
Route::get('/jadwal/execute', [JadwalController::class, 'executeSchedule']);
