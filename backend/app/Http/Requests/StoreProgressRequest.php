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
