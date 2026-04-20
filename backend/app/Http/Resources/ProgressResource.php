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
