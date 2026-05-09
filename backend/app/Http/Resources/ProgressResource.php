<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Week;

class ProgressResource extends JsonResource
{
    public function toArray($request)
    {
        $latestWeek = Week::orderBy('week_number', 'desc')->first();
        $status = 'Completed';

        if ($latestWeek && $this->week_id === $latestWeek->id) {
            $status = 'Current Week';
        }

        return [
            'id'            => $this->id,
            'student_id'    => $this->student_id,
            'week_id'       => $this->week_id,
            'subject'       => $this->subject,
            'rating_level'  => $this->rating_level,
            'rating_label'  => $this->rating_label,
            'remarks'       => $this->remarks,
            'trashed_at'    => $this->trashed_at,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'week' => $this->whenLoaded('week', function () {
                return [
                    'id'          => $this->week->id,
                    'week_number' => $this->week->week_number,
                    'start_date'  => $this->week->start_date,
                    'end_date'    => $this->week->end_date,
                ];
            }),
            'status' => $status,
        ];
    }
}
