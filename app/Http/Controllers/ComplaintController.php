<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $complaints = Complaint::with(['user', 'category'])
            ->latest()
            ->get();

        return view('staff.complaints.index', compact('complaints'));
    }

    /**
     * Show a complaint to authenticated staff members.
     */
    public function staffShow(Complaint $complaint): View
    {
        $complaint->load(['user', 'category', 'responses.staff']);

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
        ]);

        $complaint->update(['status' => $validated['status']]);

        return redirect()->route('staff.complaints.show', $complaint)
            ->with('success', 'Complaint status updated successfully.');
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
public function index(Request $request): View
{
    $validated = $request->validate([
        'status' => ['nullable', 'in:pending,in_progress,resolved,rejected'],
        'search' => ['nullable', 'string', 'max:255'],
    ]);

    $complaints = Complaint::where('user_id', $request->user()->id)
        ->with('category')
        ->when($validated['status'] ?? null, function ($query, string $status) {
            $query->where('status', $status);
        })
        ->when($validated['search'] ?? null, function ($query, string $search) {
            $query->where('title', 'like', '%' . $search . '%');
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('complaints.index', compact('complaints'));
}

}