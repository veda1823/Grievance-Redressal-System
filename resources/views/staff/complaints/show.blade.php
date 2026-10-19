@extends('layouts.app')

@section('title', 'Review Complaint')

@section('content')
<div class="staff-shell" style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('staff.dashboard') }}" style="color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 600;">&larr; Back to complaint queue</a>
        <div style="display: flex; justify-content: space-between; gap: 1rem; align-items: start; flex-wrap: wrap; margin-top: 0.75rem;">
            <div>
                <p class="staff-kicker">Complaint #{{ $complaint->id }}</p>
                <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $complaint->title }}</h1>
            </div>
            @if($complaint->verified_at)
                <span class="verification-badge">Verified {{ $complaint->verified_at->format('M d, Y') }}</span>
            @else
                <span style="color: var(--text-muted); font-size: 0.85rem;">Awaiting verification</span>
            @endif
        </div>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Submitted by {{ $complaint->user->name }} on {{ $complaint->created_at->format('M d, Y') }}</p>
    </div>

    <div class="staff-card" style="padding: 1.5rem;">
        <div class="staff-detail-grid" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Category</span><span>{{ $complaint->category->name }}</span></div>
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Priority</span><span style="text-transform: capitalize;">{{ $complaint->priority }}</span></div>
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Student email</span><span style="word-break: break-word;">{{ $complaint->user->email }}</span></div>
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Current status</span><span style="text-transform: capitalize;">{{ str_replace('_', ' ', $complaint->status) }}</span></div>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1rem; margin-bottom: 0.5rem;">Description</h2>
            <p style="white-space: pre-wrap; color: var(--text-muted);">{{ $complaint->description }}</p>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 1.25rem;">
            <div style="display: flex; justify-content: space-between; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 1rem;">
                <div>
                    <h2 style="font-size: 1rem; margin-bottom: 0.2rem;">Verification</h2>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">Confirm that this complaint has been reviewed before processing it.</p>
                </div>
                @unless($complaint->verified_at)
                    <form action="{{ route('staff.complaints.verify', $complaint) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary">Mark as verified</button>
                    </form>
                @endunless
            </div>
        </div>

        <form action="{{ route('staff.complaints.status', $complaint) }}" method="POST" style="border-top: 1px solid var(--border); padding-top: 1.25rem;">
            @csrf
            @method('PATCH')
            <label for="status" class="form-label">Update status</label>
            <div class="staff-detail-grid" style="display: grid; grid-template-columns: 1fr 2fr auto; gap: 0.75rem; align-items: start;">
                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                    @foreach(['pending' => 'Pending', 'in_progress' => 'In progress', 'resolved' => 'Resolved', 'rejected' => 'Rejected'] as $value => $label)
                        <option value="{{ $value }}" @selected($complaint->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <input id="rejection_reason" name="rejection_reason" value="{{ old('rejection_reason', $complaint->rejection_reason) }}" class="form-control @error('rejection_reason') is-invalid @enderror" placeholder="Rejection reason (required when rejected)">
                <button type="submit" class="btn btn-primary">Save status</button>
            </div>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if($complaint->status === 'rejected' && $complaint->rejection_reason)
                <p style="color: var(--danger); font-size: 0.85rem; margin-top: 0.65rem;"><strong>Rejection reason:</strong> {{ $complaint->rejection_reason }}</p>
            @endif
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