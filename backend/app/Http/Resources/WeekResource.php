<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeekResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'week_number' => $this->week_number,
            'start_date'  => $this->start_date,
            'end_date'    => $this->end_date,
        ];
    }
}
