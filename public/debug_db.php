<?php
// File ini untuk debug - taruh di root Laravel dan akses via browser
// Hapus setelah debug

$dbPath = __DIR__ . '/database/ocean_plastic_dw.db';
echo "DB Path: $dbPath\n";
echo "Exists: " . (file_exists($dbPath) ? 'YES' : 'NO') . "\n";
echo "Readable: " . (is_readable($dbPath) ? 'YES' : 'NO') . "\n";
echo "Size: " . (file_exists($dbPath) ? filesize($dbPath) . ' bytes' : 'N/A') . "\n";

if (file_exists($dbPath)) {
    $pdo = new PDO('sqlite:' . $dbPath);
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "\n";
    
    $cnt = $pdo->query("SELECT COUNT(*) FROM fact_ocean_pollution")->fetchColumn();
    echo "fact_ocean_pollution rows: $cnt\n";
}