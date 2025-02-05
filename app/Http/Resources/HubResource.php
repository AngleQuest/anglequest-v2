<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class HubResource extends JsonResource
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
            'visibility' => (string)$this->visibility,
            'name' => (string)$this->name,
            'category' => (string)$this->category,
            'specialization' => (string)$this->specialization,
            'description' => (string)$this->description,
            'hub_goals' => (string)$this->hub_goals,
            'members' => (string)$this->members?,
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
