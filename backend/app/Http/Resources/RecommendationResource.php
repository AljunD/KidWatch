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
