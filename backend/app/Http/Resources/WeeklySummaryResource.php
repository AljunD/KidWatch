<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeeklySummaryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'student_id'      => $this->student_id,
            'week_id'         => $this->week_id,
            'summary_text'    => $this->summary_text,
            'activities_text' => $this->activities_text,
            'trashed_at'      => $this->trashed_at,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,

            // ✅ Include week details when eager-loaded
            'week' => $this->whenLoaded('week', function () {
                return [
                    'id'          => $this->week->id,
                    'week_number' => $this->week->week_number,
                    'start_date'  => $this->week->start_date,
                    'end_date'    => $this->week->end_date,
                ];
            }),

            // ✅ Optionally include student info when eager-loaded
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id'          => $this->student->id,
                    'first_name'  => $this->student->first_name,
                    'middle_name' => $this->student->middle_name,
                    'last_name'   => $this->student->last_name,
                    'gender'      => $this->student->gender,
                ];
            }),
        ];
    }
}
