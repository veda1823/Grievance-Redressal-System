@extends('layouts.app')

@section('title', 'Staff Complaint Queue')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <p style="color: var(--primary); font-size: 0.8rem; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase;">Staff workspace</p>
    <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0.25rem 0;">Complaint queue</h1>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Review student complaints and move each case through its workflow.</p>
</div>

<div style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden;">
    @if($complaints->isEmpty())
        <div style="padding: 3rem 1.5rem; text-align: center; color: var(--text-muted);">
            <p style="font-weight: 600; color: var(--text-main);">No complaints have been submitted.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 680px;">
                <thead>
                    <tr style="background: var(--background); text-align: left;">
                        <th style="padding: 0.9rem 1.25rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Complaint</th>
                        <th style="padding: 0.9rem 1.25rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Student</th>
                        <th style="padding: 0.9rem 1.25rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Category</th>
                        <th style="padding: 0.9rem 1.25rem; font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted);">Status</th>
                        <th style="padding: 0.9rem 1.25rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($complaints as $complaint)
                        <tr style="border-top: 1px solid var(--border);">
                            <td style="padding: 1rem 1.25rem;">
                                <a href="{{ route('staff.complaints.show', $complaint) }}" style="font-weight: 600; color: var(--text-main); text-decoration: none;">{{ $complaint->title }}</a>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">{{ $complaint->created_at->format('M d, Y') }}</div>
                            </td>
                            <td style="padding: 1rem 1.25rem; font-size: 0.9rem;">{{ $complaint->user->name }}</td>
                            <td style="padding: 1rem 1.25rem; font-size: 0.9rem;">{{ $complaint->category->name }}</td>
                            <td style="padding: 1rem 1.25rem; font-size: 0.85rem; text-transform: capitalize;">{{ str_replace('_', ' ', $complaint->status) }}</td>
                            <td style="padding: 1rem 1.25rem; text-align: right;"><a href="{{ route('staff.complaints.show', $complaint) }}" class="btn btn-outline" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">Review</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection