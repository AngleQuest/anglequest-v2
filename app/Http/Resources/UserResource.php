<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class UserResource extends JsonResource
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
            'email' => (string)$this->email,
            'status' => (string)ucfirst($this->status),
            'role' => (string)ucfirst($this->role),
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
