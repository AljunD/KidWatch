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
