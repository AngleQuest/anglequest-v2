<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class IndividualResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'category' => (string)$this->category,
            'first_name' => (string)$this->first_name,
            'last_name' => (string)$this->last_name,
            'email' => (string)$this->email,
            'phone' => (string)$this->phone,
            'date_of_birth' => (string)$this->dob,
            'current_role' => (string)$this->current_role,
            'target_role' => (string)$this->target_role,
            'gender' => (string)$this->gender,
            'specialization' => (string)$this->specialization,
            'yrs_of_experience' => (string)$this->yrs_of_experience,
            'about' => (string)$this->about,
            'location' => (string)$this->location,
            'profile_photo' => (string)$this->profile_photo,
            'created_at' => $this->created_at->toDateString()
        ];
    }
}
