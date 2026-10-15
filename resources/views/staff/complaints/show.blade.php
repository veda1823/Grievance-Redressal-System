@extends('layouts.app')

@section('title', 'Review Complaint')

@section('content')
<div style="max-width: 820px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('staff.dashboard') }}" style="color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 600;">&larr; Back to complaint queue</a>
        <h1 style="font-size: 1.75rem; font-weight: 700; margin-top: 0.75rem; margin-bottom: 0.25rem;">{{ $complaint->title }}</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Submitted by {{ $complaint->user->name }} on {{ $complaint->created_at->format('M d, Y') }}</p>
    </div>

    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);">
        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Category</span><span>{{ $complaint->category->name }}</span></div>
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Priority</span><span style="text-transform: capitalize;">{{ $complaint->priority }}</span></div>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1rem; margin-bottom: 0.5rem;">Description</h2>
            <p style="white-space: pre-wrap; color: var(--text-muted);">{{ $complaint->description }}</p>
        </div>

        <form action="{{ route('staff.complaints.status', $complaint) }}" method="POST" style="border-top: 1px solid var(--border); padding-top: 1.25rem;">
            @csrf
            @method('PATCH')
            <label for="status" class="form-label">Update status</label>
            <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                    @foreach(['pending' => 'Pending', 'in_progress' => 'In progress', 'resolved' => 'Resolved', 'rejected' => 'Rejected'] as $value => $label)
                        <option value="{{ $value }}" @selected($complaint->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Save status</button>
            </div>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </form>

        <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-top: 1.5rem;">
            <h2 style="font-size: 1rem; margin-bottom: 1rem;">Response history</h2>
            @forelse($complaint->responses as $response)
                <div style="border-left: 3px solid var(--primary); padding: 0.1rem 0 0.1rem 1rem; margin-bottom: 1rem;">
                    <p style="white-space: pre-wrap; margin-bottom: 0.35rem;">{{ $response->comment }}</p>
                    <small style="color: var(--text-muted);">{{ $response->staff->name }} &bull; {{ $response->created_at->format('M d, Y g:i A') }}</small>
                </div>
            @empty
                <p style="color: var(--text-muted);">No responses yet.</p>
            @endforelse

            <form action="{{ route('staff.complaints.responses.store', $complaint) }}" method="POST" style="margin-top: 1.25rem;">
                @csrf
                <label for="comment" class="form-label">Add response</label>
                <textarea id="comment" name="comment" rows="4" class="form-control @error('comment') is-invalid @enderror" minlength="5" maxlength="5000" required>{{ old('comment') }}</textarea>
                @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <button type="submit" class="btn btn-primary" style="margin-top: 0.75rem;">Add response</button>
            </form>
        </div>
    </div>
</div>
@endsection