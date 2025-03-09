<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'name' => 'required',
            'gender' => 'required|in:Male,Female',
            'email' => ['required', 'email','email:rfc,dns'],
            'phone' => 'required',
            'current_role' => 'required',
            'target_role' => 'required',
            'specialization' => 'required',
            'yrs_of_experience' => 'required',
        ];
    }
}
