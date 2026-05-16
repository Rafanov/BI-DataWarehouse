<?php

use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\OceanBIController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('datasets', DatasetController::class)->except(['edit', 'update']);
    Route::get('/ocean/mis', [OceanBIController::class, 'mis'])->name('ocean.mis');
    Route::get('/ocean/dss', [OceanBIController::class, 'dss'])->name('ocean.dss');
    Route::post('/datasets/{dataset}/charts', [ChartController::class, 'store'])->name('charts.store');
    Route::delete('/charts/{chart}', [ChartController::class, 'destroy'])->name('charts.destroy');
    Route::get('/datasets/{dataset}/charts/{chart}/data', [ChartController::class, 'data'])->name('charts.data');
    Route::get('/dashboard/chart-data', [ChartController::class, 'dashboardData'])->name('dashboard.chart-data');
});
Route::prefix('api/ocean')->name('ocean.api.')->group(function () {
    Route::get('/production',   [OceanBIController::class, 'apiProduction'])->name('production');
    Route::get('/waste-fate',   [OceanBIController::class, 'apiWasteFate'])->name('waste-fate');
    Route::get('/top-mismanaged', [OceanBIController::class, 'apiTopMismanaged'])->name('top-mismanaged');
    Route::get('/kpi',          [OceanBIController::class, 'apiKpi'])->name('kpi');
    Route::get('/geo',          [OceanBIController::class, 'apiGeo'])->name('geo');
    Route::get('/risk-dist',    [OceanBIController::class, 'apiRiskDist'])->name('risk-dist');
    Route::get('/multivariate', [OceanBIController::class, 'apiMultivariate'])->name('multivariate');
    Route::get('/top-ocean',    [OceanBIController::class, 'apiTopOcean'])->name('top-ocean');
    Route::get('/priority',     [OceanBIController::class, 'apiPriority'])->name('priority');
});

Route::get('/', function () {
    return redirect()->route('dashboard');
});

require __DIR__.'/auth.php';