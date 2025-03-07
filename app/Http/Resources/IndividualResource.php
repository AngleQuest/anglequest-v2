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
            'name' => (string)$this->name,
            'email' => (string)$this->email,
            'phone' => (string)$this->phone,
            'current_role' => (string)$this->current_role,
            'target_role' => (string)$this->target_role,
            'specialization' => (string)$this->specialization,
            'profile_photo' => (string)$this->profile_photo,
            'created_at' => $this->created_at->toDateString()
        ];
    }
}
