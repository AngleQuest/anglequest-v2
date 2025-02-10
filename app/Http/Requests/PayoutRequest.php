<?php

namespace App\Http\Requests;

use App\Models\Configuration;
use Illuminate\Foundation\Http\FormRequest;

class PayoutRequest extends FormRequest
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
        $config = Configuration::first();
        $currSymbol = $config->currency_symbol;
        $min = $config->withdrawal_min;
        $max = $config->withdrawal_max;
        return [
            'amount' => [
                'required',
                'numeric',
                'min:' . $min,
                'max:' . $max
            ],
            [
                'amount.min' => 'Minimum withdrawable amount is ' . $currSymbol . number_format($min),
                'amount.max' => 'Maximum withdrawable amount is ' . $currSymbol . number_format($max),
            ]
        ];
    }
}
