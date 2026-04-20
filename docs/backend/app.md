<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user = auth()->user();
        if (!$user->is_active) {
            auth()->logout();
            return $this->errorResponse('Account is inactive', 403);
        }

        $token = $user->createToken('kidwatch-api-token', ['role:' . $user->role])->plainTextToken;

        return $this->successResponse([
            'user' => ['id' => $user->id, 'email' => $user->email, 'role' => $user->role],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function registerGuardian(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'relationship_to_child' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'contact_number' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $user = \App\Models\User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'guardian',
            'is_active' => true,
        ]);

        \App\Models\Guardian::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'relationship_to_child' => $request->relationship_to_child,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
        ]);

        $token = $user->createToken('kidwatch-guardian-token')->plainTextToken;

        return $this->successResponse([
            'user' => ['id' => $user->id, 'email' => $user->email, 'role' => 'guardian'],
            'token' => $token,
        ], 'Guardian registered successfully', 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'Logged out successfully');
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgressRequest;
use App\Http\Resources\ProgressResource;
use App\Models\Student;
use App\Models\ProgressRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProgressController extends Controller
{
    public function index(Student $student, Request $request): JsonResponse
    {
        $week = $request->query('week');
        $query = $student->progressRecords();

        if ($week) {
            $query->where('week_number', $week);
        }

        $progress = $query->paginate(10);
        return $this->successResponse(ProgressResource::collection($progress));
    }

    public function store(StoreProgressRequest $request, Student $student): JsonResponse
    {
        $data = $request->validated();
        $data['student_id'] = $student->id;

        $record = ProgressRecord::create($data);

        Cache::forget("student_summary_{$student->id}_week_{$record->week_number}");

        return $this->successResponse(new ProgressResource($record), 'Progress recorded', 201);
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecommendationResource;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    public function index(): JsonResponse
    {
        $configs = \App\Models\RecommendationEngineConfig::all();
        return $this->successResponse(RecommendationResource::collection($configs));
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $students = Student::with('guardians')->paginate(15);
        return $this->successResponse(StudentResource::collection($students));
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = Student::create($request->validated());
        return $this->successResponse(new StudentResource($student), 'Student created', 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load('guardians', 'progressRecords');
        return $this->successResponse(new StudentResource($student));
    }

    public function update(StoreStudentRequest $request, Student $student): JsonResponse
    {
        $student->update($request->validated());
        return $this->successResponse(new StudentResource($student), 'Student updated');
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();
        return $this->successResponse(null, 'Student soft-deleted', 204);
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WeeklySummaryResource;
use App\Models\Student;
use App\Models\WeeklySummary;
use App\Services\SummaryGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SummaryController extends Controller
{
    public function __construct(private readonly SummaryGeneratorService $summaryGenerator) {}

    public function show(Student $student, int $week): JsonResponse
    {
        $cacheKey = "student_summary_{$student->id}_week_{$week}";

        $summary = Cache::remember($cacheKey, 3600, function () use ($student, $week) {
            return WeeklySummary::firstOrCreate(
                ['student_id' => $student->id, 'week_number' => $week],
                ['summary_text' => $this->summaryGenerator->generate($student, $week)]
            );
        });

        return $this->successResponse(new WeeklySummaryResource($summary));
    }

    public function regenerate(Student $student, int $week): JsonResponse
    {
        $summaryText = $this->summaryGenerator->generate($student, $week);

        $summary = WeeklySummary::updateOrCreate(
            ['student_id' => $student->id, 'week_number' => $week],
            ['summary_text' => $summaryText]
        );

        Cache::forget("student_summary_{$student->id}_week_{$week}");

        return $this->successResponse(new WeeklySummaryResource($summary), 'Summary regenerated');
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // RBAC handled by middleware/policy
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'nationality' => 'required|string|max:100',
            'religion' => 'required|string|max:100',
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'week_number' => 'required|integer|min:1|max:52',
            'subject' => 'required|in:Math,Science,English,Filipino',
            'rating' => 'required|in:Poor,Good,Very Good,Excellent',
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => trim("{$this->first_name} {$this->middle_name} {$this->last_name}"),
            'gender' => $this->gender,
            'age' => $this->age,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'nationality' => $this->nationality,
            'religion' => $this->religion,
            'guardians' => $this->whenLoaded('guardians', fn() => $this->guardians->pluck('full_name')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'week_number' => $this->week_number,
            'subject' => $this->subject,
            'rating' => $this->rating,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeeklySummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'student_id' => $this->student_id,
            'week_number' => $this->week_number,
            'summary_text' => $this->summary_text,
            'generated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecommendationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'subject' => $this->subject,
            'rating' => $this->rating,
            'intervention_text' => $this->intervention_text,
        ];
    }
}
<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['admin', 'teacher'])) {
            return true;
        }
        return null;
    }

    public function view(User $user, Student $student): bool
    {
        return true; // middleware already validated
    }

    public function update(User $user, Student $student): bool
    {
        return true;
    }

    public function delete(User $user, Student $student): bool
    {
        return true;
    }
}
<?php

namespace App\Providers;

use App\Models\Student;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Student::class => StudentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // You can also define Gates here if needed
        // Gate::define('something', fn(User $user) => ...);
    }
}
