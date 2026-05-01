<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Week;
use App\Models\ProgressRecord;
use App\Models\WeeklySummary;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with live metrics.
     */
    public function index(): View
    {
        $user = Auth::user();
        $role = $user->role;

        // ✅ Student counts (exclude trashed)
        $totalStudents = $role === 'teacher'
            ? Student::whereNull('trashed_at')
                ->whereHas('guardian', fn($q) => $q->whereNull('trashed_at'))
                ->count()
            : $user->guardian?->students()
                ->whereNull('trashed_at')
                ->count() ?? 0;

        // Weeks tracked
        $weeksTracked = Week::count();

        // Current week info (latest by week_number)
        $currentWeek = Week::orderByDesc('week_number')->first();

        // ✅ Average rating across active progress records only
        $avgRating = ProgressRecord::whereNull('trashed_at')->count() > 0
            ? ProgressRecord::whereNull('trashed_at')->avg('rating_level')
            : 0;

        // Previous week average rating
        $previousWeek = Week::orderByDesc('week_number')->skip(1)->first();
        $previousAvgRating = $previousWeek
            ? ProgressRecord::where('week_id', $previousWeek->id)
                ->whereNull('trashed_at')
                ->avg('rating_level')
            : null;

        // ✅ Recent progress records (exclude trashed)
        $progressRecords = ProgressRecord::whereNull('trashed_at')
            ->with(['student' => function ($q) {
                $q->whereNull('trashed_at')
                  ->whereHas('guardian', fn($g) => $g->whereNull('trashed_at'));
            }, 'week'])
            ->orderByDesc('id')
            ->take(10)
            ->get();

        // Latest weekly summary (by ID, not created_at) — filter if you also added trashed_at to summaries
        $weeklySummary = WeeklySummary::whereNull('trashed_at')->orderByDesc('id')->first();

        // ✅ Chart data (last 4 weeks, exclude trashed records)
        $recentWeeks = Week::orderByDesc('week_number')->take(4)->pluck('id')->toArray();
        $chartData = ProgressRecord::selectRaw('week_id, subject, AVG(rating_level) as avg_rating')
            ->whereIn('week_id', $recentWeeks)
            ->whereNull('trashed_at')
            ->groupBy('week_id','subject')
            ->orderBy('week_id')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'role' => $role,
            'totalStudents' => $totalStudents,
            'weeksTracked' => $weeksTracked,
            'avgRating' => $avgRating,
            'previousAvgRating' => $previousAvgRating,

            // Current week info
            'currentWeek' => $currentWeek?->week_number ?? null,
            'currentWeekStart' => $currentWeek?->start_date ? Carbon::parse($currentWeek->start_date)->format('M d, Y') : null,
            'currentWeekEnd'   => $currentWeek?->end_date ? Carbon::parse($currentWeek->end_date)->format('M d, Y') : null,

            'progressRecords' => $progressRecords,
            'weeklySummary' => $weeklySummary,
            'chartData' => $chartData,
        ]);
    }
}
