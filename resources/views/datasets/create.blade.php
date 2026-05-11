@extends('layouts.app')
@section('title', 'Upload Dataset')

@section('content')
<div style="max-width: 560px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Upload New Dataset</div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('datasets.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Dataset Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Sales Data Q1 2024" value="{{ old('name') }}" required>
                    @error('name')<div class="form-hint" style="color:var(--danger)">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description <span class="text-muted">(optional)</span></label>
                    <textarea name="description" class="form-control" placeholder="What is this dataset about?">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">CSV File</label>
                    <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                    <div class="form-hint">Supported: .csv — Max 10MB</div>
                    @error('file')<div class="form-hint" style="color:var(--danger)">{{ $message }}</div>@enderror
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="submit" class="btn btn-primary">Upload Dataset</button>
                    <a href="{{ route('datasets.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection