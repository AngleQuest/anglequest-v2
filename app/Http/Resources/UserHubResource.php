<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class UserHubResource extends JsonResource
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
            'first_name' => (string)$this->user->profile?->first_name,
            'last_name' => (string)$this->user->profile?->last_name,
            'current_role' => (string)$this->user->profile?->current_role,
            'target_role' => (string)$this->user->profile?->target_role,
            'phone' => (string)$this->user->profile?->phone,
        ];
    }
}
