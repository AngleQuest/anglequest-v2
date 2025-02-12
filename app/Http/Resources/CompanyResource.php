<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Double;

class CompanyResource extends JsonResource
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
            'administrator_name' => (string)$this->administrator_name,
            'business_email' => (string)$this->business_email,
            'address' => (string)$this->address,
            'nda_file' => (string)$this->nda_file,
            'company_logo' => (string)$this->company_logo,
            'business_reg_number' => (string)$this->business_reg_number,
            'business_phone' => (string)$this->business_phone,
            'company_size' => (string)$this->company_size,
            'website' => (string)$this->website,
            'about' => (string)$this->about,
            'service_type' => (string)$this->service_type,
            'country' => (string)$this->country,
            'city' => (string)$this->city,
            'state' => (string)$this->state,
            'created_at' => $this->created_at->toDateString(),
            'employees_count' => (int)$this->employees?->count(),
        ];
    }
}
