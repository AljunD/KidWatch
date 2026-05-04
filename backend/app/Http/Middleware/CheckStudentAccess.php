<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Guardian;
use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // ✅ Allow admins and teachers full access
        if (!$user || in_array($user->role, ['admin', 'teacher'])) {
            return $next($request);
        }

        // ✅ Only guardians are restricted
        if ($user->role !== 'guardian') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized role.',
                'errors'  => ['Only guardians can access student data']
            ], 403);
        }

        // ✅ Resolve student ID from route or request
        $studentId = $request->route('student')?->id
            ?? $request->route('student_id')
            ?? $request->query('student_id')
            ?? $request->input('student_id');

        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Student identifier is required.'
            ], 400);
        }

        // ✅ Get guardian profile
        $guardian = Guardian::where('user_id', $user->id)->first();

        if (!$guardian) {
            return response()->json([
                'success' => false,
                'message' => 'Guardian profile not found.'
            ], 403);
        }

        // ✅ Check direct ownership via guardian_id column
        $student = Student::find($studentId);

        if (!$student || $student->guardian_id !== $guardian->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this student\'s data. (IDOR prevented)'
            ], 403);
        }

        return $next($request);
    }
}
