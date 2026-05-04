<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'student_id'    => $this->student_id,
            'week_id'       => $this->week_id,
            'subject'       => $this->subject,        // e.g. Math, Science, English
            'rating_level'  => $this->rating_level,   // numeric tinyint from DB
            'rating_label'  => $this->rating_label,   // accessor from ProgressRecord
            'remarks'       => $this->remarks,
            'trashed_at'    => $this->trashed_at,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,

            // ✅ Include week details when eager-loaded
            'week' => $this->whenLoaded('week', function () {
                return [
                    'id'          => $this->week->id,
                    'week_number' => $this->week->week_number,
                    'start_date'  => $this->week->start_date,
                    'end_date'    => $this->week->end_date,
                ];
            }),
        ];
    }
}
