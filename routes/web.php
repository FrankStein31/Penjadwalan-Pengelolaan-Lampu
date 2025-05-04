<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LampuController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\EnergiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/lampu', [LampuController::class, 'index'])->name('lampu.index');

Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

Route::resource('lampu', LampuController::class);

Route::resource('jadwal', JadwalController::class);
