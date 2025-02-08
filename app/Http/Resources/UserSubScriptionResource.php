<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class UserSubScriptionResource extends JsonResource
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
            'plan_start' => (string)$this->plan_start,
            'plan_end' => (string)$this->plan_end,
            'amount' => (float)$this->amount,
            'status' => (string)$this->status,
            "plan" => (object) [
                'name' => $this?->plan?->title,
                'users' => $this?->plan?->number_of_users,
                'price' => $this?->plan?->price,
                'duration' => $this?->plan?->duration,
            ],
        ];
    }
}
