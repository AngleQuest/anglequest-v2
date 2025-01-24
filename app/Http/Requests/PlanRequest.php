<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
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
            'title' => 'required|unique:plans,title',
            'number_of_users' => 'required',
            'price' => 'required',
            'duration' => 'required|in:monthly,yearly',
            'type' => 'required|in:individual,business',
            'note' => 'required',
            'features' => 'nullable|array',
        ];
    }
}
