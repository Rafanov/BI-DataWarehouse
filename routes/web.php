<?php

use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('datasets', DatasetController::class)->except(['edit', 'update']);
    
    // Chart routes
    Route::post('/datasets/{dataset}/charts', [ChartController::class, 'store'])->name('charts.store');
    Route::delete('/charts/{chart}', [ChartController::class, 'destroy'])->name('charts.destroy');
    Route::get('/datasets/{dataset}/charts/{chart}/data', [ChartController::class, 'data'])->name('charts.data');
    Route::get('/dashboard/chart-data', [ChartController::class, 'dashboardData'])->name('dashboard.chart-data');
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});

require __DIR__.'/auth.php';