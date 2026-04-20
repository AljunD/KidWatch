<?php

// =============================================
// E. Security Middleware – CheckStudentAccess
// app/Http/Middleware/CheckStudentAccess.php
// Prevents IDOR attacks for guardians. Uses student_guardian table + Guardian profile linkage.
// Register in Kernel.php under $routeMiddleware['check.student.access'] = CheckStudentAccess::class;
// Use on routes: ->middleware('check.student.access')
// =============================================

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Guardian;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentAccess
{
    /**
     * Handle an incoming request – guardian can only access students they are linked to via student_guardian.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Teachers & Admins bypass (they have full access per business rules)
        if (!$user || in_array($user->role, ['admin', 'teacher'])) {
            return $next($request);
        }

        // Only guardians are restricted
        if ($user->role !== 'guardian') {
            abort(403, 'Unauthorized role.');
        }

        // Extract student_id from route parameter (route model binding or query)
        $studentId = $request->route('student')?->id
            ?? $request->route('student_id')
            ?? $request->query('student_id')
            ?? $request->input('student_id');

        if (!$studentId) {
            return response()->json(['message' => 'Student identifier is required.'], 400);
        }

        $guardian = Guardian::where('user_id', $user->id)->first();

        if (!$guardian) {
            abort(403, 'Guardian profile not found.');
        }

        $hasAccess = \DB::table('student_guardian')
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardian->id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have permission to access this student\'s data. (IDOR prevented)');
        }

        return $next($request);
    }
}
