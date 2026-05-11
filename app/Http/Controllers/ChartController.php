<?php

namespace App\Http\Controllers;

use App\Models\ChartConfig;
use App\Models\Dataset;
use Illuminate\Http\Request;
use League\Csv\Reader;

class ChartController extends Controller
{
    public function store(Request $request, Dataset $dataset)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'chart_type' => 'required|in:bar,line,pie,doughnut',
            'x_column' => 'required|string',
            'y_column' => 'required|string',
            'limit' => 'nullable|integer|min:5|max:100',
        ]);

        ChartConfig::create([
            'dataset_id' => $dataset->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'chart_type' => $request->chart_type,
            'x_column' => $request->x_column,
            'y_column' => $request->y_column,
            'limit' => $request->limit ?? 10,
        ]);

        return back()->with('success', 'Chart berhasil ditambahkan!');
    }

    public function destroy(ChartConfig $chart)
    {
        $chart->delete();
        return back()->with('success', 'Chart dihapus!');
    }

    // API endpoint untuk data chart
    public function data(Dataset $dataset, ChartConfig $chart)
    {
        $fullPath = storage_path('app/private/' . $dataset->file_path);
        $csv = Reader::createFromPath($fullPath, 'r');
        $csv->setHeaderOffset(0);
        $records = iterator_to_array($csv->getRecords());
        $records = array_slice($records, 0, $chart->limit);

        $labels = array_map(fn($r) => $r[$chart->x_column] ?? '?', $records);
        $values = array_map(fn($r) => is_numeric($r[$chart->y_column] ?? '') ? (float)$r[$chart->y_column] : 0, $records);

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'chart_type' => $chart->chart_type,
            'title' => $chart->title,
            'x_label' => $chart->x_column,
            'y_label' => $chart->y_column,
        ]);
    }

    // Data untuk dashboard aggregate
    public function dashboardData()
{
    $datasets = Dataset::where('user_id', auth()->id())->latest()->get();
    $result = [];

    foreach ($datasets as $dataset) {
        $fullPath = storage_path('app/private/' . $dataset->file_path);
        if (!file_exists($fullPath)) continue;

        $csv = Reader::createFromPath($fullPath, 'r');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();
        $records = iterator_to_array($csv->getRecords());

        $result[] = [
            'dataset_id' => $dataset->id,
            'dataset_name' => $dataset->name,
            'file_name' => $dataset->file_name,
            'row_count' => $dataset->row_count,
            'col_count' => $dataset->column_count,
            'uploaded_at' => $dataset->created_at->format('d M Y'),
            'headers' => $headers,
            'records' => array_slice($records, 0, 100),
        ];
    }

    return response()->json($result);
}
}