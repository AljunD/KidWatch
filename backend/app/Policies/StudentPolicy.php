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
