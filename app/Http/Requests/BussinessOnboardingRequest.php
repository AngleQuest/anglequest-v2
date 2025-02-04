<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BussinessOnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_type' => 'required',
            'plan_id' => 'required|exists:plans,id',
            'sla_id' => 'required|exists:slas,id',
            'payment_method' => 'required',

        ];
    }
}
