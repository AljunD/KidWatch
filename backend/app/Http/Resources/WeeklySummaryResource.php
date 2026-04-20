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
