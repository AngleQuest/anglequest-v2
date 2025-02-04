<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class PaymentHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => (string)$this->type,
            'payment_id' => (string)$this->payment_id,
            'plan' => (string)$this->plan?->title,
            'plan_start' => $this->plan_start,
            'plan_end' => $this->plan_end,
            'amount' => (float)$this->amount,
            'method' => (string)$this->method,
            'payment_type' => (string)$this->payment_type,
            'status' => (string)$this->status,
        ];
    }
}
