<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    /**
     * Display a listing of the system logs.
     */
    public function index()
    {
        // ✅ Fetch logs with user relationship for accountability
        $logs = Log::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('logs', compact('logs'));
    }
}
