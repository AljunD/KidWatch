<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'guardian_id'    => $this->guardian_id,
            'first_name'     => $this->first_name,
            'middle_name'    => $this->middle_name,
            'last_name'      => $this->last_name,
            'gender'         => $this->gender,
            'date_of_birth'  => $this->date_of_birth,
            'nationality'    => $this->nationality,
            'religion'       => $this->religion,
            'photo_path'     => $this->photo_path
                                ? asset('storage/' . $this->photo_path)
                                : null,

            'trashed_at'     => $this->trashed_at,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,

            // ✅ Guardian relationship
            'guardian' => $this->whenLoaded('guardian', function () {
                return [
                    'id'                    => $this->guardian->id,
                    'first_name'            => $this->guardian->first_name,
                    'middle_name'           => $this->guardian->middle_name,
                    'last_name'             => $this->guardian->last_name,
                    'relationship_to_child' => $this->guardian->relationship_to_child,
                    'contact_number'        => $this->guardian->contact_number,
                    'address'               => $this->guardian->address,
                    'email'                 => $this->guardian->user?->email,
                ];
            }),
            'progress_records' => $this->whenLoaded('progressRecords', function () {
                return ProgressResource::collection($this->progressRecords);
            }),
            'weekly_summaries' => $this->whenLoaded('weeklySummaries', function () {
                return WeeklySummaryResource::collection($this->weeklySummaries);
            }),
        ];
    }
}
