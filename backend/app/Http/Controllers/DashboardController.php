<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with key metrics.
     */
    public function index(): View
    {
        // Student metrics
        $studentsCount = Student::count();

        // Faculty metrics
        $teachersCount = User::where('role', 'teacher')->count();


        // Pass data to the view
        return view('dashboard', [
            'studentsCount'     => $studentsCount,
            'teachersCount'     => $teachersCount,
        ]);
    }
}
