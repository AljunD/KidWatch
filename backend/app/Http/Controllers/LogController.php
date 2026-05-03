<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('logs', compact('logs'));
    }
}
