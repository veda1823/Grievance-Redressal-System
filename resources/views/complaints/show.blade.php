@extends('layouts.app')

@section('title', 'Complaint Details')

@section('content')
<div style="max-width: 820px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('dashboard') }}" style="color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 600;">&larr; Back to dashboard</a>
        <h1 class="mobile-heading" style="font-size: 1.75rem; font-weight: 700; margin-top: 0.75rem; margin-bottom: 0.25rem;">{{ $complaint->title }}</h1>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Submitted on {{ $complaint->created_at->format('M d, Y') }}</p>
    </div>

    <div class="mobile-card-padding" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);">
        <div class="mobile-single-column" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Category</span><span>{{ $complaint->category->name }}</span></div>
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Priority</span><span style="text-transform: capitalize;">{{ $complaint->priority }}</span></div>
            <div><span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Status</span><span style="text-transform: capitalize;">{{ str_replace('_', ' ', $complaint->status) }}</span></div>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 1.25rem;">
            <h2 style="font-size: 1rem; margin-bottom: 0.5rem;">Description</h2>
            <p style="white-space: pre-wrap; color: var(--text-muted);">{{ $complaint->description }}</p>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-top: 1.5rem;">
            <h2 style="font-size: 1rem; margin-bottom: 1rem;">Responses</h2>
            @forelse($complaint->responses as $response)
                <div style="border-left: 3px solid var(--primary); padding: 0.1rem 0 0.1rem 1rem; margin-bottom: 1rem;">
                    <p style="white-space: pre-wrap; margin-bottom: 0.35rem;">{{ $response->comment }}</p>
                    <small style="color: var(--text-muted);">{{ $response->staff->name }} &bull; {{ $response->created_at->format('M d, Y g:i A') }}</small>
                </div>
            @empty
                <p style="color: var(--text-muted);">No staff responses yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection