<?php

namespace App\Http\Controllers;

use App\Models\Dataset;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDatasets = Dataset::where('user_id', auth()->id())->count();
        $totalRows = Dataset::where('user_id', auth()->id())->sum('row_count');
        $totalCols = Dataset::where('user_id', auth()->id())->sum('column_count');
        $recentDatasets = Dataset::where('user_id', auth()->id())->latest()->take(5)->get();

        return view('dashboard', compact('totalDatasets', 'totalRows', 'totalCols', 'recentDatasets'));
    }
}