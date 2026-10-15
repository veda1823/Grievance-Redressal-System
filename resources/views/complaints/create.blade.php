@extends('layouts.app')

@section('title', 'Submit Complaint')

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('dashboard') }}" style="color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 600;">&larr; Back to dashboard</a>
        <h1 style="font-size: 1.75rem; font-weight: 700; margin-top: 0.75rem; margin-bottom: 0.25rem;">Submit a complaint</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Tell us what happened and we will route it to the right department.</p>
    </div>

    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.75rem; box-shadow: var(--shadow);">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <span>Please correct the highlighted fields and try again.</span>
            </div>
        @endif

        <form action="{{ route('complaints.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="title" class="form-label">Complaint title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" maxlength="255" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="7" class="form-control @error('description') is-invalid @enderror" minlength="10" required>{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="priority" class="form-label">Priority</label>
                <select id="priority" name="priority" class="form-control @error('priority') is-invalid @enderror" required>
                    @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('priority', 'medium') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="{{ route('dashboard') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit complaint</button>
            </div>
        </form>
    </div>
</div>
@endsection