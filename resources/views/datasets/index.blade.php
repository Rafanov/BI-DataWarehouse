@extends('layouts.app')
@section('title', 'Datasets')

@section('topbar-actions')
    <a href="{{ route('datasets.create') }}" class="btn btn-primary btn-sm">+ Upload Dataset</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="card-title">All Datasets</div>
        <span class="badge badge-blue">{{ $datasets->count() }} total</span>
    </div>
    @if($datasets->isEmpty())
        <div class="card-body text-muted" style="text-align:center; padding:48px 20px;">
            No datasets yet. Upload your first CSV file.
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>File</th>
                        <th>Rows</th>
                        <th>Columns</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($datasets as $dataset)
                    <tr>
                        <td style="font-family:'DM Sans',sans-serif; font-weight:500; color:var(--text-primary);">{{ $dataset->name }}</td>
                        <td class="text-muted">{{ $dataset->description ?? '—' }}</td>
                        <td>{{ $dataset->file_name }}</td>
                        <td>{{ number_format($dataset->row_count) }}</td>
                        <td>{{ $dataset->column_count }}</td>
                        <td>{{ $dataset->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('datasets.show', $dataset) }}" class="btn btn-secondary btn-sm">View</a>
                                <form method="POST" action="{{ route('datasets.destroy', $dataset) }}" onsubmit="return confirm('Delete this dataset?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection