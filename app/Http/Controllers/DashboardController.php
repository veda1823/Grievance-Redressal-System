<?php

namespace App\Http\Controllers;

use App\Models\Complaint;

class DashboardController extends Controller
{
    /**
     * Show the student dashboard with complaint statistics and recent complaints.
     */
    public function index()
    {
        $user = auth()->user();

        // Get total count of complaints by this student
        $totalComplaints = Complaint::where('user_id', $user->id)->count();

        // Get counts by status for the summary cards
        $pendingCount = Complaint::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $inProgressCount = Complaint::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();

        $resolvedCount = Complaint::where('user_id', $user->id)
            ->where('status', 'resolved')
            ->count();

        // Get the 5 most recent complaints with their category loaded
        $recentComplaints = Complaint::where('user_id', $user->id)
            ->with('category')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalComplaints',
            'pendingCount',
            'inProgressCount',
            'resolvedCount',
            'recentComplaints'
        ));
    }
}
