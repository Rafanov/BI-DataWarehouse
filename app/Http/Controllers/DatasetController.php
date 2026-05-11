<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Dataset;
use Illuminate\Http\Request;
use League\Csv\Reader;

class DatasetController extends Controller
{
    public function index()
    {
        $datasets = Dataset::where('user_id', auth()->id())->latest()->get();
        return view('datasets.index', compact('datasets'));
    }

    public function create()
    {
        return view('datasets.create');
    }

    public function store(Request $request)
    {
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'file' => 'required|file|mimes:csv,txt|max:10240',
    ]);

    $file = $request->file('file');
    
    // Debug dulu
    if (!$file || !$file->isValid()) {
        return back()->with('error', 'File tidak valid: ' . $file?->getErrorMessage());
    }

    $originalName = $file->getClientOriginalName();
    $fileName = time() . '_' . ($originalName ?: 'upload.csv');
    $uploadPath = storage_path('app/private/uploads');
    
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    $file->move($uploadPath, $fileName);
    $filePath = 'uploads/' . $fileName;

    // Parse CSV
    $csv = Reader::createFromPath($uploadPath . '/' . $fileName, 'r');
    $csv->setHeaderOffset(0);
    $rows = iterator_to_array($csv->getRecords());
    $rowCount = count($rows);
    $colCount = count($csv->getHeader());

    Dataset::create([
        'user_id' => auth()->id(),
        'name' => $request->name,
        'description' => $request->description,
        'file_path' => $filePath,
        'file_name' => $originalName ?: $fileName,
        'row_count' => $rowCount,
        'column_count' => $colCount,
    ]);

    return redirect()->route('datasets.index')->with('success', 'Dataset berhasil diupload!');
    }

    public function show(Dataset $dataset)
    {
    $fullPath = storage_path('app/private/' . $dataset->file_path);
    
    $csv = Reader::createFromPath($fullPath, 'r');
    $csv->setHeaderOffset(0);
    $headers = $csv->getHeader();
    $records = iterator_to_array($csv->getRecords());
    $rows = array_slice($records, 0, 100);

    return view('datasets.show', compact('dataset', 'headers', 'rows'));
    }

    public function destroy(Dataset $dataset)
    {
        \Storage::delete($dataset->file_path);
        $dataset->delete();
        return redirect()->route('datasets.index')->with('success', 'Dataset dihapus!');
    }

    public function generateInsight(Dataset $dataset)
{
    // Kalau udah ada cache, return langsung
    if ($dataset->ai_insight && $dataset->ai_chart_config) {
        return response()->json([
            'insight' => $dataset->ai_insight,
            'chart_config' => json_decode($dataset->ai_chart_config, true),
        ]);
    }

    // Baca sample data dari CSV
    $fullPath = storage_path('app/private/' . $dataset->file_path);
    $csv = Reader::createFromPath($fullPath, 'r');
    $csv->setHeaderOffset(0);
    $headers = $csv->getHeader();
    $records = iterator_to_array($csv->getRecords());
    $sample = array_slice($records, 0, 10);

    // Kirim ke Gemini
    $apiKey = env('GEMINI_API_KEY');
    $prompt = "Kamu adalah analis data profesional. Analisis dataset berikut dan kembalikan response HANYA dalam format JSON valid tanpa markdown, tanpa backtick, tanpa penjelasan lain.

Dataset: {$dataset->name}
Deskripsi: {$dataset->description}
Kolom: " . implode(', ', $headers) . "
Sample data (10 baris): " . json_encode($sample) . "
Total baris: {$dataset->row_count}

Return JSON dengan struktur PERSIS seperti ini:
{
  \"insight\": \"Paragraf insight dalam bahasa Indonesia yang mudah dipahami orang awam. Jelaskan pola, tren, dan temuan menarik dari data ini.\",
  \"summary_stats\": [
    {\"label\": \"Label statistik\", \"value\": \"Nilai\", \"description\": \"Penjelasan singkat\"}
  ],
  \"charts\": [
    {
      \"type\": \"bar\",
      \"title\": \"Judul chart\",
      \"description\": \"Kenapa chart ini dipilih\",
      \"x_column\": \"nama_kolom_untuk_sumbu_x\",
      \"y_column\": \"nama_kolom_untuk_sumbu_y\",
      \"x_label\": \"Label sumbu X\",
      \"y_label\": \"Label sumbu Y\",
      \"limit\": 10
    }
  ]
}

Aturan:
- summary_stats berisi 3-4 statistik kunci dari data
- charts berisi 2-3 chart yang paling relevan dan informatif
- type chart hanya boleh: bar, line, pie, doughnut
- x_column dan y_column HARUS nama kolom yang ada di dataset
- Untuk pie/doughnut, gunakan x_column untuk label dan y_column untuk nilai
- Pilih chart yang benar-benar memberikan insight bermakna dari data ini";

    $response = Http::withHeaders(['Content-Type' => 'application/json'])
        ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

    $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;

    if (!$text) {
        return response()->json(['error' => 'Gagal generate insight'], 500);
    }

    // Clean JSON kalau ada backtick
    $text = preg_replace('/```json|```/i', '', $text);
    $text = trim($text);
    $parsed = json_decode($text, true);

    if (!$parsed) {
        return response()->json(['error' => 'Response AI tidak valid'], 500);
    }

    // Cache ke DB
    $dataset->update([
        'ai_insight' => $parsed['insight'],
        'ai_chart_config' => json_encode($parsed),
    ]);

    return response()->json([
        'insight' => $parsed['insight'],
        'chart_config' => $parsed,
    ]);
    }
    
}