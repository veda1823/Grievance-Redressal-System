<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Show the form for submitting a complaint.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('complaints.create', compact('categories'));
    }

    /**
     * Store a newly submitted complaint for the authenticated student.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        Complaint::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Your complaint has been submitted successfully.');
    }

    /**
     * Show all complaints to authenticated staff members.
     */
    public function staffIndex(): View
    {
        $query = Complaint::with(['user', 'category']);

        if ($search = request('search')) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn (Builder $userQuery) => $userQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('priority')) {
            $query->where('priority', request('priority'));
        }

        if (request('verification') === 'verified') {
            $query->whereNotNull('verified_at');
        } elseif (request('verification') === 'unverified') {
            $query->whereNull('verified_at');
        }

        $complaints = $query->latest()->get();
        $stats = [
            'total' => Complaint::count(),
            'pending' => Complaint::where('status', 'pending')->count(),
            'in_progress' => Complaint::where('status', 'in_progress')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'rejected' => Complaint::where('status', 'rejected')->count(),
            'unverified' => Complaint::whereNull('verified_at')->count(),
        ];

        return view('staff.complaints.index', compact('complaints', 'stats'));
    }

    /**
     * Show a complaint to authenticated staff members.
     */
    public function staffShow(Complaint $complaint): View
    {
        $complaint->load(['user', 'category', 'responses.staff', 'verifier']);

        return view('staff.complaints.show', compact('complaint'));
    }

    /**
     * Show a complaint and its responses to its student owner.
     */
    public function studentShow(Complaint $complaint): View
    {
        abort_unless($complaint->user_id === auth()->id(), 403);

        $complaint->load(['category', 'responses.staff']);

        return view('complaints.show', compact('complaint'));
    }

    /**
     * Update a complaint's workflow status.
     */
    public function updateStatus(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,resolved,rejected'],
            'rejection_reason' => ['nullable', 'string', 'min:5', 'max:2000', 'required_if:status,rejected'],
        ]);

        $complaint->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['status'] === 'rejected'
                ? $validated['rejection_reason']
                : null,
        ]);

        return redirect()->route('staff.complaints.show', $complaint)
            ->with('success', 'Complaint status updated successfully.');
    }

    public function verify(Request $request, Complaint $complaint): RedirectResponse
    {
        $complaint->update([
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        return redirect()->route('staff.complaints.show', $complaint)
            ->with('success', 'Complaint verified successfully.');
    }

    /**
     * Add a response from the authenticated staff member.
     */
    public function storeResponse(Request $request, Complaint $complaint): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        ComplaintResponse::create([
            'complaint_id' => $complaint->id,
            'staff_id' => $request->user()->id,
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('staff.complaints.show', $complaint)
            ->with('success', 'Response added successfully.');
    }

    
/**
 * Show complaints submitted by the authenticated student.
 */
public function index(): View
{
    $complaints = Complaint::where('user_id', auth()->id())
        ->with('category')
        ->latest()
        ->get();

    return view('dashboard', [
        'totalComplaints' => $complaints->count(),
        'pendingCount' => $complaints->where('status', 'pending')->count(),
        'inProgressCount' => $complaints->where('status', 'in_progress')->count(),
        'resolvedCount' => $complaints->where('status', 'resolved')->count(),
        'recentComplaints' => $complaints->take(5),
    ]);
}

}