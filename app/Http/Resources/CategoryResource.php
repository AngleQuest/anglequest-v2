<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class CategoryResource extends JsonResource
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
            'name' => (string)$this->name,
            'specializations' => $this->specializations ? $this->specializations->map(function ($specialization): array {
                return [
                    'id' => $specialization?->id,
                    'specialization_category_id' => $specialization?->specialization_category_id,
                    'name' => $specialization?->name,
                ];
            })->toArray() : [],
        ];
    }
}
