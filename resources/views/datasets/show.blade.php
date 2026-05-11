@extends('layouts.app')
@section('title', $dataset->name)

@section('topbar-actions')
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">← Dashboard</a>
    <form method="POST" action="{{ route('datasets.destroy', $dataset) }}" onsubmit="return confirm('Delete this dataset?')" style="display:inline;">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
    </form>
@endsection

@section('content')
{{-- Meta --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-label">Dataset Name</div>
        <div class="stat-value" style="font-size:16px;letter-spacing:0;">{{ $dataset->name }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Rows</div>
        <div class="stat-value">{{ number_format($dataset->row_count) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Columns</div>
        <div class="stat-value">{{ $dataset->column_count }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Uploaded</div>
        <div class="stat-value" style="font-size:15px;letter-spacing:0;">{{ $dataset->created_at->format('d M Y') }}</div>
    </div>
</div>

{{-- Column info --}}
<div class="card mb-6">
    <div class="card-header">
        <div class="card-title">Column Information</div>
        <span class="badge badge-blue">{{ count($headers) }} columns</span>
    </div>
    <div class="card-body">
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($headers as $header)
            <span style="padding:4px 12px;background:var(--bg);border:1px solid var(--border);border-radius:100px;font-size:12px;font-family:'DM Mono',monospace;color:var(--text-secondary);">
                {{ $header }}
            </span>
            @endforeach
        </div>
    </div>
</div>

{{-- Data table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Data Preview</div>
        <span class="badge badge-blue">Showing {{ count($rows) }} of {{ number_format($dataset->row_count) }} rows</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>@foreach($headers as $h)<th>{{ $h }}</th>@endforeach</tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                <tr>@foreach($headers as $h)<td>{{ $row[$h] ?? '—' }}</td>@endforeach</tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection