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
